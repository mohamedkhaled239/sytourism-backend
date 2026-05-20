<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LocationType;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class LocationTypeController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $locationTypes = LocationType::where('is_active', true)
            ->withCount('locations')
            ->orderBy('name_ar')
            ->get()
            ->map(function ($type) {
                return [
                    'id'             => $type->id,
                    'name'           => $type->name,
                    'name_ar'        => $type->name_ar,
                    'icon'           => $type->icon,
                    'color'          => $type->color,
                    'pin_image_url'  => $type->pin_image_url,
                    'locations_count'=> $type->locations_count,
                    'is_active'      => $type->is_active,
                ];
            });

        return $this->successResponse($locationTypes, 'تم جلب أنواع المواقع بنجاح');
    }

    public function show($id)
    {
        $locationType = LocationType::with(['locations' => function($query) {
            $query->where('is_active', true)
                ->with(['governorate', 'tourismType', 'categories']);
        }])
            ->where('is_active', true)
            ->findOrFail($id);

        $data = [
            'id'            => $locationType->id,
            'name'          => $locationType->name,
            'name_ar'       => $locationType->name_ar,
            'icon'          => $locationType->icon,
            'color'         => $locationType->color,
            'pin_image_url' => $locationType->pin_image_url,
            'description_ar'=> $locationType->description_ar,
            'description'   => $locationType->description,
            'is_active'     => $locationType->is_active,
            'locations'     => $locationType->locations,
        ];

        return $this->successResponse($data, 'تم جلب نوع الموقع بنجاح');
    }

    public function locations($id)
    {
        $locationType = LocationType::where('is_active', true)->findOrFail($id);

        $locations = $locationType->locations()
            ->where('is_active', true)
            ->with(['governorate', 'tourismType', 'categories'])
            ->get();

        return $this->successResponse([
            'location_type' => [
                'id'            => $locationType->id,
                'name_ar'       => $locationType->name_ar,
                'icon'          => $locationType->icon,
                'color'         => $locationType->color,
                'pin_image_url' => $locationType->pin_image_url,
            ],
            'locations' => $locations
        ], 'تم جلب مواقع نوع الموقع بنجاح');
    }
}
