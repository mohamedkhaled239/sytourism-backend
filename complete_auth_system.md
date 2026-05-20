# نظام المصادقة الكامل - دليل شامل

## جميع الـ APIs المتاحة:

### 1. **إنشاء حساب جديد**
```http
POST {{base_url}}/register
```

### 2. **تأكيد البريد الإلكتروني**
```http
POST {{base_url}}/verify-email
```

### 3. **إعادة إرسال رمز التحقق من البريد**
```http
POST {{base_url}}/resend-email-verification
```

### 4. **تسجيل الدخول (إرسال OTP)**
```http
POST {{base_url}}/login
```

### 5. **التحقق من OTP وإكمال تسجيل الدخول**
```http
POST {{base_url}}/verify-login-otp
```

### 6. **إعادة إرسال OTP تسجيل الدخول**
```http
POST {{base_url}}/resend-login-otp
```

### 7. **نسيان كلمة المرور**
```http
POST {{base_url}}/forgot-password
```

### 8. **إعادة تعيين كلمة المرور**
```http
POST {{base_url}}/reset-password
```

---

## التدفق الكامل للمستخدم الجديد:

### المرحلة 1: إنشاء الحساب
```json
POST /register
{
    "full_name": "مستخدم تجريبي",
    "username": "testuser123",
    "email": "test@example.com",
    "phone": "01111111111",
    "country": "مصر",
    "user_type": "tourist",
    "password": "password123",
    "password_confirmation": "password123"
}
```

### المرحلة 2: تأكيد البريد الإلكتروني
```json
POST /verify-email
{
    "email": "test@example.com",
    "code": "oWzE2W"
}
```

**أو إعادة إرسال الرمز إذا لم يصل:**
```json
POST /resend-email-verification
{
    "email": "test@example.com"
}
```

### المرحلة 3: تسجيل الدخول
```json
POST /login
{
    "email": "test@example.com",
    "password": "password123"
}
```

### المرحلة 4: التحقق من OTP
```json
POST /verify-login-otp
{
    "email": "test@example.com",
    "otp": "574218"
}
```

**أو إعادة إرسال OTP إذا لم يصل:**
```json
POST /resend-login-otp
{
    "email": "test@example.com"
}
```

---

## إعادة تعيين كلمة المرور:

### الخطوة 1: طلب رمز إعادة التعيين
```json
POST /forgot-password
{
    "email": "test@example.com"
}
```

### الخطوة 2: إعادة تعيين كلمة المرور
```json
POST /reset-password
{
    "token": "472271",
    "password": "newpassword123",
    "password_confirmation": "newpassword123"
}
```

---

## بيانات الاختبار الحالية:

### المستخدم الأول (مؤكد):
- **Email**: `samuaeladel3@gmail.com`
- **Password**: `password123`
- **Login OTP**: `574218`
- **Reset Token**: `472271`

### المستخدم الثاني (غير مؤكد):
- **Email**: `test@example.com`
- **Password**: `password123`
- **Verification Code**: `oWzE2W`

---

## الميزات الأمنية:

1. **Two-Factor Authentication**: OTP عند تسجيل الدخول
2. **Email Verification**: تأكيد البريد الإلكتروني إجباري
3. **Password Reset**: رمز 6 أرقام لإعادة تعيين كلمة المرور
4. **Time-based Tokens**: جميع الرموز لها صلاحية محدودة
5. **Single-use Tokens**: كل رمز يُستخدم مرة واحدة فقط

---

## صلاحية الرموز:

- **رمز التحقق من البريد**: بدون انتهاء صلاحية (حتى التأكيد)
- **OTP تسجيل الدخول**: 10 دقائق
- **رمز إعادة تعيين كلمة المرور**: ساعتين

---

## الاستجابات المعيارية:

### نجح:
```json
{
    "success": true,
    "message": "رسالة النجح",
    "data": {...}
}
```

### فشل:
```json
{
    "success": false,
    "message": "رسالة الخطأ"
}
```

### يتطلب إجراء إضافي:
```json
{
    "success": false,
    "message": "رسالة توضيحية",
    "requires_verification": true,
    "requires_otp": true
}
```
