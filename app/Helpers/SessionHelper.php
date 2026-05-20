<?php

namespace App\Helpers;

use Illuminate\Http\Request;

class SessionHelper
{
    /**
     * مسح جميع الـ sessions والـ cookies وإعادة التوجيه إلى صفحة تسجيل دخول الأدمن
     */
    public static function clearSessionAndRedirectToAdminLogin(Request $request)
    {
        // تحقق من إعداد تعطيل إعادة التوجيه التلقائي
        if (env('DISABLE_AUTO_REDIRECT', false) || env('APP_ENV') !== 'local') {
            abort(404);
        }

        // تحقق من أن المستخدم ليس بالفعل في صفحة تسجيل دخول الأدمن لتجنب redirect loop
        if ($request->is('admin/login')) {
            abort(404);
        }

        // مسح جميع بيانات الـ session
        if ($request->hasSession()) {
            $request->session()->flush();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        // إنشاء response للتوجيه
        $response = redirect()->route('admin.login');

        // مسح جميع الـ cookies الموجودة
        $cookies = $request->cookies->all();
        foreach ($cookies as $name => $value) {
            $response->withCookie(cookie()->forget($name));
        }

        // مسح cookies إضافية شائعة في Laravel
        $commonCookies = [
            'laravel_session',
            'XSRF-TOKEN',
            'remember_web',
            'remember_admin',
            session()->getName(),
            config('session.cookie'),
        ];

        foreach ($commonCookies as $cookieName) {
            if ($cookieName) {
                $response->withCookie(cookie()->forget($cookieName));
            }
        }

        // مسح cookies بمسارات مختلفة
        foreach ($commonCookies as $cookieName) {
            if ($cookieName) {
                $response->withCookie(cookie()->forget($cookieName, '/'));
                $response->withCookie(cookie()->forget($cookieName, '/admin'));
            }
        }

        return $response;
    }

    /**
     * مسح جميع الـ sessions فقط
     */
    public static function clearSession(Request $request)
    {
        if ($request->hasSession()) {
            $request->session()->flush();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }
    }

    /**
     * مسح جميع الـ cookies من response
     */
    public static function clearCookiesFromResponse($response, Request $request)
    {
        // مسح جميع الـ cookies الموجودة
        $cookies = $request->cookies->all();
        foreach ($cookies as $name => $value) {
            $response->withCookie(cookie()->forget($name));
        }

        // مسح cookies إضافية شائعة
        $commonCookies = [
            'laravel_session',
            'XSRF-TOKEN',
            'remember_web',
            'remember_admin',
            session()->getName(),
            config('session.cookie'),
        ];

        foreach ($commonCookies as $cookieName) {
            if ($cookieName) {
                $response->withCookie(cookie()->forget($cookieName));
                $response->withCookie(cookie()->forget($cookieName, '/'));
                $response->withCookie(cookie()->forget($cookieName, '/admin'));
            }
        }

        return $response;
    }
}
