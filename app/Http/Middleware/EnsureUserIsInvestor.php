<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsInvestor
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->isInvestor() || !$request->user()->is_approved) {
            return response()->json([
                'success' => false,
                'message' => 'هذا المحتوى متاح للمستثمرين المعتمدين فقط',
                'error_code' => 'INVESTOR_ONLY_ACCESS',
            ], 403);
        }

        return $next($request);
    }
}
