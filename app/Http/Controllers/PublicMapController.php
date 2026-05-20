<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\TourismType;
use App\Models\Governorate;
use Illuminate\Http\Request;
class PublicMapController extends Controller
{
    private const HIDDEN_PUBLIC_TOURISM_TYPE_IDS = [72, 76, 83];

    private function applyPublicVisibilityFilters($query)
    {
        $query->whereNotIn('tourism_type_id', self::HIDDEN_PUBLIC_TOURISM_TYPE_IDS);

        return $query;
    }

    /**
     * عرض صفحة الخريطة العامة
     */
    public function index()
    {
        $query = Location::withoutGlobalScope('admin_tourism_type')->with([
            'categories',
            'governorate',
            'tourismType',
            'locationTypes'
        ])
            ->where('is_active', 1)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude');

        $this->applyPublicVisibilityFilters($query);

        $locations = $query->get();

        $stats = [
            'total_locations' => $locations->count(),
            'total_governorates' => $locations->pluck('governorate')->filter()->unique('id')->count(),
            'total_tourism_types' => $locations->pluck('tourismType')->filter()->unique('id')->count(),
            'locations_with_coordinates' => $locations->where('latitude', '!=', null)
                ->where('longitude', '!=', null)
                ->count()
        ];

        return view('public.map.index', compact('locations', 'stats'));
    }

    /**
     * عرض تفاصيل الموقع
     */
    public function show($id)
    {
        $query = Location::withoutGlobalScope('admin_tourism_type')->with([
            'categories',
            'governorate',
            'tourismType',
            'locationTypes',
            'events' => function ($query) {
                $query->where('is_published', true)->orderBy('events.start_date', 'desc');
            },
            'investments' => function ($query) {
                $query->where('is_published', true)->orderByDesc('investments.created_at');
            }
        ]);

        $this->applyPublicVisibilityFilters($query);

        $location = $query->findOrFail($id);

        return view('public.map.show', compact('location'));
    }

    /**
     * البحث في المواقع العامة
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        $tourismTypeId = $request->get('tourism_type');
        $governorateId = $request->get('governorate');

        $locationsQuery = Location::withoutGlobalScope('admin_tourism_type')->with(['categories', 'governorate', 'tourismType'])
            ->where('is_active', 1)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude');

        $this->applyPublicVisibilityFilters($locationsQuery);

        if ($query) {
            $locationsQuery->where(function($q) use ($query) {
                $q->where('name_ar', 'LIKE', "%{$query}%")
                    ->orWhere('name', 'LIKE', "%{$query}%")
                    ->orWhere('address_ar', 'LIKE', "%{$query}%")
                    ->orWhere('address', 'LIKE', "%{$query}%");
            });
        }

        if ($tourismTypeId && !in_array((int) $tourismTypeId, self::HIDDEN_PUBLIC_TOURISM_TYPE_IDS, true)) {
            $locationsQuery->where('tourism_type_id', $tourismTypeId);
        }

        if ($governorateId) {
            $locationsQuery->where('governorate_id', $governorateId);
        }

        $results = $locationsQuery->get();

        return response()->json([
            'success' => true,
            'data' => $results->map(function($location) {
                return [
                    'id' => $location->id,
                    'name_ar' => $location->name_ar,
                    'name' => $location->name,
                    'latitude' => (float) $location->latitude,
                    'longitude' => (float) $location->longitude,
                    'address_ar' => $location->address_ar,
                    'tourism_type' => $location->tourismType ? [
                        'id' => $location->tourismType->id,
                        'name_ar' => $location->tourismType->name_ar,
                        'color' => $location->tourismType->color
                    ] : null,
                    'governorate' => $location->governorate ? [
                        'id' => $location->governorate->id,
                        'name_ar' => $location->governorate->name_ar
                    ] : null,
                ];
            }),
            'count' => $results->count()
        ]);
    }

    /**
     * الحصول على إحصائيات الخريطة
     */
    public function getStats()
    {
        $baseQuery = function($query) {
            $query->withoutGlobalScope('admin_tourism_type')
                  ->where('is_active', 1);

            $this->applyPublicVisibilityFilters($query);
        };

        $totalLocations = Location::where($baseQuery)->count();
        $locationsWithCoordinates = Location::where($baseQuery)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->count();

        $governorates = Governorate::whereHas('locations', $baseQuery)
            ->withCount(['locations' => $baseQuery])->get();

        $tourismTypesQuery = TourismType::query()->whereNotIn('id', self::HIDDEN_PUBLIC_TOURISM_TYPE_IDS);
        $tourismTypes = $tourismTypesQuery->whereHas('locations', $baseQuery)
            ->withCount(['locations' => $baseQuery])->get();

        return response()->json([
            'total_locations' => $totalLocations,
            'locations_with_coordinates' => $locationsWithCoordinates,
            'governorates' => $governorates,
            'tourism_types' => $tourismTypes
        ]);
    }
}
