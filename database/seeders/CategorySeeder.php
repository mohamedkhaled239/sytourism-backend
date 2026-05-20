<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // تصنيفات الأخبار
        $newsCategories = [
            ['name' => 'Tourism News', 'name_ar' => 'أخبار السياحة', 'type' => 'news', 'color' => '#3E5828'],
            ['name' => 'Events', 'name_ar' => 'الفعاليات', 'type' => 'news', 'color' => '#8A6B4E'],
            ['name' => 'Announcements', 'name_ar' => 'الإعلانات', 'type' => 'news', 'color' => '#3E5828'],
            ['name' => 'Updates', 'name_ar' => 'التحديثات', 'type' => 'news', 'color' => '#8A6B4E'],
        ];

        // تصنيفات الأحداث
        $eventCategories = [
            ['name' => 'Cultural Events', 'name_ar' => 'فعاليات ثقافية', 'type' => 'events', 'color' => '#3E5828'],
            ['name' => 'Festivals', 'name_ar' => 'مهرجانات', 'type' => 'events', 'color' => '#8A6B4E'],
            ['name' => 'Conferences', 'name_ar' => 'مؤتمرات', 'type' => 'events', 'color' => '#3E5828'],
            ['name' => 'Workshops', 'name_ar' => 'ورش عمل', 'type' => 'events', 'color' => '#8A6B4E'],
            ['name' => 'Sports', 'name_ar' => 'رياضة', 'type' => 'events', 'color' => '#3E5828'],
        ];

        // تصنيفات الاستثمارات
        $investmentCategories = [
            ['name' => 'Hotels', 'name_ar' => 'فنادق', 'type' => 'investments', 'color' => '#3E5828'],
            ['name' => 'Restaurants', 'name_ar' => 'مطاعم', 'type' => 'investments', 'color' => '#8A6B4E'],
            ['name' => 'Tourism Services', 'name_ar' => 'خدمات سياحية', 'type' => 'investments', 'color' => '#3E5828'],
            ['name' => 'Transportation', 'name_ar' => 'نقل', 'type' => 'investments', 'color' => '#8A6B4E'],
            ['name' => 'Entertainment', 'name_ar' => 'ترفيه', 'type' => 'investments', 'color' => '#3E5828'],
            ['name' => 'Real Estate', 'name_ar' => 'عقارات', 'type' => 'investments', 'color' => '#8A6B4E'],
        ];

        // تصنيفات المواقع
        $locationCategories = [
            ['name' => 'Historical Sites', 'name_ar' => 'مواقع تاريخية', 'type' => 'locations', 'color' => '#3E5828'],
            ['name' => 'Natural Sites', 'name_ar' => 'مواقع طبيعية', 'type' => 'locations', 'color' => '#8A6B4E'],
            ['name' => 'Religious Sites', 'name_ar' => 'مواقع دينية', 'type' => 'locations', 'color' => '#3E5828'],
            ['name' => 'Museums', 'name_ar' => 'متاحف', 'type' => 'locations', 'color' => '#8A6B4E'],
            ['name' => 'Archaeological Sites', 'name_ar' => 'مواقع أثرية', 'type' => 'locations', 'color' => '#3E5828'],
        ];

        $allCategories = array_merge($newsCategories, $eventCategories, $investmentCategories, $locationCategories);

        foreach ($allCategories as $category) {
            Category::create($category);
        }
    }
}
