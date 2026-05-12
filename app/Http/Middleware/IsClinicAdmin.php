<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Clinic;

class IsClinicAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        $clinicId = $request->route('clinicId') ?? $request->input('clinic_id');

        if (!$clinicId) {
            return response()->json(['message' => 'Clinic ID required'], 400);
        }

        $isAdmin = $user->doctor && $user->doctor->clinics()
                ->where('clinic_id', $clinicId)
                ->wherePivot('role', 'admin')
                ->exists();

        if (!$isAdmin) {
            return response()->json(['message' => 'Not clinic admin'], 403);
        }

        return $next($request);
    }
}
