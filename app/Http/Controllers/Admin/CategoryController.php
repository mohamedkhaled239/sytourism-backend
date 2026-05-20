<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $type = $request->get('type', 'all');

        $query = Category::query();

        if ($type !== 'all') {
            $query->where('type', $type);
        }

        $categories = $query->latest()->paginate(20);

        return view('admin.categories.index', compact('categories', 'type'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $type = $request->get('type', 'news');
        return view('admin.categories.create', compact('type'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'type' => 'required|in:news,events,investments,locations',
            'color' => 'required|string|max:7',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
        ]);

        Category::create($request->all());

        return redirect()->route('admin.categories.index', ['type' => $request->type])
            ->with('success', 'تم إضافة التصنيف بنجاح');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'type' => 'required|in:news,events,investments,locations',
            'color' => 'required|string|max:7',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
        ]);

        $category->update($request->all());

        return redirect()->route('admin.categories.index', ['type' => $category->type])
            ->with('success', 'تم تحديث التصنيف بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $type = $category->type;
        $category->delete();

        return redirect()->route('admin.categories.index', ['type' => $type])
            ->with('success', 'تم حذف التصنيف بنجاح');
    }
}
