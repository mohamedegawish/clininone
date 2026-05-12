<?php

namespace App\Models\Traits;

use App\Models\Scopes\ClinicScope;

/**
 * Apply to any Eloquent model that belongs to a single clinic (tenant).
 *
 * Effects:
 *  1. Adds ClinicScope global scope — every query is automatically filtered
 *     to the authenticated user's clinic_id (super-admins bypass this).
 *  2. Auto-sets clinic_id on create if the model's clinic_id is empty.
 */
trait BelongsToClinic
{
    public static function bootBelongsToClinic(): void
    {
        // Register the global query scope
        static::addGlobalScope(new ClinicScope());

        // Auto-fill clinic_id before insert if not provided
        static::creating(function ($model) {
            if (empty($model->clinic_id)) {
                $clinicId = ClinicScope::resolveClinicId();
                if ($clinicId) {
                    $model->clinic_id = $clinicId;
                }
            }
        });
    }

    /**
     * Temporarily remove the global scope for admin/cross-clinic queries.
     * Usage: Patient::withoutClinicScope()->get()
     */
    public static function withoutClinicScope(): \Illuminate\Database\Eloquent\Builder
    {
        return static::withoutGlobalScope(ClinicScope::class);
    }
}
