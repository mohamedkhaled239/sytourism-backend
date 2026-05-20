<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\InvestmentController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\GovernorateController;
use App\Http\Controllers\Api\TourismTypeController;
use App\Http\Controllers\Api\LocationTypeController;
use App\Http\Controllers\Api\MapController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\NotificationController;

// Public routes (فقط للتسجيل وتسجيل الدخول)
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/verify-email', [AuthController::class, 'verifyEmail']);
    Route::post('/resend-email-verification', [AuthController::class, 'resendEmailVerification']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/login/verify', [AuthController::class, 'verifyLoginOtp']);
    Route::post('/login/resend-otp', [AuthController::class, 'resendLoginOtp']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

// Protected routes - كل المحتوى يتطلب تسجيل دخول وتأكيد البريد الإلكتروني
Route::middleware(['auth:sanctum', 'api.verified'])->group(function () {

    // Auth Management
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
        Route::post('/toggle-notifications', [AuthController::class, 'toggleNotifications']);
        Route::delete('/delete-account', [AuthController::class, 'deleteAccount']);
    });

    // News Routes
    Route::prefix('news')->group(function () {
        Route::get('/', [NewsController::class, 'index']);
        Route::get('/latest', [NewsController::class, 'latest']);
        Route::get('/popular', [NewsController::class, 'popular']);
        Route::get('/search', [NewsController::class, 'search']);
        Route::get('/category/{categoryId}', [NewsController::class, 'byCategory']);
        Route::get('/{id}', [NewsController::class, 'show']);
        Route::get('/{id}/related', [NewsController::class, 'related']);
    });

    // Events Routes
    Route::prefix('events')->group(function () {
        Route::get('/', [EventController::class, 'index']);
        Route::get('/upcoming', [EventController::class, 'upcoming']);
        Route::get('/active', [EventController::class, 'active']);
        Route::get('/search', [EventController::class, 'search']);
        Route::get('/location/{locationId}', [EventController::class, 'byLocation']);
        Route::get('/category/{categoryId}', [EventController::class, 'byCategory']);
        Route::get('/{id}', [EventController::class, 'show']);

        // Favorites (للمستخدمين المسجلين فقط)
        Route::post('/{id}/favorite', [EventController::class, 'toggleFavorite']);
        Route::get('/user/favorites', [EventController::class, 'favorites']);
    });

    // Investments Routes (للمستثمرين فقط)
    Route::prefix('investments')->middleware('investor')->group(function () {
        Route::get('/', [InvestmentController::class, 'index']);
        Route::get('/search', [InvestmentController::class, 'search']);
        Route::get('/location/{locationId}', [InvestmentController::class, 'byLocation']);
        Route::get('/category/{categoryId}', [InvestmentController::class, 'byCategory']);
        Route::get('/{id}', [InvestmentController::class, 'show']);
    });

    // Locations Routes
    Route::prefix('locations')->group(function () {
        Route::get('/', [LocationController::class, 'index']);
        Route::get('/search', [LocationController::class, 'search']);
        Route::get('/nearby', [LocationController::class, 'nearby']);
        Route::get('/{id}', [LocationController::class, 'show']);
        Route::get('/{id}/images', [LocationController::class, 'images']);
        Route::get('/{id}/events', [LocationController::class, 'events']);
        Route::get('/{id}/investments', [LocationController::class, 'investments']);

        // Favorites
        Route::post('/{id}/favorite', [LocationController::class, 'toggleFavorite']);
        Route::get('/user/favorites', [LocationController::class, 'favorites']);
    });

    // Categories Routes
    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index']);
        Route::get('/type/{type}', [CategoryController::class, 'getByType']);
        Route::get('/{id}', [CategoryController::class, 'show']);
    });

    // Governorates Routes
    Route::prefix('governorates')->group(function () {
        Route::get('/', [GovernorateController::class, 'index']);
        Route::get('/{id}', [GovernorateController::class, 'show']);
        Route::get('/{id}/cities', [GovernorateController::class, 'cities']);
        Route::get('/{id}/locations', [GovernorateController::class, 'locations']);
    });

    // Tourism Types Routes
    Route::prefix('tourism-types')->group(function () {
        Route::get('/', [TourismTypeController::class, 'index']);
        Route::get('/{id}', [TourismTypeController::class, 'show']);
        Route::get('/{id}/locations', [TourismTypeController::class, 'locations']);
    });

    // Location Types Routes
    Route::prefix('location-types')->group(function () {
        Route::get('/', [LocationTypeController::class, 'index']);
        Route::get('/{id}', [LocationTypeController::class, 'show']);
        Route::get('/{id}/locations', [LocationTypeController::class, 'locations']);
    });

    // Map Routes
    Route::prefix('map')->group(function () {
        Route::get('/locations', [MapController::class, 'locations']);
        Route::get('/locations/{id}', [MapController::class, 'locationDetails']);
        Route::get('/stats', [MapController::class, 'stats']);
        Route::get('/search', [MapController::class, 'search']);
        Route::get('/cluster', [MapController::class, 'cluster']);
    });

    // Home (requires login)
    Route::get('/home', [HomeController::class, 'summary']);

    // Notifications Routes
    Route::prefix('notifications')->group(function () {
        Route::get('/latest', [NotificationController::class, 'getLatest']);
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/stats', [NotificationController::class, 'getStats']);
        Route::post('/fcm-token', [NotificationController::class, 'updateFcmToken']);
        Route::post('/onesignal-player', [NotificationController::class, 'updateOneSignalPlayerId']);
    });
});

// Note: Removed problematic fallback route that was blocking public API endpoints
// Laravel will handle 404s automatically for API routes
