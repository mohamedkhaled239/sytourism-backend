{{-- resources/views/admin/auth/login.blade.php --}}
    <!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل دخول الإدارة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #4A7C59 0%, #2F5233 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 400px;
        }
        .login-card h3 {
            color: #333;
            margin-bottom: 30px;
            text-align: center;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px;
            border: 1px solid #ddd;
        }
        .btn-login {
            background: linear-gradient(135deg, #4A7C59 0%, #2F5233 100%);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 12px;
            width: 100%;
            font-size: 16px;
            margin-top: 20px;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #2F5233 0%, #4A7C59 100%);
        }
    </style>
</head>
<body>
<div class="login-card">
    <div class="text-center mb-4">
        <img src="{{ asset('images/logo.png') }}" alt="وزارة السياحة السورية" style="width: 100px; height: 100px; margin-bottom: 20px;">
        <h3>تسجيل دخول الإدارة</h3>
        <p class="text-muted">وزارة السياحة السورية</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('admin.login') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">البريد الإلكتروني</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">كلمة المرور</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-login">تسجيل الدخول</button>
    </form>

    <div class="text-center mt-3">
        <small>
            <a href="/clear-session" style="color: #666; text-decoration: none;">
                واجهت مشكلة؟ امسح الجلسة والـ cookies
            </a>
        </small>
    </div>
</div>
</body>
</html>
