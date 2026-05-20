<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // نقل البيانات من event_categories إلى categories
        $eventCategories = DB::table('event_categories')->get();

        foreach ($eventCategories as $eventCategory) {
            // إنشاء تصنيف جديد في جدول categories
            $newCategoryId = DB::table('categories')->insertGetId([
                'name' => $eventCategory->name,
                'name_ar' => $eventCategory->name_ar,
                'type' => 'events',
                'color' => $eventCategory->color,
                'is_active' => 1,
                'created_at' => $eventCategory->created_at,
                'updated_at' => $eventCategory->updated_at,
            ]);

            // تحديث الأحداث لتشير للتصنيف الجديد
            DB::table('events')
                ->where('category_id', $eventCategory->id)
                ->update(['category_id' => $newCategoryId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // حذف التصنيفات المنقولة من جدول categories
        DB::table('categories')->where('type', 'events')->delete();
    }
};
