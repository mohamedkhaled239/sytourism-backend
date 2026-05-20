<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - لوحة التحكم</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
        }
        .sidebar {
            min-height: 100vh;
            background: #3E5828;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.82);
            padding: 12px 20px;
            margin: 5px 10px;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        .sidebar .nav-link.active {
            background: #8A6B4E;
            color: white;
        }
        .main-header {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 15px 30px;
        }
        .card {
            border: none;
            box-shadow: 0 2px 20px rgba(0,0,0,0.08);
            border-radius: 12px;
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
        .btn-success {
            background: #3E5828;
            border: none;
            border-radius: 8px;
        }
        .btn-warning {
            background: linear-gradient(135deg, #8B4513 0%, #654321 100%);
            border: none;
            border-radius: 8px;
        }
        .badge {
            padding: 6px 12px;
            border-radius: 6px;
        }
        .stat-card {
            background: #3E5828;
            color: white;
            border-radius: 12px;
            padding: 25px;
            border-left: 4px solid #8A6B4E;
            height: 100%;
        }
    </style>
    @stack('styles')
</head>
@php($currentAdmin = auth()->guard('admin')->user())
<body>
<div class="container-fluid">
    <div class="row">
        <nav class="col-md-2 sidebar p-0">
            <div class="text-center py-4">
                <img src="{{ asset('images/logo.png') }}" alt="وزارة السياحة" style="width: 80px; height: 80px; margin-bottom: 10px;">
                <h4 class="text-white">لوحة التحكم</h4>
                <small class="text-white-50">{{ $currentAdmin->accountTypeLabel() }}</small>
                @if($currentAdmin->governorate)
                    <div class="mt-2">
                        <span class="badge bg-light text-dark">{{ $currentAdmin->governorate->name_ar }}</span>
                    </div>
                @endif
            </div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-home me-2"></i> الرئيسية
                    </a>
                </li>

                @if(!$currentAdmin->isDataEntry() && $currentAdmin->hasPermission('users'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                            <i class="fas fa-users me-2"></i> المستخدمين
                        </a>
                    </li>
                @endif

                @if(!$currentAdmin->isDataEntry() && $currentAdmin->hasPermission('news'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.news.*') ? 'active' : '' }}" href="{{ route('admin.news.index') }}">
                            <i class="fas fa-newspaper me-2"></i> الأخبار
                        </a>
                    </li>
                @endif

                @if(!$currentAdmin->isDataEntry() && $currentAdmin->hasPermission('events'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.events.*') ? 'active' : '' }}" href="{{ route('admin.events.index') }}">
                            <i class="fas fa-calendar-alt me-2"></i> الأحداث
                        </a>
                    </li>
                @endif

                @if(!$currentAdmin->isDataEntry() && $currentAdmin->hasPermission('categories'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index', ['type' => 'all']) }}">
                            <i class="fas fa-tags me-2"></i> التصنيفات
                        </a>
                    </li>
                @endif

                @if(!$currentAdmin->isDataEntry() && $currentAdmin->hasPermission('investments'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.investments.*') ? 'active' : '' }}" href="{{ route('admin.investments.index') }}">
                            <i class="fas fa-chart-line me-2"></i> الاستثمارات
                        </a>
                    </li>
                @endif

                @if($currentAdmin->hasPermission('locations') || $currentAdmin->isDataEntry() || $currentAdmin->tourism_type_id)
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.locations.*') ? 'active' : '' }}" href="{{ route('admin.locations.index') }}">
                            <i class="fas fa-map-marker-alt me-2"></i> المواقع
                        </a>
                    </li>
                @endif

                @if($currentAdmin->hasPermission('map') || $currentAdmin->isDataEntry() || $currentAdmin->tourism_type_id)
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.map.*') ? 'active' : '' }}" href="{{ route('admin.map.index') }}">
                            <i class="fas fa-globe me-2"></i> خريطة المواقع
                        </a>
                    </li>
                @endif

                @if(!$currentAdmin->isDataEntry() && $currentAdmin->hasPermission('settings'))
                    <li class="nav-item mt-3">
                        <h6 class="text-white-50 px-3 mb-2">الإعدادات</h6>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.governorates.*') ? 'active' : '' }}" href="{{ route('admin.governorates.index') }}">
                            <i class="fas fa-map me-2"></i> المحافظات
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.cities.*') ? 'active' : '' }}" href="{{ route('admin.cities.index') }}">
                            <i class="fas fa-city me-2"></i> المدن
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.tourism-types.*') ? 'active' : '' }}" href="{{ route('admin.tourism-types.index') }}">
                            <i class="fas fa-route me-2"></i> أنواع السياحة
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.location-types.*') ? 'active' : '' }}" href="{{ route('admin.location-types.index') }}">
                            <i class="fas fa-building me-2"></i> أنواع المواقع
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.admins.*') ? 'active' : '' }}" href="{{ route('admin.admins.index') }}">
                            <i class="fas fa-user-shield me-2"></i> الحسابات الإدارية
                        </a>
                    </li>
                @endif

                <li class="nav-item">
                    <form action="{{ route('admin.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="nav-link w-100 text-start border-0 bg-transparent">
                            <i class="fas fa-sign-out-alt me-2"></i> تسجيل الخروج
                        </button>
                    </form>
                </li>
            </ul>
        </nav>

        <main class="col-md-10 ms-sm-auto px-0">
            <div class="main-header mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-1">@yield('title')</h2>
                        @if($currentAdmin->governorate)
                            <small class="text-muted">عرض البيانات الخاصة بمحافظة {{ $currentAdmin->governorate->name_ar }}</small>
                        @endif
                    </div>
                    <div>
                        <span class="text-muted">مرحبًا، {{ $currentAdmin->name }}</span>
                    </div>
                </div>
            </div>

            <div class="px-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@stack('scripts')
</body>
</html>
