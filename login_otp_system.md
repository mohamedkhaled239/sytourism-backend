# نظام OTP لتسجيل الدخول

## التغييرات التي تمت:

1. **إضافة حقول جديدة لقاعدة البيانات:**
   - `login_otp`: رمز OTP مكون من 6 أرقام
   - `login_otp_expires`: تاريخ انتهاء صلاحية الرمز

2. **تعديل عملية تسجيل الدخول:**
   - بدلاً من إرجاع token مباشرة، يتم إرسال OTP للبريد الإلكتروني
   - المستخدم يحتاج لإدخال OTP للحصول على token

3. **إضافة endpoint جديد:**
   - `/verify-login-otp`: للتحقق من OTP وإكمال تسجيل الدخول

## كيفية الاستخدام:

### الخطوة 1: تسجيل الدخول (إرسال OTP)
```http
POST {{base_url}}/login
Content-Type: application/json

{
    "email": "samuaeladel3@gmail.com",
    "password": "password123"
}
```

**الاستجابة المتوقعة:**
```json
{
    "success": false,
    "message": "تم إرسال رمز تسجيل الدخول إلى بريدك الإلكتروني",
    "requires_otp": true,
    "email": "samuaeladel3@gmail.com"
}
```

### الخطوة 2: التحقق من OTP وإكمال تسجيل الدخول
```http
POST {{base_url}}/verify-login-otp
Content-Type: application/json

{
    "email": "samuaeladel3@gmail.com",
    "otp": "574218"
}
```

**الاستجابة المتوقعة:**
```json
{
    "success": true,
    "message": "تم تسجيل الدخول بنجاح",
    "data": {
        "user": {
            "id": 1,
            "full_name": "أحمد محمد",
            "email": "samuaeladel3@gmail.com",
            ...
        },
        "token": "1|abc123..."
    }
}
```

### الخطوة 3 (اختيارية): إعادة إرسال OTP
```http
POST {{base_url}}/resend-login-otp
Content-Type: application/json

{
    "email": "samuaeladel3@gmail.com"
}
```

**الاستجابة المتوقعة:**
```json
{
    "success": true,
    "message": "تم إعادة إرسال رمز تسجيل الدخول إلى بريدك الإلكتروني"
}
```

## مثال للاختبار:

يمكنك الآن اختبار النظام باستخدام:
- **Email**: `samuaeladel3@gmail.com`
- **Password**: `password123` (أو كلمة المرور الصحيحة)
- **OTP**: `574218` (الرمز الذي تم إنشاؤه في قاعدة البيانات)

## ملاحظات مهمة:

1. **صلاحية OTP**: 10 دقائق فقط
2. **طول OTP**: 6 أرقام بالضبط
3. **استخدام واحد**: يتم حذف OTP بعد الاستخدام
4. **أمان إضافي**: حتى لو تم اختراق كلمة المرور، المهاجم يحتاج للوصول للبريد الإلكتروني

## تدفق العمل الكامل:

1. المستخدم يدخل email و password
2. النظام يتحقق من صحة البيانات
3. إذا كانت صحيحة، يتم إنشاء OTP وإرساله للبريد الإلكتروني
4. المستخدم يدخل OTP
5. النظام يتحقق من OTP وصلاحيته
6. إذا كان صحيح، يتم إنشاء token وإرجاعه للمستخدم

## الأمان:

- **Two-Factor Authentication**: طبقة أمان إضافية
- **Time-based**: OTP صالح لفترة محدودة
- **Single-use**: لا يمكن استخدام نفس OTP مرتين
- **Email verification**: يتطلب الوصول للبريد الإلكتروني
