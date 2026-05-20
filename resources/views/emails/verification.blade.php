{{-- resources/views/emails/verification.blade.php --}}
    <!DOCTYPE html>
<html dir="rtl">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            max-width: 600px;
            margin: 0 auto;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 2px solid #667eea;
            margin-bottom: 30px;
        }
        .code-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            font-size: 32px;
            letter-spacing: 5px;
            margin: 30px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>مرحباً {{ $user->full_name }}</h1>
        <p>شكراً لتسجيلك في منصتنا</p>
    </div>

    <p>لتأكيد بريدك الإلكتروني، يرجى استخدام الرمز التالي:</p>

    <div class="code-box">
        {{ $code }}
    </div>

    <p>هذا الرمز صالح لمدة 60 دقيقة.</p>

    <div class="footer">
        <p>إذا لم تقم بإنشاء حساب، يرجى تجاهل هذا البريد.</p>
    </div>
</div>
</body>
</html>
