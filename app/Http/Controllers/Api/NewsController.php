<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $query = News::with(['categories', 'images'])
            ->where('is_published', true);

        // Filter by category
        if ($request->has('category_id')) {
            $query->whereHas('categories', function($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }

        // Search by title or content
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title_ar', 'LIKE', "%{$search}%")
                    ->orWhere('title', 'LIKE', "%{$search}%")
                    ->orWhere('content_ar', 'LIKE', "%{$search}%");
            });
        }

        // Order by
        $orderBy = $request->get('order_by', 'created_at');
        $orderDirection = $request->get('order_direction', 'desc');
        $query->orderBy($orderBy, $orderDirection);

        $news = $query->paginate($request->per_page ?? 10);

        return $this->paginatedResponse($news, 'تم جلب الأخبار بنجاح');
    }

    public function show($id)
    {
        $news = News::with(['images', 'categories'])
            ->where('is_published', true)
            ->findOrFail($id);

        // Increment views
        $news->increment('views');

        return $this->successResponse($news, 'تم جلب الخبر بنجاح');
    }

    public function latest(Request $request)
    {
        $news = News::with(['categories'])
            ->where('is_published', true)
            ->orderBy('created_at', 'desc')
            ->limit($request->limit ?? 5)
            ->get();

        return $this->successResponse($news, 'تم جلب آخر الأخبار بنجاح');
    }

    public function popular(Request $request)
    {
        $news = News::with(['categories'])
            ->where('is_published', true)
            ->orderBy('views', 'desc')
            ->limit($request->limit ?? 10)
            ->get();

        return $this->successResponse($news, 'تم جلب الأخبار الشائعة بنجاح');
    }

    public function byCategory($categoryId, Request $request)
    {
        $news = News::with(['images'])
            ->whereHas('categories', function($query) use ($categoryId) {
                $query->where('categories.id', $categoryId);
            })
            ->where('is_published', true)
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 10);

        return $this->paginatedResponse($news, 'تم جلب أخبار التصنيف بنجاح');
    }

    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2'
        ]);

        $search = $request->q;

        $news = News::with(['categories'])
            ->where('is_published', true)
            ->where(function($query) use ($search) {
                $query->where('title_ar', 'LIKE', "%{$search}%")
                    ->orWhere('title', 'LIKE', "%{$search}%")
                    ->orWhere('content_ar', 'LIKE', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return $this->paginatedResponse($news, 'تم البحث في الأخبار بنجاح');
    }

    public function related($id, Request $request)
    {
        $currentNews = News::findOrFail($id);

        $related = News::with(['categories'])
            ->where('is_published', true)
            ->where('id', '!=', $id)
            ->whereHas('categories', function($query) use ($currentNews) {
                $categoryIds = $currentNews->categories->pluck('id');
                $query->whereIn('categories.id', $categoryIds);
            })
            ->orderBy('created_at', 'desc')
            ->limit($request->limit ?? 5)
            ->get();

        return $this->successResponse($related, 'تم جلب الأخبار ذات الصلة بنجاح');
    }
}
