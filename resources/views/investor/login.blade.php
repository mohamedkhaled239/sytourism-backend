<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بوابة المستثمرين - تسجيل الدخول</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; background: linear-gradient(180deg, #f4f7ef 0%, #ffffff 100%); }
        .login-container { max-width: 430px; margin: 80px auto; background: white; padding: 32px; border-radius: 16px; box-shadow: 0 18px 40px rgba(62, 88, 40, 0.12); }
        .btn-primary { background: #3E5828; border: none; }
        .btn-primary:hover { background: #31471f; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="text-center mb-4">
            <img src="{{ asset('images/logo.png') }}" alt="وزارة السياحة" style="width: 82px; height: 82px; margin-bottom: 12px;">
            <h4 class="mb-1">بوابة المستثمرين</h4>
            <p class="text-muted mb-0">سجّل الدخول إلى حساب المستثمر المعتمد</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('investor.login') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">البريد الإلكتروني</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">كلمة المرور</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">تسجيل الدخول</button>
        </form>

        <div class="text-center mt-3">
            <span class="text-muted">ليس لديك حساب؟</span>
            <a href="{{ route('investor.register') }}" class="text-decoration-none">إنشاء حساب مستثمر</a>
        </div>
    </div>
</body>
</html>
