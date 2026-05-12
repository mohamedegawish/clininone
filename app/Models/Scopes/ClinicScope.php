<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Automatically filter queries to the authenticated user's clinic.
 * Applied via the BelongsToClinic trait on all tenant models.
 */
class ClinicScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $clinicId = static::resolveClinicId();

        if ($clinicId !== null) {
            $builder->where($model->getTable() . '.clinic_id', $clinicId);
        }
    }

    /**
     * Resolve the current clinic ID from the authenticated user.
     * Returns null for super-admins (they see all data) or unauthenticated contexts.
     */
    public static function resolveClinicId(): ?int
    {
        if (! auth()->check()) {
            return null;
        }

        $user = auth()->user();

        // Super-admins bypass the scope — they see everything
        if ($user->role === 'admin') {
            return null;
        }

        return $user->clinic_id
            ?? $user->doctor?->clinics()->value('clinics.id');
    }
}
