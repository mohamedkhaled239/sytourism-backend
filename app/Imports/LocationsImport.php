<?php

namespace App\Imports;

use App\Models\Location;
use App\Models\City;
use App\Models\Governorate;
use App\Models\TourismType;
use App\Models\LocationType;
use App\Models\Category;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class LocationsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsEmptyRows, WithCustomCsvSettings
{
    private $errors = [];

    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        try {
            // تنظيف البيانات وتطبيق أسماء الأعمدة البديلة
            $cleanedRow = $this->cleanRowData($row);

            // التحقق من وجود الحقول المطلوبة
            if (empty($cleanedRow['name']) || empty($cleanedRow['name_ar'])) {
                Log::warning('Skipping row due to missing required fields', ['row' => $row]);
                return null;
            }

            // إنشاء الموقع مع البيانات الأساسية فقط
            $locationData = [
                'name' => $cleanedRow['name'],
                'name_ar' => $cleanedRow['name_ar'],
                'latitude' => $this->parseCoordinate($cleanedRow['latitude']),
                'longitude' => $this->parseCoordinate($cleanedRow['longitude']),
                'address' => $cleanedRow['address'],
                'address_ar' => $cleanedRow['address_ar'],
                'phone' => $cleanedRow['phone'],
                'description' => $cleanedRow['description'],
                'description_ar' => $cleanedRow['description_ar'],
                'features' => $cleanedRow['features'],
                'features_ar' => $cleanedRow['features_ar'],
                'website' => $this->validateUrl($cleanedRow['website']),
                'email' => $this->validateEmail($cleanedRow['email']),
                'rating' => $this->parseRating($cleanedRow['rating']),
                'is_active' => true,
            ];

            // التعامل مع المحافظة (اختياري)
            if (!empty($cleanedRow['governorate'])) {
                $governorate = $this->findOrCreateGovernorate($cleanedRow);
                $locationData['governorate_id'] = $governorate->id;

                if (!empty($cleanedRow['city'])) {
                    $city = $this->findOrCreateCity($governorate, $cleanedRow);
                    $locationData['city_id'] = $city->id;
                }
            }

            // التعامل مع نوع السياحة (اختياري)
            if (!empty($cleanedRow['tourism_type'])) {
                $tourismType = $this->findOrCreateTourismType($cleanedRow);
                $locationData['tourism_type_id'] = $tourismType->id;
            }

            $location = Location::create($locationData);

            // التعامل مع أنواع المواقع (اختياري)
            if (!empty($cleanedRow['location_types'])) {
                $this->attachLocationTypes($location, $cleanedRow['location_types']);
            }

            // التعامل مع الفئات (اختياري)
            if (!empty($cleanedRow['categories'])) {
                $this->attachCategories($location, $cleanedRow['categories']);
            }

            return $location;

        } catch (\Exception $e) {
            Log::error('Error importing location: ' . $e->getMessage(), [
                'row' => $row,
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ',',
            'enclosure' => '"',
            'input_encoding' => 'UTF-8',
        ];
    }

    /**
     * تنظيف بيانات الصف وتطبيق أسماء الأعمدة البديلة
     */
    private function cleanRowData(array $row): array
    {
        return [
            'name' => trim($row['name'] ?? $row['location_name'] ?? ''),
            'name_ar' => trim($row['name_ar'] ?? $row['arabic_name'] ?? ''),
            'latitude' => $row['latitude'] ?? $row['lat'] ?? null,
            'longitude' => $row['longitude'] ?? $row['lng'] ?? null,
            'address' => trim($row['address'] ?? ''),
            'address_ar' => trim($row['address_ar'] ?? $row['arabic_address'] ?? ''),
            'phone' => trim($row['phone'] ?? ''),
            'description' => trim($row['description'] ?? ''),
            'description_ar' => trim($row['description_ar'] ?? ''),
            'features' => trim($row['features'] ?? ''),
            'features_ar' => trim($row['features_ar'] ?? ''),
            'website' => trim($row['website'] ?? ''),
            'email' => trim($row['email'] ?? ''),
            'rating' => $row['rating'] ?? null,
            'governorate' => trim($row['governorate'] ?? ''),
            'governorate_ar' => trim($row['governorate_ar'] ?? ''),
            'city' => trim($row['city'] ?? ''),
            'city_ar' => trim($row['city_ar'] ?? ''),
            'tourism_type' => trim($row['tourism_type'] ?? ''),
            'tourism_type_ar' => trim($row['tourism_type_ar'] ?? ''),
            'location_types' => trim($row['location_types'] ?? ''),
            'categories' => trim($row['categories'] ?? ''),
        ];
    }

    /**
     * تحليل الإحداثيات
     */
    private function parseCoordinate($value): ?float
    {
        if (empty($value)) {
            return null;
        }

        $coordinate = (float) $value;

        // التحقق من صحة الإحداثيات
        if ($coordinate == 0) {
            return null;
        }

        return $coordinate;
    }

    /**
     * تحليل التقييم
     */
    private function parseRating($value): ?float
    {
        if (empty($value)) {
            return null;
        }

        $rating = (float) $value;

        // التأكد من أن التقييم بين 0 و 5
        if ($rating < 0 || $rating > 5) {
            return null;
        }

        return $rating;
    }

    /**
     * التحقق من صحة الرابط
     */
    private function validateUrl($url): ?string
    {
        if (empty($url)) {
            return null;
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        return $url;
    }

    /**
     * التحقق من صحة البريد الإلكتروني
     */
    private function validateEmail($email): ?string
    {
        if (empty($email)) {
            return null;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        return $email;
    }

    /**
     * البحث عن المحافظة أو إنشاؤها
     */
    private function findOrCreateGovernorate(array $row): Governorate
    {
        $governorateName = $row['governorate'];
        $governorateNameAr = $row['governorate_ar'] ?: $governorateName;

        // إنشاء كود فريد للمحافظة
        $code = strtoupper(substr($governorateName, 0, 2)) . rand(10, 99);

        // التأكد من أن الكود فريد
        while (Governorate::where('code', $code)->exists()) {
            $code = strtoupper(substr($governorateName, 0, 2)) . rand(10, 99);
        }

        return Governorate::firstOrCreate(
            ['name' => $governorateName],
            [
                'name_ar' => $governorateNameAr,
                'code' => $code,
                'is_active' => true
            ]
        );
    }

    private function findOrCreateCity(Governorate $governorate, array $row): City
    {
        $cityName = $row['city'];
        $cityNameAr = $row['city_ar'] ?: $cityName;

        return City::firstOrCreate(
            [
                'governorate_id' => $governorate->id,
                'name' => $cityName,
            ],
            [
                'name_ar' => $cityNameAr,
                'is_active' => true,
            ]
        );
    }

    /**
     * البحث عن نوع السياحة أو إنشاؤه
     */
    private function findOrCreateTourismType(array $row): TourismType
    {
        $tourismTypeName = $row['tourism_type'];
        $tourismTypeNameAr = $row['tourism_type_ar'] ?: $tourismTypeName;

        return TourismType::firstOrCreate(
            ['name' => $tourismTypeName],
            [
                'name_ar' => $tourismTypeNameAr,
                'is_active' => true
            ]
        );
    }

    /**
     * ربط أنواع المواقع
     */
    private function attachLocationTypes(Location $location, string $locationTypesString): void
    {
        $locationTypeNames = array_map('trim', explode(',', $locationTypesString));
        $locationTypeIds = [];

        foreach ($locationTypeNames as $name) {
            if (empty($name)) continue;

            $type = LocationType::firstOrCreate(
                ['name' => $name],
                [
                    'name_ar' => $name,
                    'is_active' => true
                ]
            );
            $locationTypeIds[] = $type->id;
        }

        if (!empty($locationTypeIds)) {
            $location->locationTypes()->sync($locationTypeIds);
        }
    }

    /**
     * ربط الفئات
     */
    private function attachCategories(Location $location, string $categoriesString): void
    {
        $categoryNames = array_map('trim', explode(',', $categoriesString));
        $categoryIds = [];

        foreach ($categoryNames as $name) {
            if (empty($name)) continue;

            $category = Category::firstOrCreate(
                ['name' => $name, 'type' => 'locations'],
                [
                    'name_ar' => $name,
                    'is_active' => true
                ]
            );
            $categoryIds[] = $category->id;
        }

        if (!empty($categoryIds)) {
            $location->categories()->sync($categoryIds);
        }
    }

    /**
     * قواعد التحقق
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'email' => 'nullable|email',
            'website' => 'nullable|url',
            'rating' => 'nullable|numeric|between:0,5',
        ];
    }

    /**
     * رسائل التحقق المخصصة
     */
    public function customValidationMessages(): array
    {
        return [
            'name.required' => 'اسم الموقع مطلوب',
            'name_ar.required' => 'اسم الموقع بالعربية مطلوب',
            'latitude.between' => 'خط العرض يجب أن يكون بين -90 و 90',
            'longitude.between' => 'خط الطول يجب أن يكون بين -180 و 180',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'website.url' => 'رابط الموقع غير صحيح',
            'rating.between' => 'التقييم يجب أن يكون بين 0 و 5',
        ];
    }

    /**
     * التعامل مع الأخطاء
     */
    public function onError(Throwable $e): void
    {
        $this->errors[] = $e->getMessage();
        Log::error('Import error: ' . $e->getMessage());
    }

    /**
     * الحصول على الأخطاء
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
