# نظام التحقق من البريد الإلكتروني المحدث - SEAHA2

## نظرة عامة

تم تحديث نظام المصادقة في تطبيق SEAHA2 ليشمل تحقق إجباري من البريد الإلكتروني مع ميزات أمان محسنة.

## الميزات الجديدة

### 1. التحقق الإجباري من البريد الإلكتروني
- **منع تسجيل الدخول**: لا يمكن للمستخدمين تسجيل الدخول حتى يتم تأكيد بريدهم الإلكتروني
- **إرسال رمز تلقائي**: عند محاولة تسجيل الدخول بدون تحقق، يتم إرسال رمز تحقق جديد تلقائياً

### 2. إعادة إرسال رمز التحقق
- **حد الإرسال**: حد أقصى 3 مرات في الساعة الواحدة
- **انتهاء الصلاحية**: كل رمز صالح لمدة 15 دقيقة فقط
- **رسائل واضحة**: رسائل خطأ مفصلة مع أكواد خطأ محددة

### 3. تتبع النشاط
- **آخر تسجيل دخول**: تسجيل وقت آخر تسجيل دخول ناجح
- **إدارة الجلسات**: تحسين إدارة tokens و sessions

## API Endpoints

### التسجيل
```http
POST /api/auth/register
Content-Type: application/json

{
    "full_name": "اسم المستخدم",
    "username": "username",
    "email": "user@example.com",
    "phone": "1234567890",
    "country": "السعودية",
    "user_type": "tourist",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**استجابة ناجحة (201):**
```json
{
    "success": true,
    "message": "تم التسجيل بنجاح. يرجى تأكيد بريدك الإلكتروني",
    "data": {
        "user": { ... },
        "verification_sent": true,
        "expires_in": 15
    }
}
```

### إعادة إرسال رمز التحقق
```http
POST /api/auth/resend-email-verification
Content-Type: application/json

{
    "email": "user@example.com"
}
```

**استجابة ناجحة (200):**
```json
{
    "success": true,
    "message": "تم إعادة إرسال رمز التحقق إلى بريدك الإلكتروني",
    "data": {
        "email": "user@example.com",
        "expires_in": 15,
        "remaining_attempts": 2
    }
}
```

**تجاوز الحد الأقصى (429):**
```json
{
    "success": false,
    "message": "تم تجاوز الحد الأقصى لإعادة الإرسال. يرجى المحاولة بعد ساعة",
    "error_code": "RESEND_LIMIT_EXCEEDED",
    "retry_after": 3600
}
```

### تأكيد البريد الإلكتروني
```http
POST /api/auth/verify-email
Content-Type: application/json

{
    "email": "user@example.com",
    "code": "123456"
}
```

**استجابة ناجحة (200):**
```json
{
    "success": true,
    "message": "تم تأكيد البريد الإلكتروني بنجاح",
    "data": {
        "user": { ... },
        "token": "auth_token_here"
    }
}
```

### تسجيل الدخول
```http
POST /api/auth/login
Content-Type: application/json

{
    "email": "user@example.com",
    "password": "password123"
}
```

**بريد غير مؤكد (403):**
```json
{
    "success": false,
    "message": "يرجى تأكيد بريدك الإلكتروني. تم إرسال رمز تحقق جديد",
    "error_code": "EMAIL_NOT_VERIFIED",
    "requires_verification": true,
    "data": {
        "email": "user@example.com",
        "code_sent": true,
        "expires_in": 15
    }
}
```

**تسجيل دخول ناجح (200):**
```json
{
    "success": true,
    "message": "تم تسجيل الدخول بنجاح",
    "data": {
        "user": { ... },
        "token": "auth_token_here"
    }
}
```

## أكواد الخطأ

| كود الخطأ | الوصف |
|-----------|--------|
| `EMAIL_ALREADY_VERIFIED` | البريد الإلكتروني مؤكد بالفعل |
| `RESEND_LIMIT_EXCEEDED` | تم تجاوز الحد الأقصى لإعادة الإرسال |
| `EMAIL_SEND_FAILED` | فشل في إرسال البريد الإلكتروني |
| `EMAIL_NOT_FOUND` | البريد الإلكتروني غير موجود |
| `INVALID_VERIFICATION_CODE` | رمز التحقق غير صحيح |
| `VERIFICATION_CODE_EXPIRED` | رمز التحقق منتهي الصلاحية |
| `EMAIL_NOT_VERIFIED` | البريد الإلكتروني غير مؤكد |
| `INVALID_CREDENTIALS` | بيانات الدخول غير صحيحة |

## قاعدة البيانات

### حقول جديدة في جدول users:
- `email_verification_code_expires`: وقت انتهاء صلاحية رمز التحقق
- `last_login_at`: وقت آخر تسجيل دخول ناجح

### Migration:
```bash
php artisan migrate
```

## الأمان

### Rate Limiting
- **إعادة الإرسال**: 3 مرات كحد أقصى في الساعة لكل مستخدم
- **انتهاء الصلاحية**: رموز التحقق تنتهي صلاحيتها خلال 15 دقيقة
- **تنظيف تلقائي**: مسح عداد إعادة الإرسال عند التحقق الناجح

### حماية البيانات
- رموز التحقق مخفية في API responses
- تشفير كلمات المرور باستخدام bcrypt
- استخدام Sanctum للمصادقة

## الاختبار

### تشغيل ملف الاختبار:
```bash
# من المتصفح
http://localhost/seaha2/test_auth_api.php

# من Terminal
php test_auth_api.php
```

### اختبار يدوي:
1. سجل مستخدم جديد
2. حاول تسجيل الدخول (سيفشل ويرسل رمز)
3. تحقق من جدول users للحصول على الرمز
4. أكد البريد باستخدام الرمز
5. سجل الدخول مرة أخرى (سينجح)

## استكشاف الأخطاء

### مشاكل شائعة:

1. **فشل إرسال البريد الإلكتروني:**
   - تحقق من إعدادات SMTP في `.env`
   - راجع logs في `storage/logs/laravel.log`

2. **رمز التحقق لا يعمل:**
   - تأكد من أن الرمز لم ينته صلاحيته (15 دقيقة)
   - تحقق من جدول users للرمز الصحيح

3. **تجاوز حد الإرسال:**
   - انتظر ساعة كاملة أو امسح cache يدوياً:
   ```bash
   php artisan cache:clear
   ```

### Logs مفيدة:
```bash
# عرض آخر 50 سطر من logs
tail -n 50 storage/logs/laravel.log

# متابعة logs في الوقت الفعلي
tail -f storage/logs/laravel.log
```

## التطوير المستقبلي

### ميزات مقترحة:
- [ ] إعدادات مخصصة لمدة انتهاء الرمز
- [ ] إشعارات push للتحقق
- [ ] تحقق بخطوتين (2FA)
- [ ] تسجيل دخول بالرقم أو البريد
- [ ] إحصائيات أمان للمدراء

---

**ملاحظة:** هذا النظام جزء من مشروع SEAHA2 لإدارة السياحة، وقد تم تطويره ليتماشى مع أفضل ممارسات الأمان الحديثة.
