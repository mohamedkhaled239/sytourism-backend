<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Investment;
use App\Models\Location;
use App\Models\News;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $admin = auth('admin')->user();
        $locationQuery = Location::query();

        $statistics = [
            'total_users' => User::count(),
            'total_tourists' => User::where('user_type', 'tourist')->count(),
            'total_investors' => User::where('user_type', 'investor')->count(),
            'pending_investors' => User::where('user_type', 'investor')->where('is_approved', false)->count(),
            'verified_users' => User::whereNotNull('email_verified_at')->count(),
            'total_news' => News::count(),
            'total_events' => Event::count(),
            'total_investments' => Investment::count(),
            'total_locations' => (clone $locationQuery)->count(),
        ];

        $locationsByGovernorate = (clone $locationQuery)
            ->select('governorate_id', DB::raw('count(*) as total'))
            ->whereNotNull('governorate_id')
            ->groupBy('governorate_id')
            ->with('governorate:id,name_ar')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->governorate->name_ar ?? 'غير محدد' => $item->total];
            });

        $locationsByTourismType = (clone $locationQuery)
            ->select('tourism_type_id', DB::raw('count(*) as total'))
            ->whereNotNull('tourism_type_id')
            ->groupBy('tourism_type_id')
            ->with('tourismType:id,name_ar')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->tourismType->name_ar ?? 'غير محدد' => $item->total];
            });

        $locationsByType = DB::table('location_location_types')
            ->join('locations', 'location_location_types.location_id', '=', 'locations.id')
            ->join('location_types', 'location_location_types.location_type_id', '=', 'location_types.id')
            ->select('location_types.name_ar', DB::raw('count(*) as total'))
            ->when($admin->governorate_id, function ($query) use ($admin) {
                $query->where('locations.governorate_id', $admin->governorate_id);
            })
            ->when($admin->tourism_type_id, function ($query) use ($admin) {
                $query->where('locations.tourism_type_id', $admin->tourism_type_id);
            })
            ->groupBy('location_types.id', 'location_types.name_ar')
            ->get()
            ->pluck('total', 'name_ar');

        $isScopedAdmin = (bool) $admin->governorate_id;

        return view('admin.dashboard', compact(
            'statistics',
            'locationsByGovernorate',
            'locationsByTourismType',
            'locationsByType',
            'isScopedAdmin'
        ));
    }
}
