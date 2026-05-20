# حل مشكلة Domain الصور في API

## المشكلة الأصلية
كانت الـ URLs في API تعيد `localhost` بدلاً من الـ domain الحقيقي:
```json
{
  "main_image_url": "http://localhost/storage/locations/main/image.jpg"
}
```

## الحل المطبق

### 1. Middleware ديناميكي
تم إنشاء `SetImageDomain` middleware يضبط الـ domain تلقائياً حسب الطلب:

```php
// app/Http/Middleware/SetImageDomain.php
public function handle(Request $request, Closure $next): Response
{
    $scheme = $request->isSecure() ? 'https' : 'http';
    $host = $request->getHost();
    $domain = $scheme . '://' . $host;
    
    config(['app.image_domain' => $domain]);
    
    return $next($request);
}
```

### 2. ImageHelper محسن
تم تحديث `ImageHelper` ليدعم عدة مصادر للـ domain:

```php
public static function getImageUrl($imagePath)
{
    // 1. من config (يضبطه middleware)
    $baseUrl = config('app.image_domain');
    
    // 2. من request مباشرة
    if (!$baseUrl && request()) {
        $baseUrl = request()->getSchemeAndHttpHost();
    }
    
    // 3. fallback آمن
    if (!$baseUrl) {
        $baseUrl = 'https://darkslateblue-cobra-779637.hostingersite.com';
    }
    
    return $baseUrl . '/storage/' . $imagePath;
}
```

### 3. Config قابل للتخصيص
تم إضافة config جديد في `config/app.php`:

```php
'image_domain' => env('IMAGE_DOMAIN', 'https://darkslateblue-cobra-779637.hostingersite.com'),
```

## النتيجة

الآن الـ API يعيد URLs صحيحة:
```json
{
  "main_image_url": "https://darkslateblue-cobra-779637.hostingersite.com/storage/locations/main/image.jpg"
}
```

## المزايا

1. **ديناميكي**: يتكيف مع أي domain تلقائياً
2. **مرن**: يمكن تحديد domain ثابت عبر config
3. **آمن**: fallback في حالة عدم التوفر
4. **متوافق**: يعمل مع جميع البيئات

## الملفات المحدثة

- `app/Http/Middleware/SetImageDomain.php` (جديد)
- `app/Http/Kernel.php` - تسجيل middleware
- `config/app.php` - إضافة config
- `app/Helpers/ImageHelper.php` - تحسين المنطق

## الاختبار

تم اختبار الحل والتأكد من عمله:
```
URL للصورة: https://darkslateblue-cobra-779637.hostingersite.com/storage/locations/main/test.jpg
```

## الاستخدام

لا يحتاج أي إعداد إضافي - يعمل تلقائياً مع جميع طلبات API.
