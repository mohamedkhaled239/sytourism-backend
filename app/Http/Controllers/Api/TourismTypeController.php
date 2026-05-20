<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TourismType;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class TourismTypeController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $tourismTypes = TourismType::where('is_active', true)
            ->withCount('locations')
            ->orderBy('name_ar')
            ->get();

        return $this->successResponse($tourismTypes, 'تم جلب أنواع السياحة بنجاح');
    }

    public function show($id)
    {
        $tourismType = TourismType::with(['locations' => function($query) {
            $query->where('is_active', true)
                ->with(['governorate', 'locationTypes', 'categories']);
        }])
            ->where('is_active', true)
            ->findOrFail($id);

        return $this->successResponse($tourismType, 'تم جلب نوع السياحة بنجاح');
    }

    public function locations($id)
    {
        $tourismType = TourismType::where('is_active', true)->findOrFail($id);

        $locations = $tourismType->locations()
            ->where('is_active', true)
            ->with(['governorate', 'locationTypes', 'categories'])
            ->get();

        return $this->successResponse([
            'tourism_type' => $tourismType,
            'locations' => $locations
        ], 'تم جلب مواقع نوع السياحة بنجاح');
    }
}
