<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DisableRedirectOnProduction
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // إذا كانت البيئة production وليست local، تجاهل إعادة التوجيه
        if (env('APP_ENV') !== 'local') {
            // إضافة flag للطلب لتجاهل إعادة التوجيه
            $request->attributes->set('disable_auto_redirect', true);
        }

        return $next($request);
    }
}
