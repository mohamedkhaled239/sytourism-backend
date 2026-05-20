<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Governorate;
use Illuminate\Http\Request;

class GovernorateController extends Controller
{
    public function index()
    {
        $governorates = Governorate::withCount(['locations', 'cities'])->latest()->paginate(20);

        return view('admin.governorates.index', compact('governorates'));
    }

    public function create()
    {
        return view('admin.governorates.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:governorates,code',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
        ]);

        Governorate::create($request->all());

        return redirect()->route('admin.governorates.index')
            ->with('success', 'تم إضافة المحافظة بنجاح');
    }

    public function edit($id)
    {
        $governorate = Governorate::findOrFail($id);

        return view('admin.governorates.edit', compact('governorate'));
    }

    public function show($id)
    {
        return redirect()->route('admin.governorates.edit', $id);
    }

    public function update(Request $request, $id)
    {
        $governorate = Governorate::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:governorates,code,' . $id,
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
        ]);

        $governorate->update($request->all());

        return redirect()->route('admin.governorates.index')
            ->with('success', 'تم تحديث المحافظة بنجاح');
    }

    public function destroy($id)
    {
        $governorate = Governorate::findOrFail($id);

        if ($governorate->locations()->count() > 0) {
            return redirect()->route('admin.governorates.index')
                ->with('error', 'لا يمكن حذف هذه المحافظة لأنها مرتبطة بمواقع');
        }

        if ($governorate->cities()->count() > 0) {
            return redirect()->route('admin.governorates.index')
                ->with('error', 'لا يمكن حذف هذه المحافظة لأنها مرتبطة بمدن');
        }

        $governorate->delete();

        return redirect()->route('admin.governorates.index')
            ->with('success', 'تم حذف المحافظة بنجاح');
    }
}
