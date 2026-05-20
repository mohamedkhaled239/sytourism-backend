<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\OneSignalService;
use App\Models\Investment;
use App\Models\Location;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InvestmentController extends Controller
{
    public function index()
    {
        $investments = Investment::with('locations')->latest()->paginate(20);
        return view('admin.investments.index', compact('investments'));
    }

    public function create()
    {
        $locations = Location::all();
        $categories = Category::where('type', 'investments')->active()->get();
        return view('admin.investments.create', compact('locations', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_ar' => 'required|string|max:255',
            'description' => 'required',
            'description_ar' => 'required',
            'main_image' => 'required|image|max:2048',
            'locations' => 'required|array|min:1',
            'locations.*' => 'exists:locations,id',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id'
        ]);

        $imagePath = $request->file('main_image')->store('investments', 'public');

        $investment = Investment::create([
            'title' => $request->title,
            'title_ar' => $request->title_ar,
            'description' => $request->description,
            'description_ar' => $request->description_ar,
            'main_image' => $imagePath,
            'is_published' => $request->has('is_published')
        ]);

        $investment->locations()->attach($request->locations);

        if ($request->has('categories')) {
            $investment->categories()->attach($request->categories);
        }

        // Send push notification to all users via OneSignal
        $oneSignalService = new OneSignalService();
        $oneSignalService->sendInvestmentNotification($investment);

        return redirect()->route('admin.investments.index')
            ->with('success', 'تم إضافة الاستثمار بنجاح');
    }

    public function edit($id)
    {
        $investment = Investment::with('locations')->findOrFail($id);
        $locations = Location::all();
        $categories = Category::where('type', 'investments')->active()->get();
        return view('admin.investments.edit', compact('investment', 'locations', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $investment = Investment::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'title_ar' => 'required|string|max:255',
            'description' => 'required',
            'description_ar' => 'required',
            'main_image' => 'nullable|image|max:2048',
            'locations' => 'required|array|min:1',
            'locations.*' => 'exists:locations,id',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id'
        ]);

        $data = [
            'title' => $request->title,
            'title_ar' => $request->title_ar,
            'description' => $request->description,
            'description_ar' => $request->description_ar,
            'is_published' => $request->has('is_published')
        ];

        if ($request->hasFile('main_image')) {
            Storage::disk('public')->delete($investment->main_image);
            $data['main_image'] = $request->file('main_image')->store('investments', 'public');
        }

        $investment->update($data);
        $investment->locations()->sync($request->locations);
        $investment->categories()->sync($request->categories ?? []);

        return redirect()->route('admin.investments.index')
            ->with('success', 'تم تحديث الاستثمار بنجاح');
    }

    public function destroy($id)
    {
        $investment = Investment::findOrFail($id);
        Storage::disk('public')->delete($investment->main_image);
        $investment->delete();

        return redirect()->route('admin.investments.index')
            ->with('success', 'تم حذف الاستثمار بنجاح');
    }
}
