<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Governorate;
use App\Models\Location;
use App\Models\TourismType;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class MapController extends Controller
{
    use ApiResponse;

    public function locations(Request $request)
    {
        $query = Location::with([
            'categories',
            'governorate',
            'city',
            'tourismType',
            'locationTypes'
        ])
            ->where('is_active', 1)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude');

        if ($request->has('tourism_type_id')) {
            $query->where('tourism_type_id', $request->tourism_type_id);
        }

        if ($request->has('governorate_id')) {
            $query->where('governorate_id', $request->governorate_id);
        }

        if ($request->has('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name_ar', 'LIKE', "%{$search}%")
                    ->orWhere('name', 'LIKE', "%{$search}%")
                    ->orWhere('address_ar', 'LIKE', "%{$search}%");
            });
        }

        if ($request->has('bounds')) {
            $bounds = $request->bounds;
            $query->whereBetween('latitude', [$bounds['south'], $bounds['north']])
                ->whereBetween('longitude', [$bounds['west'], $bounds['east']]);
        }

        $locations = $query->get()->map(function ($location) {
            return [
                'id' => $location->id,
                'name_ar' => $location->name_ar,
                'name' => $location->name,
                'latitude' => (float) $location->latitude,
                'longitude' => (float) $location->longitude,
                'address_ar' => $location->address_ar,
                'description_ar' => $location->description_ar,
                'rating' => $location->rating,
                'tourism_type' => $location->tourismType ? [
                    'id' => $location->tourismType->id,
                    'name_ar' => $location->tourismType->name_ar,
                    'color' => $location->tourismType->color,
                    'icon' => $location->tourismType->icon,
                ] : null,
                'governorate' => $location->governorate ? [
                    'id' => $location->governorate->id,
                    'name_ar' => $location->governorate->name_ar,
                ] : null,
                'city' => $location->city ? [
                    'id' => $location->city->id,
                    'name_ar' => $location->city->name_ar,
                ] : null,
                'categories' => $location->categories->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name_ar' => $category->name_ar,
                        'color' => $category->color,
                    ];
                }),
                'location_types' => $location->locationTypes->map(function ($type) {
                    return [
                        'id'            => $type->id,
                        'name_ar'       => $type->name_ar,
                        'icon'          => $type->icon,
                        'color'         => $type->color,
                        'pin_image_url' => $type->pin_image_url,
                    ];
                })
            ];
        });

        return $this->successResponse($locations, 'تم جلب مواقع الخريطة بنجاح');
    }

    public function locationDetails($id)
    {
        $location = Location::with([
            'categories',
            'governorate',
            'city',
            'tourismType',
            'locationTypes',
            'events' => function ($query) {
                $query->where('is_published', true)
                    ->where('end_date', '>=', now())
                    ->orderBy('start_date')
                    ->limit(5);
            },
            'investments' => function ($query) {
                $query->where('is_published', true)
                    ->orderBy('created_at', 'desc')
                    ->limit(5);
            }
        ])
            ->where('is_active', 1)
            ->findOrFail($id);

        return $this->successResponse($location, 'تم جلب تفاصيل الموقع بنجاح');
    }

    public function stats()
    {
        $totalLocations = Location::where('is_active', 1)->count();
        $locationsWithCoordinates = Location::where('is_active', 1)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->count();

        $governorates = Governorate::whereHas('locations', function ($query) {
            $query->where('is_active', 1);
        })->withCount(['locations' => function ($query) {
            $query->where('is_active', 1);
        }])->get();

        $tourismTypes = TourismType::whereHas('locations', function ($query) {
            $query->where('is_active', 1);
        })->withCount(['locations' => function ($query) {
            $query->where('is_active', 1);
        }])->get();

        return $this->successResponse([
            'total_locations' => $totalLocations,
            'locations_with_coordinates' => $locationsWithCoordinates,
            'coverage_percentage' => $totalLocations > 0 ? round(($locationsWithCoordinates / $totalLocations) * 100, 2) : 0,
            'governorates' => $governorates,
            'tourism_types' => $tourismTypes,
        ], 'تم جلب إحصائيات الخريطة بنجاح');
    }

    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2',
        ]);

        $search = $request->q;

        $locations = Location::with(['governorate', 'city', 'tourismType'])
            ->where('is_active', 1)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where(function ($query) use ($search) {
                $query->where('name_ar', 'LIKE', "%{$search}%")
                    ->orWhere('name', 'LIKE', "%{$search}%")
                    ->orWhere('address_ar', 'LIKE', "%{$search}%");
            })
            ->limit(20)
            ->get()
            ->map(function ($location) {
                return [
                    'id' => $location->id,
                    'name_ar' => $location->name_ar,
                    'latitude' => (float) $location->latitude,
                    'longitude' => (float) $location->longitude,
                    'address_ar' => $location->address_ar,
                    'tourism_type' => $location->tourismType ? [
                        'name_ar' => $location->tourismType->name_ar,
                        'color' => $location->tourismType->color,
                    ] : null,
                    'governorate' => $location->governorate ? [
                        'name_ar' => $location->governorate->name_ar,
                    ] : null,
                    'city' => $location->city ? [
                        'name_ar' => $location->city->name_ar,
                    ] : null,
                ];
            });

        return $this->successResponse($locations, 'تم البحث في مواقع الخريطة بنجاح');
    }

    public function cluster(Request $request)
    {
        $request->validate([
            'zoom' => 'required|integer|min:1|max:18',
            'bounds' => 'required|array',
        ]);

        $zoom = $request->zoom;
        $bounds = $request->bounds;
        $precision = max(1, 6 - floor($zoom / 3));

        $locations = Location::selectRaw("
                ROUND(latitude, {$precision}) as cluster_lat,
                ROUND(longitude, {$precision}) as cluster_lng,
                COUNT(*) as count,
                AVG(latitude) as avg_lat,
                AVG(longitude) as avg_lng
            ")
            ->where('is_active', 1)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->whereBetween('latitude', [$bounds['south'], $bounds['north']])
            ->whereBetween('longitude', [$bounds['west'], $bounds['east']])
            ->groupBy('cluster_lat', 'cluster_lng')
            ->get()
            ->map(function ($cluster) {
                return [
                    'latitude' => (float) $cluster->avg_lat,
                    'longitude' => (float) $cluster->avg_lng,
                    'count' => $cluster->count,
                ];
            });

        return $this->successResponse($locations, 'تم جلب مجموعات المواقع بنجاح');
    }
}
