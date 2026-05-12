<?php

namespace App\Policies;

use App\Models\User;
use App\Models\core\Patient;
use Illuminate\Auth\Access\Response;

class PatientPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $this->isAdminOrDoctor($user);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Patient $patient): bool
    {
        return $this->isAdmin($user)
            || ($user->doctor?->clinics()->where('clinics.id', $patient->clinic_id)->exists() ?? false);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->isAdminOrDoctor($user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Patient $patient): bool
    {
        return $this->isAdmin($user)
            || ($user->doctor?->clinics()->where('clinics.id', $patient->clinic_id)->exists() ?? false);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Patient $patient): bool
    {
        return $this->isAdmin($user)
            || ($user->doctor?->clinics()->where('clinics.id', $patient->clinic_id)->exists() ?? false);
    }

    /**
     * Check if the user has an admin role.
     */
    private function isAdmin(User $user): bool
    {
        return in_array($user->role, ['admin', 'super_admin']);
    }

    /**
     * Check if the user is an admin or has a doctor record.
     */
    private function isAdminOrDoctor(User $user): bool
    {
        return $this->isAdmin($user) || $user->doctor()->exists();
    }
}
