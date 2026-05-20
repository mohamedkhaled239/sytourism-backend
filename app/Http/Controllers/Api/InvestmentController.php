<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Investment;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class InvestmentController extends Controller
{
    use ApiResponse;

    // تم إزالة __construct لأن middleware يتولى التحقق من المستثمرين

    public function index(Request $request)
    {
        $query = Investment::with(['locations.governorate', 'categories'])
            ->where('is_published', true);

        // Filter by location
        if ($request->has('location_id')) {
            $query->whereHas('locations', function($q) use ($request) {
                $q->where('locations.id', $request->location_id);
            });
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->whereHas('categories', function($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
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
        $orderBy = $request->get('order_by', 'created_at');
        $orderDirection = $request->get('order_direction', 'desc');
        $query->orderBy($orderBy, $orderDirection);

        $investments = $query->paginate($request->per_page ?? 10);

        return $this->paginatedResponse($investments, 'تم جلب الاستثمارات بنجاح');
    }

    public function show($id)
    {
        $investment = Investment::with([
            'locations.governorate',
            'categories'
        ])
            ->where('is_published', true)
            ->findOrFail($id);

        return $this->successResponse($investment, 'تم جلب الاستثمار بنجاح');
    }

    public function byLocation($locationId, Request $request)
    {
        $investments = Investment::with(['categories'])
            ->whereHas('locations', function($query) use ($locationId) {
                $query->where('locations.id', $locationId);
            })
            ->where('is_published', true)
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 10);

        return $this->paginatedResponse($investments, 'تم جلب استثمارات الموقع بنجاح');
    }

    public function byCategory($categoryId, Request $request)
    {
        $investments = Investment::with(['locations'])
            ->whereHas('categories', function($query) use ($categoryId) {
                $query->where('categories.id', $categoryId);
            })
            ->where('is_published', true)
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 10);

        return $this->paginatedResponse($investments, 'تم جلب استثمارات التصنيف بنجاح');
    }

    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2'
        ]);

        $search = $request->q;

        $investments = Investment::with(['locations', 'categories'])
            ->where('is_published', true)
            ->where(function($query) use ($search) {
                $query->where('title_ar', 'LIKE', "%{$search}%")
                    ->orWhere('title', 'LIKE', "%{$search}%")
                    ->orWhere('description_ar', 'LIKE', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return $this->paginatedResponse($investments, 'تم البحث في الاستثمارات بنجاح');
    }
}
