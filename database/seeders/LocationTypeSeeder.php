<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LocationType;

class LocationTypeSeeder extends Seeder
{
    /**
     * تنفيذ بيانات الجدول - كل أنواع المواقع من قائمة الرموز المعتمدة
     */
    public function run(): void
    {
        // حذف البيانات القديمة أولاً إذا وُجدت
        LocationType::query()->delete();

        $locationTypes = [
            // ======= المنشآت السياحية =======
            [
                'name'        => 'Restaurant',
                'name_ar'     => 'مطعم',
                'color'       => '#E74C3C',
                'icon'        => 'fas fa-utensils',
                'pin_image'   => 'مطعم.png',
                'description_ar' => 'المطاعم ومحلات الطعام',
                'is_active'   => true,
            ],
            [
                'name'        => 'Hotel',
                'name_ar'     => 'فندق',
                'color'       => '#2980B9',
                'icon'        => 'fas fa-hotel',
                'pin_image'   => 'فندق.png',
                'description_ar' => 'الفنادق ودور الإقامة',
                'is_active'   => true,
            ],
            [
                'name'        => 'Furnished Apartments',
                'name_ar'     => 'شقق مفروشة',
                'color'       => '#8E44AD',
                'icon'        => 'fas fa-building',
                'pin_image'   => 'شقق مفروشة.png',
                'description_ar' => 'الشقق المفروشة للإيجار',
                'is_active'   => true,
            ],
            [
                'name'        => 'Café',
                'name_ar'     => 'مقهى',
                'color'       => '#D35400',
                'icon'        => 'fas fa-coffee',
                'pin_image'   => 'مقهى.png',
                'description_ar' => 'المقاهي ومحلات القهوة',
                'is_active'   => true,
            ],
            [
                'name'        => 'Swimming Pool',
                'name_ar'     => 'مسبح',
                'color'       => '#1ABC9C',
                'icon'        => 'fas fa-swimming-pool',
                'pin_image'   => 'مسبح.png',
                'description_ar' => 'المسابح والمنشآت المائية',
                'is_active'   => true,
            ],
            [
                'name'        => 'Resort',
                'name_ar'     => 'منتجع',
                'color'       => '#27AE60',
                'icon'        => 'fas fa-umbrella-beach',
                'pin_image'   => 'منتجع.png',
                'description_ar' => 'المنتجعات السياحية',
                'is_active'   => true,
            ],
            [
                'name'        => 'Rest Stop',
                'name_ar'     => 'استراحة طرقية',
                'color'       => '#7F8C8D',
                'icon'        => 'fas fa-road',
                'pin_image'   => 'استراحة طرقية (2).png',
                'description_ar' => 'الاستراحات الطرقية',
                'is_active'   => true,
            ],
            [
                'name'        => 'Park',
                'name_ar'     => 'منتزه',
                'color'       => '#2ECC71',
                'icon'        => 'fas fa-tree',
                'pin_image'   => 'منتزه.png',
                'description_ar' => 'المنتزهات والحدائق العامة',
                'is_active'   => true,
            ],
            [
                'name'        => 'Bar',
                'name_ar'     => 'بار',
                'color'       => '#C0392B',
                'icon'        => 'fas fa-glass-martini-alt',
                'pin_image'   => null,
                'description_ar' => 'الحانات ومحلات المشروبات',
                'is_active'   => true,
            ],
            [
                'name'        => 'Chalets',
                'name_ar'     => 'شاليهات',
                'color'       => '#6C8B3A',
                'icon'        => 'fas fa-house-user',
                'pin_image'   => 'شاليهات.png',
                'description_ar' => 'الشاليهات وبيوت العطل',
                'is_active'   => true,
            ],
            [
                'name'        => 'Night Club',
                'name_ar'     => 'ملهى',
                'color'       => '#9B59B6',
                'icon'        => 'fas fa-music',
                'pin_image'   => 'ملهى.png',
                'description_ar' => 'الملاهي الليلية',
                'is_active'   => true,
            ],
            [
                'name'        => 'Fast Food',
                'name_ar'     => 'وجبات سريعة',
                'color'       => '#F39C12',
                'icon'        => 'fas fa-hamburger',
                'pin_image'   => 'وجبات سريعة.png',
                'description_ar' => 'مطاعم الوجبات السريعة',
                'is_active'   => true,
            ],

            // ======= مواقع الجذب السياحي =======
            [
                'name'        => 'Natural',
                'name_ar'     => 'طبيعي',
                'color'       => '#27AE60',
                'icon'        => 'fas fa-leaf',
                'pin_image'   => 'طبيعي.png',
                'description_ar' => 'المواقع الطبيعية والمناطق البيئية',
                'is_active'   => true,
            ],
            [
                'name'        => 'Archaeological',
                'name_ar'     => 'آثار',
                'color'       => '#E67E22',
                'icon'        => 'fas fa-monument',
                'pin_image'   => 'مناطق اثرية.png',
                'description_ar' => 'المواقع الأثرية والتاريخية',
                'is_active'   => true,
            ],
            [
                'name'        => 'Cultural',
                'name_ar'     => 'ثقافي',
                'color'       => '#8E44AD',
                'icon'        => 'fas fa-theater-masks',
                'pin_image'   => 'ثقافي.png',
                'description_ar' => 'المواقع الثقافية والفنية',
                'is_active'   => true,
            ],
            [
                'name'        => 'Recreational',
                'name_ar'     => 'ترفيه',
                'color'       => '#E74C3C',
                'icon'        => 'fas fa-gamepad',
                'pin_image'   => 'ترفيه.png',
                'description_ar' => 'مراكز الترفيه والألعاب',
                'is_active'   => true,
            ],
            [
                'name'        => 'Shopping',
                'name_ar'     => 'تسوق',
                'color'       => '#2980B9',
                'icon'        => 'fas fa-shopping-bag',
                'pin_image'   => 'تسوق.png',
                'description_ar' => 'مراكز التسوق والأسواق',
                'is_active'   => true,
            ],
            [
                'name'        => 'Islamic Religious',
                'name_ar'     => 'ديني إسلامي',
                'color'       => '#1ABC9C',
                'icon'        => 'fas fa-mosque',
                'pin_image'   => 'سياحة اسلامية.png',
                'description_ar' => 'المساجد والمواقع الإسلامية',
                'is_active'   => true,
            ],
            [
                'name'        => 'Christian Religious',
                'name_ar'     => 'ديني مسيحي',
                'color'       => '#3498DB',
                'icon'        => 'fas fa-church',
                'pin_image'   => 'سياحة مسيحية.png',
                'description_ar' => 'الكنائس والمواقع المسيحية',
                'is_active'   => true,
            ],
            [
                'name'        => 'Historical',
                'name_ar'     => 'تاريخي',
                'color'       => '#D35400',
                'icon'        => 'fas fa-landmark',
                'pin_image'   => 'تاريخي.png',
                'description_ar' => 'المواقع التاريخية',
                'is_active'   => true,
            ],

            // ======= الأنشطة السياحية =======
            [
                'name'        => 'Flower Expo',
                'name_ar'     => 'معرض الزهور',
                'color'       => '#E91E63',
                'icon'        => 'fas fa-seedling',
                'pin_image'   => 'معرض الزهور.png',
                'description_ar' => 'معرض الزهور السنوي',
                'is_active'   => true,
            ],
            [
                'name'        => 'Damascus International Expo',
                'name_ar'     => 'معرض دمشق الدولي',
                'color'       => '#3E5828',
                'icon'        => 'fas fa-building',
                'pin_image'   => 'معرض دمشق الدولي.png',
                'description_ar' => 'معرض دمشق الدولي',
                'is_active'   => true,
            ],
            [
                'name'        => 'Zipline',
                'name_ar'     => 'زيبلاين',
                'color'       => '#00BCD4',
                'icon'        => 'fas fa-meteor',
                'pin_image'   => 'zipline.png',
                'description_ar' => 'نشاط الزيبلاين والتحليق',
                'is_active'   => true,
            ],

            // ======= العمل السياحي =======
            [
                'name'        => 'Tourism Job',
                'name_ar'     => 'العمل السياحي',
                'color'       => '#607D8B',
                'icon'        => 'fas fa-briefcase',
                'pin_image'   => 'العمل السياحي.png',
                'description_ar' => 'مكاتب العمل السياحي',
                'is_active'   => true,
            ],

            // ======= المشاريع والفرص الاستثمارية =======
            [
                'name'        => 'Investment Projects',
                'name_ar'     => 'مشاريع استثمارية',
                'color'       => '#FF9800',
                'icon'        => 'fas fa-chart-line',
                'pin_image'   => 'المشاريع والفرص الاستثمارية.png',
                'description_ar' => 'المشاريع والفرص الاستثمارية السياحية',
                'is_active'   => true,
            ],

            // ======= منشآت التعليم والتدريب =======
            [
                'name'        => 'Tourism Education',
                'name_ar'     => 'منشآت التعليم السياحي',
                'color'       => '#5C6BC0',
                'icon'        => 'fas fa-graduation-cap',
                'pin_image'   => 'منشات التعليم.png',
                'description_ar' => 'منشآت التعليم والتدريب السياحي والفندقي',
                'is_active'   => true,
            ],

            // ======= خارطة الأساس =======
            [
                'name'        => 'Airport',
                'name_ar'     => 'مطار',
                'color'       => '#0288D1',
                'icon'        => 'fas fa-plane',
                'pin_image'   => 'مطارات.png',
                'description_ar' => 'المطارات الدولية والداخلية',
                'is_active'   => true,
            ],
            [
                'name'        => 'Ports and Harbors',
                'name_ar'     => 'موانئ ومرافئ',
                'color'       => '#0097A7',
                'icon'        => 'fas fa-anchor',
                'pin_image'   => 'موانئ.png',
                'description_ar' => 'الموانئ والمرافئ البحرية',
                'is_active'   => true,
            ],
            [
                'name'        => 'Border Outlets',
                'name_ar'     => 'منافذ حدودية',
                'color'       => '#455A64',
                'icon'        => 'fas fa-passport',
                'pin_image'   => 'منافذ حدودية.png',
                'description_ar' => 'المنافذ والمعابر الحدودية',
                'is_active'   => true,
            ],
            [
                'name'        => 'International Archaeological Sites',
                'name_ar'     => 'مناطق أثرية مسجلة دولياً',
                'color'       => '#795548',
                'icon'        => 'fas fa-globe',
                'pin_image'   => 'مناطق اثرية.png',
                'description_ar' => 'المواقع الأثرية المسجلة على قوائم التراث الدولي',
                'is_active'   => true,
            ],
            [
                'name'        => 'Hospital',
                'name_ar'     => 'مشفى',
                'color'       => '#E53935',
                'icon'        => 'fas fa-hospital',
                'pin_image'   => 'مشفى.png',
                'description_ar' => 'المستشفيات والمراكز الطبية',
                'is_active'   => true,
            ],
            [
                'name'        => 'Governorate Center',
                'name_ar'     => 'مركز محافظة',
                'color'       => '#1565C0',
                'icon'        => 'fas fa-city',
                'pin_image'   => 'مراكز المحافظات.png',
                'description_ar' => 'مراكز المحافظات الإدارية',
                'is_active'   => true,
            ],
            [
                'name'        => 'Cave',
                'name_ar'     => 'كهف',
                'color'       => '#546E7A',
                'icon'        => 'fas fa-mountain',
                'pin_image'   => 'كهوف.png',
                'description_ar' => 'الكهوف والمغارات الطبيعية',
                'is_active'   => true,
            ],
            [
                'name'        => 'Bank',
                'name_ar'     => 'بنك',
                'color'       => '#2E7D32',
                'icon'        => 'fas fa-university',
                'pin_image'   => null,
                'description_ar' => 'البنوك والمؤسسات المالية',
                'is_active'   => true,
            ],
            [
                'name'        => 'Embassy',
                'name_ar'     => 'سفارة',
                'color'       => '#BF360C',
                'icon'        => 'fas fa-flag',
                'pin_image'   => 'سفارات.png',
                'description_ar' => 'السفارات والقنصليات الأجنبية',
                'is_active'   => true,
            ],
        ];

        foreach ($locationTypes as $type) {
            LocationType::create($type);
        }

        $this->command->info('✅ تم إضافة ' . count($locationTypes) . ' نوع موقع بنجاح');
    }
}
