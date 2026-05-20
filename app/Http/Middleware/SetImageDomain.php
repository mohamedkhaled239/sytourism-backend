<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetImageDomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Set the image domain based on the current request
        $scheme = $request->isSecure() ? 'https' : 'http';
        $host = $request->getHost();
        $port = $request->getPort();
        
        // Build the full domain
        $domain = $scheme . '://' . $host;
        if ($port && $port !== 80 && $port !== 443) {
            $domain .= ':' . $port;
        }
        
        // Set the image domain for this request
        config(['app.image_domain' => $domain]);
        
        return $next($request);
    }
}
