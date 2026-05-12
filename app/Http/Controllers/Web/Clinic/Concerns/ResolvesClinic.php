<?php

namespace App\Http\Controllers\Web\Clinic\Concerns;

use App\Models\core\Clinic;
use Illuminate\Support\Facades\Auth;

trait ResolvesClinic
{
    /**
     * Return the clinic the current user belongs to.
     * Priority: user.clinic_id → doctor→clinics pivot.
     * Aborts with 403 if no clinic context can be determined.
     * Never falls back to an arbitrary "first" clinic.
     */
    private function resolveClinic(): Clinic
    {
        return once(function (): Clinic {
            $user = Auth::user();

            // 1. Direct association via clinic_id on the user row (preferred)
            if ($user?->clinic_id) {
                return Clinic::findOrFail($user->clinic_id);
            }

            // 2. Doctor → clinic pivot
            $clinicId = $user?->doctor?->clinics()->value('clinics.id');

            abort_if(! $clinicId, 403, 'No clinic context found for this user.');

            return Clinic::findOrFail($clinicId);
        });
    }
}
