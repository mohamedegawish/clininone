<?php

namespace App\Models;

use App\Models\core\Clinic;
use App\Models\core\ClinicNotification;
use App\Models\core\Doctor;
use Laravel\Sanctum\HasApiTokens;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    public const ROLE_ADMIN  = 'admin';
    public const ROLE_DOCTOR = 'doctor';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'clinic_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /** Whether this user is a super-admin. */
    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /** Whether this user belongs to a clinic. */
    public function hasClinic(): bool
    {
        return (bool) ($this->clinic_id ?? $this->doctor?->clinics()->exists());
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function doctor(): HasOne
    {
        return $this->hasOne(Doctor::class);
    }

    public function clinicNotifications(): HasMany
    {
        return $this->hasMany(ClinicNotification::class);
    }
}
