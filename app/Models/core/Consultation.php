<?php

namespace App\Models\core;

use App\Models\core\ConsultationMedication;
use App\Models\Traits\BelongsToClinic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Consultation extends Model
{
    use HasFactory, BelongsToClinic;

    protected $fillable = [
        'appointment_id',
        'patient_id',
        'doctor_id',
        'clinic_id',
        'symptoms',
        'diagnosis',
        'treatment',
        'notes',
        'bp', 'temp', 'pulse', 'hr', 'rr', 'spo2', 'weight', 'height',
        'medications',
    ];

    protected function casts(): array
    {
        return [
            'medications' => 'array',
        ];
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function medicationRecords(): HasMany
    {
        return $this->hasMany(ConsultationMedication::class)->orderBy('sort_order');
    }
}
