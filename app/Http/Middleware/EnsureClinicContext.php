<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Ensures the authenticated user has a resolvable clinic context before
 * entering any clinic-scoped route. Super-admins are redirected to their
 * own dashboard rather than being blocked.
 */
class EnsureClinicContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Super-admins do not belong to a clinic — send them to admin area
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard')
                ->with('warning', 'Super-admins should use the admin panel.');
        }

        // Resolve clinic: direct clinic_id on user first, then doctor→pivot
        $clinicId = $user->clinic_id
            ?? $user->doctor?->clinics()->value('clinics.id');

        if (! $clinicId) {
            abort(403, 'Your account is not associated with any clinic. Contact your administrator.');
        }

        // Make clinic_id available throughout the request lifecycle
        $request->merge(['_resolved_clinic_id' => $clinicId]);

        return $next($request);
    }
}
