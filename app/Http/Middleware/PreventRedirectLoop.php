<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventRedirectLoop
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // إذا كان المستخدم يحاول الوصول لصفحة تسجيل الدخول
        if ($request->is('admin/login')) {
            // تحقق من وجود redirect loop في الـ session
            $redirectCount = session('admin_login_redirect_count', 0);
            
            if ($redirectCount > 3) {
                // مسح العداد وإظهار رسالة خطأ
                session()->forget('admin_login_redirect_count');
                session()->flash('error', 'حدث خطأ في إعادة التوجيه. يرجى المحاولة مرة أخرى.');
            } else {
                // زيادة العداد
                session(['admin_login_redirect_count' => $redirectCount + 1]);
            }
        } else {
            // مسح العداد إذا لم يكن في صفحة تسجيل الدخول
            session()->forget('admin_login_redirect_count');
        }

        return $next($request);
    }
}
