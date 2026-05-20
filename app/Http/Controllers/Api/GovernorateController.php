<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Governorate;
use App\Traits\ApiResponse;

class GovernorateController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $governorates = Governorate::where('is_active', true)
            ->withCount('locations')
            ->withCount('cities')
            ->orderBy('name_ar')
            ->get();

        return $this->successResponse($governorates, 'تم جلب المحافظات بنجاح');
    }

    public function show($id)
    {
        $governorate = Governorate::with([
            'cities' => function ($query) {
                $query->where('is_active', true)->orderBy('name_ar');
            },
            'locations' => function ($query) {
                $query->where('is_active', true);
            }
        ])
            ->where('is_active', true)
            ->findOrFail($id);

        return $this->successResponse($governorate, 'تم جلب المحافظة بنجاح');
    }

    public function cities($id)
    {
        $governorate = Governorate::where('is_active', true)->findOrFail($id);

        return $this->successResponse([
            'governorate' => $governorate,
            'cities' => $governorate->cities()->active()->orderBy('name_ar')->get(),
        ], 'تم جلب مدن المحافظة بنجاح');
    }

    public function locations($id)
    {
        $governorate = Governorate::where('is_active', true)->findOrFail($id);

        $locations = $governorate->locations()
            ->where('is_active', true)
            ->with(['tourismType', 'locationTypes', 'categories', 'city'])
            ->get();

        return $this->successResponse([
            'governorate' => $governorate,
            'locations' => $locations,
        ], 'تم جلب مواقع المحافظة بنجاح');
    }
}
