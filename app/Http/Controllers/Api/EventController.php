<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $query = Event::with(['category', 'locations', 'organizers'])
            ->where('is_published', true);

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by location
        if ($request->has('location_id')) {
            $query->whereHas('locations', function($q) use ($request) {
                $q->where('locations.id', $request->location_id);
            });
        }

        // Filter by date range
        if ($request->has('date_from')) {
            $query->where('start_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('end_date', '<=', $request->date_to);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by governorate (through locations)
        if ($request->has('governorate_id')) {
            $query->whereHas('locations', function($q) use ($request) {
                $q->where('governorate_id', $request->governorate_id);
            });
        }

        // Search by title or description
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title_ar', 'LIKE', "%{$search}%")
                    ->orWhere('title', 'LIKE', "%{$search}%")
                    ->orWhere('description_ar', 'LIKE', "%{$search}%");
            });
        }

        // Order by
        $orderBy = $request->get('order_by', 'start_date');
        $orderDirection = $request->get('order_direction', 'asc');
        $query->orderBy($orderBy, $orderDirection);

        $events = $query->paginate($request->per_page ?? 10);

        return $this->paginatedResponse($events, 'تم جلب الأحداث بنجاح');
    }

    public function show($id)
    {
        $event = Event::with(['category', 'locations.governorate', 'organizers'])
            ->where('is_published', true)
            ->findOrFail($id);

        $isFavorited = false;
        if (auth()->check()) {
            $isFavorited = auth()->user()->favoriteEvents()
                ->where('event_id', $id)
                ->exists();
        }

        return $this->successResponse([
            'event' => $event,
            'is_favorited' => $isFavorited
        ], 'تم جلب الحدث بنجاح');
    }

    public function toggleFavorite(Request $request, $id)
    {
        $event = Event::where('is_published', true)->findOrFail($id);
        $user = $request->user();

        if ($user->favoriteEvents()->where('event_id', $id)->exists()) {
            $user->favoriteEvents()->detach($id);
            $message = 'تم إزالة الحدث من المفضلة';
            $is_favorited = false;
        } else {
            $user->favoriteEvents()->attach($id);
            $message = 'تم إضافة الحدث إلى المفضلة';
            $is_favorited = true;
        }

        return $this->successResponse([
            'is_favorited' => $is_favorited
        ], $message);
    }

    public function favorites(Request $request)
    {
        $favorites = $request->user()
            ->favoriteEvents()
            ->with(['category', 'locations'])
            ->where('is_published', true)
            ->paginate($request->per_page ?? 10);

        return $this->paginatedResponse($favorites, 'تم جلب الأحداث المفضلة بنجاح');
    }

    public function upcoming(Request $request)
    {
        $events = Event::with(['category', 'locations'])
            ->where('is_published', true)
            ->where('start_date', '>', now())
            ->orderBy('start_date')
            ->paginate($request->per_page ?? 10);

        return $this->paginatedResponse($events, 'تم جلب الأحداث القادمة بنجاح');
    }

    public function active(Request $request)
    {
        $events = Event::with(['category', 'locations'])
            ->where('is_published', true)
            ->where('status', 'active')
            ->orWhere(function($query) {
                $query->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
            })
            ->orderBy('start_date')
            ->paginate($request->per_page ?? 10);

        return $this->paginatedResponse($events, 'تم جلب الأحداث النشطة بنجاح');
    }

    public function byLocation($locationId, Request $request)
    {
        $events = Event::with(['category', 'organizers'])
            ->whereHas('locations', function($query) use ($locationId) {
                $query->where('locations.id', $locationId);
            })
            ->where('is_published', true)
            ->orderBy('start_date')
            ->paginate($request->per_page ?? 10);

        return $this->paginatedResponse($events, 'تم جلب أحداث الموقع بنجاح');
    }

    public function byCategory($categoryId, Request $request)
    {
        $events = Event::with(['locations', 'organizers'])
            ->where('category_id', $categoryId)
            ->where('is_published', true)
            ->orderBy('start_date')
            ->paginate($request->per_page ?? 10);

        return $this->paginatedResponse($events, 'تم جلب أحداث التصنيف بنجاح');
    }
}
