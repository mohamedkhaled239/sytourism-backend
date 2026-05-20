<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LocationType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LocationTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $locationTypes = LocationType::latest()->paginate(20);
        return view('admin.location-types.index', compact('locationTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // جلب قائمة صور الدبابيس المتاحة من المجلد العام
        $availablePins = $this->getAvailablePins();
        return view('admin.location-types.create', compact('availablePins'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'name_ar'     => 'required|string|max:255',
            'color'       => 'required|string|max:7',
            'icon'        => 'nullable|string|max:255',
            'pin_image'   => 'nullable|string|max:255',
            'description'    => 'nullable|string',
            'description_ar' => 'nullable|string',
        ]);

        $data = $request->only([
            'name', 'name_ar', 'color', 'icon',
            'pin_image', 'description', 'description_ar'
        ]);
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        LocationType::create($data);

        return redirect()->route('admin.location-types.index')
            ->with('success', 'تم إضافة نوع الموقع بنجاح');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $locationType = LocationType::findOrFail($id);
        $availablePins = $this->getAvailablePins();
        return view('admin.location-types.edit', compact('locationType', 'availablePins'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $locationType = LocationType::findOrFail($id);

        $request->validate([
            'name'        => 'required|string|max:255',
            'name_ar'     => 'required|string|max:255',
            'color'       => 'required|string|max:7',
            'icon'        => 'nullable|string|max:255',
            'pin_image'   => 'nullable|string|max:255',
            'description'    => 'nullable|string',
            'description_ar' => 'nullable|string',
        ]);

        $data = $request->only([
            'name', 'name_ar', 'color', 'icon',
            'pin_image', 'description', 'description_ar'
        ]);
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        $locationType->update($data);

        return redirect()->route('admin.location-types.index')
            ->with('success', 'تم تحديث نوع الموقع بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $locationType = LocationType::findOrFail($id);

        // التحقق من وجود مواقع مرتبطة بهذا النوع
        if ($locationType->locations()->count() > 0) {
            return redirect()->route('admin.location-types.index')
                ->with('error', 'لا يمكن حذف هذا النوع لأنه مرتبط بمواقع');
        }

        $locationType->delete();

        return redirect()->route('admin.location-types.index')
            ->with('success', 'تم حذف نوع الموقع بنجاح');
    }

    /**
     * جلب قائمة صور الدبابيس المتاحة من مجلد public/images/location-type-pins
     */
    private function getAvailablePins(): array
    {
        $pinsDir = public_path('images/location-type-pins');
        if (!is_dir($pinsDir)) {
            return [];
        }
        $files = glob($pinsDir . '/*.png');
        $pins = [];
        foreach ($files as $file) {
            $pins[] = basename($file);
        }
        sort($pins);
        return $pins;
    }
}
