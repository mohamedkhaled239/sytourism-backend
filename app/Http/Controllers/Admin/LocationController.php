<?php

namespace App\Http\Controllers\Admin;

use App\Exports\LocationsTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\LocationsImport;
use App\Models\Category;
use App\Models\City;
use App\Models\Governorate;
use App\Models\Location;
use App\Models\LocationImage;
use App\Models\LocationType;
use App\Models\TourismType;
use App\Services\OneSignalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::with(['governorate', 'city', 'tourismType', 'categories', 'locationTypes'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.locations.index', compact('locations'));
    }

    public function create()
    {
        $admin = auth('admin')->user();
        $governorates = Governorate::active()
            ->when($admin->governorate_id, fn ($query) => $query->where('id', $admin->governorate_id))
            ->orderBy('name_ar')
            ->get();
        $cities = City::active()
            ->when($admin->governorate_id, fn ($query) => $query->where('governorate_id', $admin->governorate_id))
            ->orderBy('name_ar')
            ->get();
        $tourismTypes = TourismType::active()->get();
        $locationTypes = LocationType::active()->get();
        $categories = Category::where('type', 'locations')->active()->get();

        return view('admin.locations.create', compact('governorates', 'cities', 'tourismTypes', 'locationTypes', 'categories'));
    }

    public function store(Request $request)
    {
        $this->applyAdminGovernorateConstraint($request);
        $this->validateLocationRequest($request);

        $data = $request->except(['location_types', 'categories', 'main_image', 'images', 'image_captions', 'image_captions_ar']);

        if ($request->hasFile('main_image')) {
            $mainImagePath = $request->file('main_image')->store('locations/main', 'public');
            $data['main_image'] = $mainImagePath;
        }

        $location = Location::create($data);

        if ($request->has('location_types')) {
            $location->locationTypes()->attach($request->location_types);
        }

        if ($request->has('categories')) {
            $location->categories()->attach($request->categories);
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                if ($image->isValid()) {
                    $imagePath = $image->store('locations/gallery', 'public');

                    LocationImage::create([
                        'location_id' => $location->id,
                        'image_path' => $imagePath,
                        'caption' => $request->input("image_captions.{$index}"),
                        'caption_ar' => $request->input("image_captions_ar.{$index}"),
                        'order' => $index,
                    ]);
                }
            }
        }

        $oneSignalService = new OneSignalService();
        $oneSignalService->sendLocationNotification($location);

        return redirect()->route('admin.locations.index')
            ->with('success', 'تم إضافة الموقع بنجاح');
    }

    public function edit($id)
    {
        $admin = auth('admin')->user();
        $location = Location::with(['locationTypes', 'categories', 'images', 'events', 'investments'])->findOrFail($id);
        $governorates = Governorate::active()
            ->when($admin->governorate_id, fn ($query) => $query->where('id', $admin->governorate_id))
            ->orderBy('name_ar')
            ->get();
        $cities = City::active()
            ->when($admin->governorate_id, fn ($query) => $query->where('governorate_id', $admin->governorate_id))
            ->orderBy('name_ar')
            ->get();
        $tourismTypes = TourismType::active()->get();
        $locationTypes = LocationType::active()->get();
        $categories = Category::where('type', 'locations')->active()->get();

        return view('admin.locations.edit', compact('location', 'governorates', 'cities', 'tourismTypes', 'locationTypes', 'categories'));
    }

    public function show($id)
    {
        return redirect()->route('admin.locations.edit', $id);
    }

    public function update(Request $request, $id)
    {
        try {
            $location = Location::findOrFail($id);
            $this->applyAdminGovernorateConstraint($request);
            $this->validateLocationRequest($request);

            $data = $request->except(['location_types', 'categories', 'main_image', 'images', 'image_captions', 'image_captions_ar']);

            if ($request->hasFile('main_image') && $request->file('main_image')->isValid()) {
                try {
                    if ($location->main_image && Storage::disk('public')->exists($location->main_image)) {
                        Storage::disk('public')->delete($location->main_image);
                    }

                    $data['main_image'] = $request->file('main_image')->store('locations/main', 'public');
                } catch (\Exception $e) {
                    Log::error('Main image upload failed', ['error' => $e->getMessage()]);
                }
            }

            $location->update($data);

            $location->locationTypes()->sync($request->location_types ?? []);
            $location->categories()->sync($request->categories ?? []);

            if ($request->hasFile('images')) {
                try {
                    $maxOrder = $location->images()->max('order') ?? -1;

                    foreach ($request->file('images') as $index => $image) {
                        if ($image && $image->isValid()) {
                            $imagePath = $image->store('locations/gallery', 'public');

                            LocationImage::create([
                                'location_id' => $location->id,
                                'image_path' => $imagePath,
                                'caption' => $request->input("image_captions.{$index}"),
                                'caption_ar' => $request->input("image_captions_ar.{$index}"),
                                'order' => $maxOrder + $index + 1,
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('Multiple images upload failed', ['error' => $e->getMessage()]);
                }
            }

            Log::info('Location updated successfully', [
                'location_id' => $location->id,
                'location_name' => $location->name,
                'updated_by' => auth('admin')->user()->name ?? 'Unknown',
            ]);

            return redirect()->route('admin.locations.index')
                ->with('success', 'تم تحديث الموقع بنجاح');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Location validation failed', [
                'location_id' => $id,
                'errors' => $e->errors(),
                'input' => $request->except(['_token', 'main_image', 'images']),
            ]);

            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', 'يرجى تصحيح الأخطاء في النموذج');
        } catch (\Exception $e) {
            Log::error('Location update error', [
                'location_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تحديث الموقع. يرجى المحاولة مرة أخرى.')
                ->withInput();
        }
    }

    public function destroy($id)
    {
        $location = Location::findOrFail($id);

        if ($location->main_image) {
            Storage::disk('public')->delete($location->main_image);
        }

        foreach ($location->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $location->delete();

        return redirect()->route('admin.locations.index')
            ->with('success', 'تم حذف الموقع بنجاح');
    }

    public function deleteImage($locationId, $imageId)
    {
        $location = Location::findOrFail($locationId);
        $image = $location->images()->findOrFail($imageId);

        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return redirect()->back()->with('success', 'تم حذف الصورة بنجاح');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'max:10240', 'mimes:xlsx,xls,csv,txt', 'mimetypes:text/plain,text/csv,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
        ], [
            'file.required' => 'يرجى اختيار ملف للاستيراد',
            'file.mimes' => 'يجب أن يكون الملف من نوع Excel أو CSV',
            'file.mimetypes' => 'يجب أن يكون الملف من نوع Excel أو CSV',
            'file.max' => 'حجم الملف يجب أن يكون أقل من 10 ميجابايت',
        ]);

        try {
            $import = new LocationsImport();
            Excel::import($import, $request->file('file'));

            $errors = $import->getErrors();

            if (!empty($errors)) {
                return redirect()->route('admin.locations.index')
                    ->with('warning', 'تم استيراد المواقع مع بعض التحذيرات. يرجى مراجعة ملف السجلات للتفاصيل.')
                    ->with('import_errors', $errors);
            }

            return redirect()->route('admin.locations.index')
                ->with('success', 'تم استيراد المواقع بنجاح');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];

            foreach ($failures as $failure) {
                $errorMessages[] = "الصف {$failure->row()}: " . implode(', ', $failure->errors());
            }

            return redirect()->route('admin.locations.index')
                ->with('error', 'فشل في استيراد الملف بسبب أخطاء في البيانات')
                ->with('validation_errors', $errorMessages);
        } catch (\Exception $e) {
            Log::error('Import failed: ' . $e->getMessage(), [
                'file' => $request->file('file')->getClientOriginalName(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('admin.locations.index')
                ->with('error', 'حدث خطأ أثناء استيراد الملف: ' . $e->getMessage());
        }
    }

    public function template()
    {
        return Excel::download(new LocationsTemplateExport(), 'one_location_sample.xlsx');
    }

    public function export()
    {
        return Excel::download(new \App\Exports\LocationsExport(), 'locations_export.xlsx');
    }

    private function applyAdminGovernorateConstraint(Request $request): void
    {
        $admin = auth('admin')->user();

        if ($admin && $admin->governorate_id) {
            $request->merge([
                'governorate_id' => $admin->governorate_id,
            ]);
        }
    }

    private function validateLocationRequest(Request $request): void
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'address' => 'nullable|string',
            'address_ar' => 'nullable|string',
            'governorate_id' => 'nullable|exists:governorates,id',
            'city_id' => [
                'nullable',
                Rule::exists('cities', 'id')->where(function ($query) use ($request) {
                    if ($request->filled('governorate_id')) {
                        $query->where('governorate_id', $request->governorate_id);
                    }
                }),
            ],
            'tourism_type_id' => 'nullable|exists:tourism_types,id',
            'phone' => 'nullable|string',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'features' => 'nullable|string',
            'features_ar' => 'nullable|string',
            'rating_description' => 'nullable|string',
            'rating_description_ar' => 'nullable|string',
            'website' => 'nullable|url',
            'email' => 'nullable|email',
            'rating' => 'nullable|numeric|between:0,5',
            'location_types' => 'nullable|array',
            'location_types.*' => 'exists:location_types,id',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_captions.*' => 'nullable|string|max:255',
            'image_captions_ar.*' => 'nullable|string|max:255',
        ]);
    }
}
