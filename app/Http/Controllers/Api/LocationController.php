<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $query = Location::with(['governorate', 'city', 'tourismType', 'locationTypes', 'categories', 'activeImages'])
            ->where('is_active', true);

        if ($request->has('governorate_id')) {
            $query->where('governorate_id', $request->governorate_id);
        }

        if ($request->has('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        if ($request->has('tourism_type_id')) {
            $query->where('tourism_type_id', $request->tourism_type_id);
        }

        if ($request->has('location_type_id')) {
            $query->whereHas('locationTypes', function ($q) use ($request) {
                $q->where('location_types.id', $request->location_type_id);
            });
        }

        if ($request->has('category_id')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name_ar', 'LIKE', "%{$search}%")
                    ->orWhere('name', 'LIKE', "%{$search}%")
                    ->orWhere('address_ar', 'LIKE', "%{$search}%")
                    ->orWhere('address', 'LIKE', "%{$search}%");
            });
        }

        if ($request->has('has_coordinates') && $request->has_coordinates == 1) {
            $query->whereNotNull('latitude')->whereNotNull('longitude');
        }

        $locations = $query->paginate($request->per_page ?? 15);

        $locations->getCollection()->transform(function ($location) {
            return $this->formatLocationWithImages($location);
        });

        return $this->paginatedResponse($locations, 'تم جلب المواقع بنجاح');
    }

    public function show($id)
    {
        $location = Location::with([
            'governorate',
            'city',
            'tourismType',
            'locationTypes',
            'categories',
            'activeImages',
            'events' => function ($query) {
                $query->where('is_published', true)
                    ->where('end_date', '>=', now())
                    ->orderBy('start_date');
            },
            'investments' => function ($query) {
                $query->where('is_published', true);
            }
        ])
            ->where('is_active', true)
            ->findOrFail($id);

        $location->increment('views');

        $isFavorited = false;
        if (auth()->check()) {
            $isFavorited = auth()->user()->favoriteLocations()
                ->where('location_id', $id)
                ->exists();
        }

        return $this->successResponse([
            'location' => $this->formatLocationWithImages($location->fresh(['governorate', 'city', 'tourismType', 'locationTypes', 'categories', 'activeImages'])),
            'is_favorited' => $isFavorited,
        ], 'تم جلب الموقع بنجاح');
    }

    public function nearby(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:0.1|max:100',
        ]);

        $lat = $request->latitude;
        $lng = $request->longitude;
        $radius = $request->radius ?? 10;

        $locations = Location::selectRaw("
                *,
                (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance
            ", [$lat, $lng, $lat])
            ->with(['governorate', 'city', 'tourismType', 'locationTypes', 'categories', 'activeImages'])
            ->where('is_active', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->having('distance', '<', $radius)
            ->orderBy('distance')
            ->limit(20)
            ->get()
            ->map(function ($location) {
                return $this->formatLocationWithImages($location);
            });

        return $this->successResponse($locations, 'تم جلب المواقع القريبة بنجاح');
    }

    public function events($id)
    {
        $location = Location::where('is_active', true)->findOrFail($id);

        $events = $location->events()
            ->with(['category', 'organizers'])
            ->where('is_published', true)
            ->orderBy('start_date')
            ->paginate(10);

        return $this->paginatedResponse($events, 'تم جلب أحداث الموقع بنجاح');
    }

    public function investments($id)
    {
        $location = Location::where('is_active', true)->findOrFail($id);

        $investments = $location->investments()
            ->with(['categories'])
            ->where('is_published', true)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return $this->paginatedResponse($investments, 'تم جلب استثمارات الموقع بنجاح');
    }

    public function toggleFavorite(Request $request, $id)
    {
        $location = Location::where('is_active', true)->findOrFail($id);
        $user = $request->user();

        if ($user->favoriteLocations()->where('location_id', $id)->exists()) {
            $user->favoriteLocations()->detach($id);
            $message = 'تم إزالة الموقع من المفضلة';
            $isFavorited = false;
        } else {
            $user->favoriteLocations()->attach($id);
            $message = 'تم إضافة الموقع إلى المفضلة';
            $isFavorited = true;
        }

        return $this->successResponse([
            'is_favorited' => $isFavorited,
        ], $message);
    }

    public function favorites(Request $request)
    {
        $favorites = $request->user()
            ->favoriteLocations()
            ->with(['governorate', 'city', 'tourismType', 'locationTypes', 'categories', 'activeImages'])
            ->where('is_active', true)
            ->paginate($request->per_page ?? 10);

        $favorites->getCollection()->transform(function ($location) {
            return $this->formatLocationWithImages($location);
        });

        return $this->paginatedResponse($favorites, 'تم جلب المواقع المفضلة بنجاح');
    }

    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2',
        ]);

        $search = $request->q;

        $locations = Location::with(['governorate', 'city', 'tourismType', 'activeImages'])
            ->where('is_active', true)
            ->where(function ($query) use ($search) {
                $query->where('name_ar', 'LIKE', "%{$search}%")
                    ->orWhere('name', 'LIKE', "%{$search}%")
                    ->orWhere('address_ar', 'LIKE', "%{$search}%")
                    ->orWhere('description_ar', 'LIKE', "%{$search}%");
            })
            ->limit(20)
            ->get()
            ->map(function ($location) {
                return $this->formatLocationWithImages($location);
            });

        return $this->successResponse($locations, 'تم البحث في المواقع بنجاح');
    }

    private function formatLocationWithImages($location)
    {
        $locationData = $location->toArray();
        $locationData['main_image_url'] = $location->main_image_url;

        if (isset($locationData['active_images'])) {
            $locationData['active_images'] = $location->activeImages->map(function ($image) {
                return [
                    'id' => $image->id,
                    'image_path' => $image->image_path,
                    'image_url' => $image->image_url,
                    'caption' => $image->caption,
                    'caption_ar' => $image->caption_ar,
                    'order' => $image->order,
                    'is_active' => $image->is_active,
                    'created_at' => $image->created_at,
                    'updated_at' => $image->updated_at,
                ];
            });
        }

        return $locationData;
    }

    public function images($id)
    {
        $location = Location::where('is_active', true)->findOrFail($id);

        $images = [
            'main_image' => [
                'path' => $location->main_image,
                'url' => $location->main_image_url,
            ],
            'gallery_images' => $location->activeImages->map(function ($image) {
                return [
                    'id' => $image->id,
                    'image_path' => $image->image_path,
                    'image_url' => $image->image_url,
                    'caption' => $image->caption,
                    'caption_ar' => $image->caption_ar,
                    'order' => $image->order,
                    'created_at' => $image->created_at,
                ];
            })
        ];

        return $this->successResponse($images, 'تم جلب صور الموقع بنجاح');
    }
}
