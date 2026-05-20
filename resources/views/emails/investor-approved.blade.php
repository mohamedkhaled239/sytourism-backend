<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>الموافقة على حساب المستثمر</title>
</head>
<body style="font-family: Arial, sans-serif; background:#f7f7f7; margin:0; padding:24px;">
    <div style="max-width:600px; margin:0 auto; background:#ffffff; border-radius:12px; padding:32px;">
        <h2 style="margin-top:0; color:#3E5828;">تمت الموافقة على حسابك</h2>
        <p style="line-height:1.8; color:#333333;">مرحبًا {{ $user->full_name }}،</p>
        <p style="line-height:1.8; color:#333333;">
            تمت موافقة الإدارة على حساب المستثمر الخاص بك، ويمكنك الآن تسجيل الدخول واستخدام البوابة.
        </p>
        <p style="line-height:1.8; color:#333333;">البريد الإلكتروني: {{ $user->email }}</p>
        <p style="line-height:1.8; color:#333333; margin-bottom:0;">مع تحيات فريق المنصة.</p>
    </div>
</body>
</html>
