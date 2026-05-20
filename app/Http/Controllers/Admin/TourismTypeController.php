<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TourismType;
use Illuminate\Http\Request;

class TourismTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tourismTypes = TourismType::latest()->paginate(20);
        return view('admin.tourism-types.index', compact('tourismTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.tourism-types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'color' => 'required|string|max:7',
            'icon' => 'required|string|max:255',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
        ]);

        TourismType::create($request->all());

        return redirect()->route('admin.tourism-types.index')
            ->with('success', 'تم إضافة نوع السياحة بنجاح');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $tourismType = TourismType::findOrFail($id);
        return view('admin.tourism-types.edit', compact('tourismType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $tourismType = TourismType::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'color' => 'required|string|max:7',
            'icon' => 'required|string|max:255',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
        ]);

        $tourismType->update($request->all());

        return redirect()->route('admin.tourism-types.index')
            ->with('success', 'تم تحديث نوع السياحة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $tourismType = TourismType::findOrFail($id);

        // التحقق من وجود مواقع مرتبطة بهذا النوع
        if ($tourismType->locations()->count() > 0) {
            return redirect()->route('admin.tourism-types.index')
                ->with('error', 'لا يمكن حذف هذا النوع لأنه مرتبط بمواقع');
        }

        $tourismType->delete();

        return redirect()->route('admin.tourism-types.index')
            ->with('success', 'تم حذف نوع السياحة بنجاح');
    }
}
