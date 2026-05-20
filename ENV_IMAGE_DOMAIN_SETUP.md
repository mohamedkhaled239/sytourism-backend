# إعداد Domain الصور في API

## المشكلة
الـ URLs في API تستخدم `localhost` بدلاً من الـ domain الحقيقي للموقع.

## الحل
تم تطبيق حل متعدد المستويات لضمان أن URLs الصور تكون صحيحة:

1. **Middleware ديناميكي**: يضبط الـ domain تلقائياً حسب الطلب
2. **Config قابل للتخصيص**: يمكن تحديد domain ثابت
3. **Fallback آمن**: domain افتراضي في حالة عدم التوفر

## الإعداد

### 1. إضافة متغير البيئة
أضف السطر التالي إلى ملف `.env`:

```env
IMAGE_DOMAIN=https://darkslateblue-cobra-779637.hostingersite.com
```

### 2. التحقق من الإعداد
تأكد من أن الـ config مضبوط بشكل صحيح:

```php
// في tinker أو أي مكان في الكود
echo config('app.image_domain');
// يجب أن يطبع: https://darkslateblue-cobra-779637.hostingersite.com
```

### 3. اختبار API
بعد الإعداد، يجب أن تعطي API URLs صحيحة:

```json
{
  "main_image": "locations/main/image.jpg",
  "main_image_url": "https://darkslateblue-cobra-779637.hostingersite.com/storage/locations/main/image.jpg"
}
```

## الملفات المحدثة

1. `config/app.php` - إضافة config جديد
2. `app/Helpers/ImageHelper.php` - استخدام الـ config الجديد
3. `app/Http/Middleware/SetImageDomain.php` - middleware ديناميكي
4. `app/Http/Kernel.php` - تسجيل الـ middleware

## ملاحظات مهمة

- تأكد من أن الـ domain صحيح ومتاح
- تأكد من أن مجلد `storage` متاح عبر الـ web
- تأكد من وجود الرابط الرمزي: `php artisan storage:link`

## استكشاف الأخطاء

### إذا كانت الصور لا تظهر:
1. تحقق من `IMAGE_DOMAIN` في ملف `.env`
2. تأكد من وجود الرابط الرمزي
3. تحقق من صلاحيات الملفات

### إذا كان الـ domain خاطئ:
1. تحديث `IMAGE_DOMAIN` في ملف `.env`
2. مسح cache: `php artisan config:clear`
3. إعادة تشغيل الخادم
