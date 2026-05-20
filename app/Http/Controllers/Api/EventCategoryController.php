<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EventCategory;

class EventCategoryController extends Controller
{
    public function index()
    {
        $categories = EventCategory::all();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }
}
