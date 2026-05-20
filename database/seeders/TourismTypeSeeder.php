<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TourismTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tourismTypes = [
            ['name' => 'Cultural Tourism', 'name_ar' => 'السياحة الثقافية', 'color' => '#3E5828', 'icon' => 'fas fa-landmark'],
            ['name' => 'Religious Tourism', 'name_ar' => 'السياحة الدينية', 'color' => '#8A6B4E', 'icon' => 'fas fa-mosque'],
            ['name' => 'Archaeological Tourism', 'name_ar' => 'السياحة الأثرية', 'color' => '#3E5828', 'icon' => 'fas fa-monument'],
            ['name' => 'Natural Tourism', 'name_ar' => 'السياحة الطبيعية', 'color' => '#8A6B4E', 'icon' => 'fas fa-tree'],
            ['name' => 'Medical Tourism', 'name_ar' => 'السياحة العلاجية', 'color' => '#3E5828', 'icon' => 'fas fa-hospital'],
            ['name' => 'Adventure Tourism', 'name_ar' => 'سياحة المغامرات', 'color' => '#8A6B4E', 'icon' => 'fas fa-mountain'],
            ['name' => 'Business Tourism', 'name_ar' => 'سياحة الأعمال', 'color' => '#3E5828', 'icon' => 'fas fa-briefcase'],
        ];

        foreach ($tourismTypes as $type) {
            \App\Models\TourismType::create($type);
        }
    }
}
