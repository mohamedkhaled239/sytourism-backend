<?php

use App\Helpers\SessionHelper;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin;

// Route للاختبار
Route::get('/', function () {
    return redirect()->route('admin.login');
});

// Public Map Routes
Route::get('/public-map', [\App\Http\Controllers\PublicMapController::class, 'index'])->name('public.map.index');
Route::get('/public-map/search', [\App\Http\Controllers\PublicMapController::class, 'search'])->name('public.map.search');
Route::get('/public-map/stats', [\App\Http\Controllers\PublicMapController::class, 'getStats'])->name('public.map.stats');
Route::get('/public-map/{id}', [\App\Http\Controllers\PublicMapController::class, 'show'])->name('public.map.show');

// Investor Routes
Route::group(['prefix' => 'investor', 'as' => 'investor.'], function() {
    Route::get('/login', [\App\Http\Controllers\InvestorController::class, 'showLoginForm'])->name('login');
    Route::get('/register', [\App\Http\Controllers\InvestorController::class, 'showRegisterForm'])->name('register');
    Route::get('/verify-email', [\App\Http\Controllers\InvestorController::class, 'showVerifyEmailForm'])->name('verify-email');
    Route::post('/login', [\App\Http\Controllers\InvestorController::class, 'login']);

    Route::group(['middleware' => ['auth:web']], function() {
        Route::get('/dashboard', [\App\Http\Controllers\InvestorController::class, 'dashboard'])->name('dashboard');
        Route::get('/locations/{location}', [\App\Http\Controllers\InvestorController::class, 'showLocation'])->name('locations.show');
        Route::get('/investments', [\App\Http\Controllers\InvestorController::class, 'investments'])->name('investments.index');
        Route::post('/logout', [\App\Http\Controllers\InvestorController::class, 'logout'])->name('logout');
    });
});

// Route لفحص حالة الجلسة والتطبيق
Route::get('/debug-session', function () {
    $sessionData = [
        'session_id' => session()->getId(),
        'session_driver' => config('session.driver'),
        'session_lifetime' => config('session.lifetime'),
        'session_path' => config('session.files'),
        'app_env' => env('APP_ENV'),
        'app_debug' => env('APP_DEBUG'),
        'session_data' => session()->all(),
        'cookies' => request()->cookies->all(),
        'auth_admin' => Auth::guard('admin')->check(),
        'auth_user' => Auth::guard('web')->check(),
        'storage_writable' => is_writable(storage_path('framework/sessions')),
        'session_file_exists' => file_exists(storage_path('framework/sessions/' . session()->getId())),
    ];

    return response()->json($sessionData, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
});

// Route للوصول المباشر للوحة التحكم (تجاوز مشاكل الجلسة)
Route::get('/admin-direct', function () {
    // تسجيل دخول تلقائي للأدمن الافتراضي
    $adminEmail = env('ADMIN_EMAIL', 'samuaeladel3@gmail.com');
    $admin = \App\Models\Admin::where('email', $adminEmail)->first();

    if ($admin) {
        Auth::guard('admin')->login($admin);
        return redirect()->route('admin.dashboard')->with('success', 'تم تسجيل الدخول بنجاح');
    }

    return redirect()->route('admin.login')->with('error', 'لم يتم العثور على حساب الأدمن');
});

// Route لمسح الجلسة والـ cookies
Route::get('/clear-session', function () {
    // مسح جميع بيانات الجلسة
    session()->flush();
    session()->invalidate();
    session()->regenerateToken();

    // إنشاء response
    $response = redirect()->route('admin.login')->with('success', 'تم مسح الجلسة بنجاح');

    // مسح الـ cookies الشائعة
    $cookiesToClear = [
        'laravel_session',
        'XSRF-TOKEN',
        'remember_web',
        'remember_admin',
        session()->getName(),
    ];

    foreach ($cookiesToClear as $cookieName) {
        $response->withCookie(cookie()->forget($cookieName));
        $response->withCookie(cookie()->forget($cookieName, '/'));
        $response->withCookie(cookie()->forget($cookieName, '/admin'));
    }

    return $response;
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return Auth::guard('admin')->check()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('admin.login');
    });
    // Guest routes
    Route::middleware('admin.guest')->group(function () {
        Route::get('/login', [Admin\AuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [Admin\AuthController::class, 'login']);
    });

    // Protected routes
    Route::middleware('auth:admin')->group(function () {
        Route::pattern('location', '[0-9]+');
        Route::post('/logout', [Admin\AuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

        // Users
        Route::resource('users', Admin\UserController::class)->only(['index', 'show', 'destroy']);
        Route::get('users/export', [Admin\UserController::class, 'export'])->name('users.export');
        Route::post('users/{user}/approve-investor', [Admin\UserController::class, 'approveInvestor'])->name('users.approve-investor');

        // News
        Route::resource('news', Admin\NewsController::class);

        // Events
        Route::resource('events', Admin\EventController::class);

        // Investments
        Route::resource('investments', Admin\InvestmentController::class);

        // Locations
        Route::get('locations/template', [Admin\LocationController::class, 'template'])->name('locations.template');
        Route::get('locations/export', [Admin\LocationController::class, 'export'])->name('locations.export');
        Route::resource('locations', Admin\LocationController::class);
        Route::post('locations/import', [Admin\LocationController::class, 'import'])->name('locations.import');
        Route::delete('locations/{location}/images/{image}', [Admin\LocationController::class, 'deleteImage'])->name('locations.delete-image');

        // Categories (General)
        Route::resource('categories', Admin\CategoryController::class);

        // Settings
        Route::resource('governorates', Admin\GovernorateController::class);
        Route::resource('cities', Admin\CityController::class);
        Route::resource('tourism-types', Admin\TourismTypeController::class);
        Route::resource('location-types', Admin\LocationTypeController::class);
        Route::resource('admins', Admin\AdminController::class);

        // Map
        Route::get('map', [Admin\MapController::class, 'index'])->name('map.index');
        Route::get('map', [Admin\MapController::class, 'index'])->name('map.index');
        Route::get('map/search', [Admin\MapController::class, 'search'])->name('map.search');
        Route::get('map/stats', [Admin\MapController::class, 'getStats'])->name('map.stats');
        Route::get('map/{location}', [Admin\MapController::class, 'show'])->name('map.show');
    });
});

// Fallback route - إعادة توجيه أي مسار غير موجود إلى صفحة تسجيل دخول الأدمن
// تعطيل مؤقتاً لحل مشكلة السيرفر
Route::fallback(function () {
    $request = request();

    // التأكد من أن الطلب ليس API request أو مسارات محددة
    if ($request->is('api/*') ||
        $request->expectsJson() ||
        $request->is('admin/login') ||
        $request->is('clear-session') ||
        $request->is('test-*') ||
        $request->is('debug_*') ||
        str_ends_with($request->path(), '.php') ||
        $request->header('Accept') === 'application/json' ||
        str_contains($request->header('Accept', ''), 'application/json')) {
        abort(404);
    }

    // تعطيل إعادة التوجيه التلقائي في بيئة الإنتاج مؤقتاً
    if (env('APP_ENV') !== 'local') {
        abort(404);
    }

    return SessionHelper::clearSessionAndRedirectToAdminLogin($request);
});
