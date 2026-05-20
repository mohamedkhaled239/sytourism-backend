<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $type = $request->get('type', 'all');

        $query = Category::query();

        if ($type !== 'all') {
            $query->where('type', $type);
        }

        $categories = $query->where('is_active', true)
            ->orderBy('name_ar')
            ->get();

        return $this->successResponse($categories, 'تم جلب التصنيفات بنجاح');
    }

    public function show($id)
    {
        $category = Category::where('is_active', true)->findOrFail($id);

        return $this->successResponse($category, 'تم جلب التصنيف بنجاح');
    }

    public function getByType($type)
    {
        $validTypes = ['news', 'events', 'investments', 'locations'];

        if (!in_array($type, $validTypes)) {
            return $this->errorResponse('نوع التصنيف غير صحيح', 400);
        }

        $categories = Category::where('type', $type)
            ->where('is_active', true)
            ->orderBy('name_ar')
            ->get();

        return $this->successResponse($categories, "تم جلب تصنيفات {$type} بنجاح");
    }
}
