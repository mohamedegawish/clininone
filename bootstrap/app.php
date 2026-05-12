<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckTokenExpiry;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);

        $middleware->api(prepend: [
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);

        $middleware->api(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);

        $middleware->alias([
            'check.token.expiry'   => CheckTokenExpiry::class,
            'super_admin'          => \App\Http\Middleware\IsSuperAdmin::class,
            'clinic_admin'         => \App\Http\Middleware\CheckClinicAdmin::class,
            'cache.headers'        => \App\Http\Middleware\CacheHeaders::class,
            'clinic.context'       => \App\Http\Middleware\EnsureClinicContext::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage() ?: 'Internal Server Error',
                ], $status)->header('Access-Control-Allow-Origin', '*');
            }
        });
    })
    ->create();
