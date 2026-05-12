<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckClinicAdmin
{
    
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->role === 'admin') {
            return $next($request);
        }


        $clinicId = $request->route('clinic') ?? $request->route('id');

        if ($clinicId && $user && $user->doctor && $user->doctor->isClinicAdmin($clinicId)) {
            return $next($request);
        }

        return response()->json([
            'message' => 'Access denied. You must be the clinic admin or a super admin.'
        ], 403);
    }
}
