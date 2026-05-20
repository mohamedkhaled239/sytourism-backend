# تحديث نظام الصور للمواقع

## التحديثات المضافة

### 1. قاعدة البيانات
- **إضافة عمود `main_image`** إلى جدول `locations` لتخزين الصورة الرئيسية للموقع
- **إنشاء جدول `location_images`** لتخزين الصور المتعددة للمواقع مع:
  - `location_id`: معرف الموقع
  - `image_path`: مسار الصورة
  - `caption`: وصف الصورة بالإنجليزية
  - `caption_ar`: وصف الصورة بالعربية
  - `order`: ترتيب الصورة
  - `is_active`: حالة نشاط الصورة

### 2. النماذج (Models)

#### Location Model
- إضافة `main_image` إلى `$fillable`
- إضافة `main_image_url` accessor للحصول على URL الصورة
- إضافة علاقات `images()` و `activeImages()` مع LocationImage

#### LocationImage Model
- إضافة جميع الحقول المطلوبة إلى `$fillable`
- إضافة `image_url` accessor للحصول على URL الصورة
- إضافة علاقة `location()` مع Location
- إضافة scopes `active()` و `ordered()`

### 3. واجهة الإدارة (Admin Interface)

#### LocationController
- **تحديث `store()`**: إضافة معالجة رفع الصورة الرئيسية والصور المتعددة
- **تحديث `update()`**: إضافة معالجة تحديث الصور مع حذف الصور القديمة
- **تحديث `destroy()`**: حذف جميع الصور عند حذف الموقع
- **إضافة `deleteImage()`**: حذف صورة محددة من الموقع

#### النماذج (Views)
- **create.blade.php**: إضافة حقول رفع الصورة الرئيسية والصور المتعددة
- **edit.blade.php**: إضافة عرض الصور الحالية وإمكانية حذفها وإضافة صور جديدة
- **index.blade.php**: إضافة عمود لعرض الصورة الرئيسية في الجدول

### 4. API

#### LocationController API
- تحديث جميع الدوال لتشمل `activeImages` في الاستجابة
- إضافة الصور في جميع endpoints:
  - `index()`: قائمة المواقع
  - `show()`: تفاصيل الموقع
  - `nearby()`: المواقع القريبة
  - `favorites()`: المواقع المفضلة
  - `search()`: البحث في المواقع

### 5. المسارات (Routes)
- إضافة route لحذف الصور: `DELETE /admin/locations/{location}/images/{image}`

## كيفية الاستخدام

### في الإدارة
1. **إضافة موقع جديد**: يمكن رفع صورة رئيسية وعدة صور إضافية مع وصف لكل صورة
2. **تعديل موقع**: يمكن عرض الصور الحالية وحذفها أو إضافة صور جديدة
3. **عرض المواقع**: يتم عرض الصورة الرئيسية في جدول المواقع

### في API
```json
{
  "id": 1,
  "name": "Location Name",
  "name_ar": "اسم الموقع",
  "main_image": "locations/main/image.jpg",
  "main_image_url": "http://example.com/storage/locations/main/image.jpg",
  "active_images": [
    {
      "id": 1,
      "image_path": "locations/gallery/image1.jpg",
      "image_url": "http://example.com/storage/locations/gallery/image1.jpg",
      "caption": "Image description",
      "caption_ar": "وصف الصورة",
      "order": 1
    }
  ]
}
```

## مجلدات التخزين
- `storage/app/public/locations/main/`: الصور الرئيسية
- `storage/app/public/locations/gallery/`: الصور الإضافية

## الملفات المحدثة
- `database/migrations/2025_08_23_005912_add_main_image_to_locations_table.php`
- `database/migrations/2025_08_23_005923_create_location_images_table.php`
- `app/Models/Location.php`
- `app/Models/LocationImage.php`
- `app/Http/Controllers/Admin/LocationController.php`
- `app/Http/Controllers/Api/LocationController.php`
- `resources/views/admin/locations/create.blade.php`
- `resources/views/admin/locations/edit.blade.php`
- `resources/views/admin/locations/index.blade.php`
- `routes/web.php`

## ملاحظات مهمة
- تم إضافة validation للصور (jpeg, png, jpg, gif) بحد أقصى 2MB
- يتم حذف الصور القديمة تلقائياً عند تحديثها
- الصور يتم ترتيبها حسب حقل `order`
- يمكن تفعيل/إلغاء تفعيل الصور الفردية
- جميع URLs الصور تكون كاملة ومطلقة (full URLs)
- تم إنشاء ImageHelper class لإدارة URLs الصور بشكل موحد

## الملفات المحدثة
- `database/migrations/2025_08_23_005912_add_main_image_to_locations_table.php`
- `database/migrations/2025_08_23_005923_create_location_images_table.php`
- `app/Models/Location.php`
- `app/Models/LocationImage.php`
- `app/Http/Controllers/Admin/LocationController.php`
- `app/Http/Controllers/Api/LocationController.php`
- `resources/views/admin/locations/create.blade.php`
- `resources/views/admin/locations/edit.blade.php`
- `resources/views/admin/locations/index.blade.php`
- `routes/web.php`
- `routes/api.php`
- `app/Helpers/ImageHelper.php` (جديد)
- `API_IMAGES_DOCUMENTATION.md` (جديد)
