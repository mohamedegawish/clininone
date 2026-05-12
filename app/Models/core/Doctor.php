<?php

namespace App\Models\core;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Doctor extends Model
{
    use HasFactory;

    protected $table = 'doctors';

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($doctor) {
            if (empty($doctor->arabic_name) && !empty($doctor->name)) {
                $doctor->arabic_name = \App\Services\TransliterationService::enToAr($doctor->name);
            }
        });
    }

    protected $fillable = [
        'user_id',
        'name',
        'arabic_name',
        'email',
        'phone',
        'address',
        'governorate',
        'city',
        'specialty',
        'price',
        'followup_price',
        'photo_path',
        'status',
        'gender',
        'experience_years',
        'qualification',
        'bio',
    ];

    protected function casts(): array
    {
        return [
            'experience_years' => 'integer',
            'price' => 'decimal:2',
            'followup_price' => 'decimal:2',
        ];
    }

    /**
     * Get the user that owns the doctor.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The clinics that belong to the doctor.
     */
    public function clinics(): BelongsToMany
    {
        return $this->belongsToMany(Clinic::class, 'clinic_doctor')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Check if the doctor is an admin in a specific clinic.
     */
    public function isClinicAdmin(int|string $clinicId): bool
    {
        return $this->clinics()
            ->where('clinics.id', $clinicId)
            ->wherePivot('role', 'admin')
            ->exists();
    }

    /**
     * Get the schedules for the doctor.
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(DoctorSchedule::class);
    }

    /**
     * Get the services for the doctor.
     */
    public function services(): HasMany
    {
        return $this->hasMany(DoctorService::class);
    }

    /**
     * Get the appointments for the doctor.
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
    /**
     * Get the ratings for the doctor.
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }
}
