<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;

class CheckTokenExpiry
{
    public function handle($request, Closure $next)
    {
        $token = $request->user()?->currentAccessToken();

        if ($token && $token->expires_at && Carbon::parse($token->expires_at)->isPast()) {
            return response()->json([
                'message' => 'Token expired'
            ], 401);
        }

        return $next($request);
    }
}
