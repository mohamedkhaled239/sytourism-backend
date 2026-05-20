<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Governorate;
use App\Models\TourismType;
use App\Models\Category;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء محافظة للاختبار إذا لم تكن موجودة
        $governorate = Governorate::firstOrCreate(
            ['name_ar' => 'الرياض'],
            ['name' => 'Riyadh']
        );

        // إنشاء نوع سياحة للاختبار إذا لم يكن موجود
        $tourismType = TourismType::firstOrCreate(
            ['name_ar' => 'سياحة ثقافية'],
            ['name' => 'Cultural Tourism']
        );

        // إنشاء تصنيف للاختبار إذا لم يكن موجود
        $category = Category::firstOrCreate(
            ['name_ar' => 'معالم تاريخية'],
            ['name' => 'Historical Landmarks']
        );

        // إنشاء مواقع للاختبار
        $locations = [
            [
                'name' => 'Diriyah',
                'name_ar' => 'الدرعية',
                'latitude' => 24.7358,
                'longitude' => 46.5763,
                'address' => 'Diriyah, Riyadh',
                'address_ar' => 'الدرعية، الرياض',
                'governorate_id' => $governorate->id,
                'tourism_type_id' => $tourismType->id,
                'description' => 'Historical site in Riyadh',
                'description_ar' => 'موقع تاريخي في الرياض',
                'is_active' => true
            ],
            [
                'name' => 'National Museum',
                'name_ar' => 'المتحف الوطني',
                'latitude' => 24.6893,
                'longitude' => 46.7243,
                'address' => 'King Abdul Aziz Historical Centre, Riyadh',
                'address_ar' => 'مركز الملك عبد العزيز التاريخي، الرياض',
                'governorate_id' => $governorate->id,
                'tourism_type_id' => $tourismType->id,
                'description' => 'National museum of Saudi Arabia',
                'description_ar' => 'المتحف الوطني للمملكة العربية السعودية',
                'is_active' => true
            ],
            [
                'name' => 'Kingdom Centre',
                'name_ar' => 'مركز المملكة',
                'latitude' => 24.7116,
                'longitude' => 46.6753,
                'address' => 'Al Olaya, Riyadh',
                'address_ar' => 'العليا، الرياض',
                'governorate_id' => $governorate->id,
                'tourism_type_id' => $tourismType->id,
                'description' => 'Iconic skyscraper in Riyadh',
                'description_ar' => 'ناطحة سحاب مميزة في الرياض',
                'is_active' => true
            ]
        ];

        foreach ($locations as $locationData) {
            $location = Location::firstOrCreate(
                ['name_ar' => $locationData['name_ar']],
                $locationData
            );

            // ربط التصنيف بالموقع
            $location->categories()->syncWithoutDetaching([$category->id]);
        }
    }
}