<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Governorate;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CityController extends Controller
{
    public function index()
    {
        $cities = City::with('governorate')
            ->withCount('locations')
            ->latest()
            ->paginate(20);

        return view('admin.cities.index', compact('cities'));
    }

    public function create()
    {
        $governorates = Governorate::active()->orderBy('name_ar')->get();

        return view('admin.cities.create', compact('governorates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'governorate_id' => ['required', 'exists:governorates,id'],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('cities', 'name')->where(fn ($query) => $query->where('governorate_id', $request->governorate_id)),
            ],
            'name_ar' => [
                'required',
                'string',
                'max:255',
                Rule::unique('cities', 'name_ar')->where(fn ($query) => $query->where('governorate_id', $request->governorate_id)),
            ],
        ]);

        City::create($request->only(['governorate_id', 'name', 'name_ar', 'is_active']));

        return redirect()->route('admin.cities.index')
            ->with('success', 'تم إضافة المدينة بنجاح');
    }

    public function edit($id)
    {
        $city = City::findOrFail($id);
        $governorates = Governorate::active()->orderBy('name_ar')->get();

        return view('admin.cities.edit', compact('city', 'governorates'));
    }

    public function show($id)
    {
        return redirect()->route('admin.cities.edit', $id);
    }

    public function update(Request $request, $id)
    {
        $city = City::findOrFail($id);

        $request->validate([
            'governorate_id' => ['required', 'exists:governorates,id'],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('cities', 'name')
                    ->ignore($city->id)
                    ->where(fn ($query) => $query->where('governorate_id', $request->governorate_id)),
            ],
            'name_ar' => [
                'required',
                'string',
                'max:255',
                Rule::unique('cities', 'name_ar')
                    ->ignore($city->id)
                    ->where(fn ($query) => $query->where('governorate_id', $request->governorate_id)),
            ],
        ]);

        $city->update($request->only(['governorate_id', 'name', 'name_ar', 'is_active']));

        return redirect()->route('admin.cities.index')
            ->with('success', 'تم تحديث المدينة بنجاح');
    }

    public function destroy($id)
    {
        $city = City::withCount('locations')->findOrFail($id);

        if ($city->locations_count > 0) {
            return redirect()->route('admin.cities.index')
                ->with('error', 'لا يمكن حذف هذه المدينة لأنها مرتبطة بمواقع');
        }

        $city->delete();

        return redirect()->route('admin.cities.index')
            ->with('success', 'تم حذف المدينة بنجاح');
    }
}
