# حل مشكلة إعادة التوجيه على السيرفر

## المشكلة
على السيرفر، عند الضغط على أي رابط يتم إعادة التوجيه تلقائياً لصفحة تسجيل الدخول، بينما محلياً كل شيء يعمل بشكل طبيعي.

## الحلول المطبقة

### 1. الحل السريع - تعطيل إعادة التوجيه التلقائي
تم تعديل الملفات التالية لتعطيل إعادة التوجيه التلقائي في بيئة الإنتاج:

- `app/Exceptions/Handler.php` - تعطيل إعادة التوجيه في Exception Handler
- `routes/web.php` - تعطيل Fallback Route في بيئة الإنتاج
- `app/Helpers/SessionHelper.php` - إضافة فحص للبيئة قبل إعادة التوجيه

### 2. Routes جديدة للتشخيص والحل
تم إضافة routes جديدة:

#### `/debug-session`
لفحص حالة الجلسة والتطبيق:
```
http://your-domain.com/debug-session
```

#### `/admin-direct`
للوصول المباشر لوحة التحكم (تجاوز مشاكل الجلسة):
```
http://your-domain.com/admin-direct
```

#### `/clear-session`
لمسح الجلسة والـ cookies:
```
http://your-domain.com/clear-session
```

### 3. إعدادات البيئة الجديدة
تم إضافة إعدادات جديدة في `.env`:

```env
DISABLE_AUTO_REDIRECT=false
FORCE_ADMIN_LOGIN=false
SESSION_SECURE_COOKIE=false
```

## خطوات التطبيق على السيرفر

### الخطوة 1: رفع الملفات المحدثة
ارفع الملفات المحدثة التالية على السيرفر:
- `app/Exceptions/Handler.php`
- `routes/web.php`
- `app/Helpers/SessionHelper.php`
- `app/Http/Middleware/DisableRedirectOnProduction.php`

### الخطوة 2: تحديث ملف البيئة
في ملف `.env` على السيرفر، أضف هذه الأسطر:

```env
# إعدادات خاصة بحل مشكلة السيرفر
DISABLE_AUTO_REDIRECT=true
FORCE_ADMIN_LOGIN=false
SESSION_SECURE_COOKIE=false
```

أو انسخ ملف `.env.production` واستخدمه كـ `.env` على السيرفر.

### الخطوة 3: مسح الـ Cache
نفذ الأوامر التالية على السيرفر:

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### الخطوة 4: فحص الصلاحيات
تأكد من أن مجلد `storage/framework/sessions` له صلاحيات الكتابة:

```bash
chmod -R 775 storage/
chown -R www-data:www-data storage/
```

### الخطوة 5: اختبار الحل
1. اذهب إلى `http://your-domain.com/debug-session` لفحص حالة الجلسة
2. اذهب إلى `http://your-domain.com/admin-direct` للوصول المباشر لوحة التحكم
3. اذهب إلى `http://your-domain.com/clear-session` لمسح الجلسة إذا لزم الأمر

## الحلول البديلة

### إذا لم تنجح الحلول السابقة:

#### 1. تغيير Session Driver
في `.env` على السيرفر:
```env
SESSION_DRIVER=database
```

ثم نفذ:
```bash
php artisan session:table
php artisan migrate
```

#### 2. تعطيل CSRF مؤقتاً
في `app/Http/Middleware/VerifyCsrfToken.php`:
```php
protected $except = [
    '*'
];
```

#### 3. إعادة تعيين Session Configuration
في `config/session.php`:
```php
'secure' => false,
'http_only' => true,
'same_site' => 'lax',
```

## ملاحظات مهمة

1. **الأمان**: هذه الحلول تعطل بعض آليات الحماية، لذا استخدمها مؤقتاً فقط
2. **البيئة**: الحلول مصممة للعمل في بيئة الإنتاج فقط، محلياً ستبقى الآليات الأصلية
3. **المراقبة**: راقب logs السيرفر لمعرفة أي أخطاء إضافية

## استعادة الوضع الأصلي

لاستعادة الوضع الأصلي، غير في `.env`:
```env
DISABLE_AUTO_REDIRECT=false
```

وأعد تشغيل:
```bash
php artisan config:clear
```
