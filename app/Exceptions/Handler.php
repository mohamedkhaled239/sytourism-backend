<?php

namespace App\Exceptions;

use App\Helpers\SessionHelper;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e)
    {
        // تعطيل إعادة التوجيه التلقائي مؤقتاً لحل مشكلة السيرفر
        // إعادة توجيه أي مسار غير موجود (404) إلى صفحة تسجيل دخول الأدمن
        if ($e instanceof NotFoundHttpException && env('APP_ENV') === 'local') {
            // التأكد من أن الطلب ليس API request وليس في مسارات محددة
            if (!$request->expectsJson() &&
                !$request->is('api/*') &&
                !$request->is('admin/login') &&
                !$request->is('clear-session') &&
                !$request->is('test-*') &&
                !$request->is('debug_*') &&
                !str_ends_with($request->path(), '.php')) {
                return SessionHelper::clearSessionAndRedirectToAdminLogin($request);
            }
        }

        return parent::render($request, $e);
    }


}
