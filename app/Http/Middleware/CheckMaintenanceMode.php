<?php

namespace App\Http\Middleware;

use App\Helpers\WebsiteSettings;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip for admin routes
        if ($request->is('admin/*') || $request->is('login') || $request->is('register')) {
            return $next($request);
        }

        // Check if maintenance mode is enabled
        if (WebsiteSettings::isMaintenance()) {
            $message = WebsiteSettings::maintenanceMessage() ?? 'Maaf, website sedang dalam masa pemeliharaan. Silakan kembali lagi nanti.';

            return response()->view('frontend.maintenance', compact('message'), 503);
        }

        return $next($request);
    }
}
