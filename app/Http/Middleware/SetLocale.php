<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (Session::has('locale')) {
            App::setLocale(Session::get('locale'));
        } elseif ($request->hasHeader('Accept-Language')) {
            $locale = $request->header('Accept-Language');
            if (in_array($locale, ['en', 'ar'])) {
                App::setLocale($locale);
            }
        } else {
            // Default to Arabic
            App::setLocale('ar');
        }

        return $next($request);
    }
}
