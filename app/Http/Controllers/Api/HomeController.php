<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Event;
use App\Models\News;
use App\Models\Location;

class HomeController extends Controller
{
    // GET /api/home (protected)
    public function summary(Request $request)
    {
        // آخر حدثين مع الصور
        $latestEvents = Event::query()
            ->orderByDesc('created_at')
            ->limit(2)
            ->get(['id', 'title', 'title_ar', 'main_image', 'created_at']);

        // آخر خبرين مع الصور
        $latestNews = News::query()
            ->orderByDesc('created_at')
            ->limit(2)
            ->get(['id', 'title', 'title_ar', 'main_image', 'created_at']);

        // أكثر 3 مواقع مشاهدة حسب عداد المشاهدات
        $topLocations = Location::query()
            ->where('is_active', true)
            ->orderByDesc('views')
            ->limit(3)
            ->get(['id', 'name', 'name_ar', 'views']);

        return response()->json([
            'success' => true,
            'data' => [
                'latest_events' => $latestEvents,
                'latest_news' => $latestNews,
                'top_locations' => $topLocations,
            ]
        ]);
    }
}
