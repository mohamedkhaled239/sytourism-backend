<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - خريطة السياحة العامة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
        }
        .main-header {
            background: #3E5828;
            color: white;
            padding: 15px 30px;
            display: flex;
            align-items: center;
        }
        .main-header img {
            width: 40px;
            height: 40px;
            margin-left: 15px;
        }
        .card {
            border: none;
            box-shadow: 0 2px 20px rgba(0,0,0,0.08);
            border-radius: 12px;
            transition: transform 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .btn-primary {
            background: #3E5828;
            border: none;
            border-radius: 8px;
            padding: 10px 25px;
        }
        .btn-primary:hover {
            background: #8A6B4E;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="main-header mb-4">
        <img src="{{ asset('images/logo.png') }}" alt="وزارة السياحة السورية">
        <h3 class="m-0">خريطة السياحة العامة</h3>
    </div>

    <div class="container">
        @yield('content')
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@stack('scripts')
</body>
</html>
