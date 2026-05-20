<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\TourismType;
use App\Models\Governorate;
use Illuminate\Http\Request;

class MapController extends Controller
{
    /**
     * عرض صفحة الخريطة مع جميع المواقع
     */
    public function index()
    {
        // جلب جميع المواقع النشطة مع العلاقات
        $locations = Location::with([
            'categories',
            'governorate',
            'city',
            'tourismType',
            'locationTypes'
        ])
            ->where('is_active', 1)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        // جلب إحصائيات إضافية
        $stats = [
            'total_locations' => $locations->count(),
            'total_governorates' => $locations->pluck('governorate')->filter()->unique('id')->count(),
            'total_tourism_types' => $locations->pluck('tourismType')->filter()->unique('id')->count(),
            'locations_with_coordinates' => $locations->where('latitude', '!=', null)
                ->where('longitude', '!=', null)
                ->count()
        ];

        return view('admin.map.index', compact('locations', 'stats'));
    }

    /**
     * عرض تفاصيل موقع محدد
     */
    public function show($id)
    {
        $location = Location::with([
            'categories',
            'governorate',
            'city',
            'tourismType',
            'locationTypes',
            'events',
            'investments'
        ])->findOrFail($id);

        return view('admin.map.show', compact('location'));
    }

    /**
     * البحث في المواقع عبر AJAX
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        $tourismTypeId = $request->get('tourism_type');
        $governorateId = $request->get('governorate');
        $cityId = $request->get('city');

        $locations = Location::with(['categories', 'governorate', 'tourismType'])
            ->where('is_active', 1)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude');

        // فلترة البحث النصي
        if ($query) {
            $locations->where(function($q) use ($query) {
                $q->where('name_ar', 'LIKE', "%{$query}%")
                    ->orWhere('name', 'LIKE', "%{$query}%")
                    ->orWhere('address_ar', 'LIKE', "%{$query}%")
                    ->orWhere('address', 'LIKE', "%{$query}%");
            });
        }

        // فلترة حسب نوع السياحة
        if ($tourismTypeId) {
            $locations->where('tourism_type_id', $tourismTypeId);
        }

        // فلترة حسب المحافظة
        if ($governorateId) {
            $locations->where('governorate_id', $governorateId);
        }

        if ($cityId) {
            $locations->where('city_id', $cityId);
        }

        $results = $locations->get();

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
                    'city' => $location->city ? [
                        'id' => $location->city->id,
                        'name_ar' => $location->city->name_ar
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
        $totalLocations = Location::where('is_active', 1)->count();
        $locationsWithCoordinates = Location::where('is_active', 1)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->count();

        $governorates = Governorate::whereHas('locations', function($query) {
            $query->where('is_active', 1);
        })->withCount(['locations' => function($query) {
            $query->where('is_active', 1);
        }])->get();

        $tourismTypes = TourismType::whereHas('locations', function($query) {
            $query->where('is_active', 1);
        })->withCount(['locations' => function($query) {
            $query->where('is_active', 1);
        }])->get();

        return response()->json([
            'total_locations' => $totalLocations,
            'locations_with_coordinates' => $locationsWithCoordinates,
            'governorates' => $governorates,
            'tourism_types' => $tourismTypes
        ]);
    }
}
