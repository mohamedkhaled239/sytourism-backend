<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventCategory;
use Illuminate\Http\Request;

class EventCategoryController extends Controller
{
    public function index()
    {
        $categories = EventCategory::withCount('events')->paginate(20);
        return view('admin.event-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.event-categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'color' => 'required|string|max:7'
        ]);

        EventCategory::create($request->all());

        return redirect()->route('admin.event-categories.index')
            ->with('success', 'تم إضافة التصنيف بنجاح');
    }

    public function edit($id)
    {
        $category = EventCategory::findOrFail($id);
        return view('admin.event-categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $category = EventCategory::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'color' => 'required|string|max:7'
        ]);

        $category->update($request->all());

        return redirect()->route('admin.event-categories.index')
            ->with('success', 'تم تحديث التصنيف بنجاح');
    }

    public function destroy($id)
    {
        EventCategory::findOrFail($id)->delete();

        return redirect()->route('admin.event-categories.index')
            ->with('success', 'تم حذف التصنيف بنجاح');
    }
}
