<?php

namespace App\Models\core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsultationMedication extends Model
{
    protected $fillable = [
        'consultation_id',
        'medication_id',
        'name',
        'generic',
        'dosage',
        'frequency',
        'route',
        'duration',
        'instructions',
        'sort_order',
    ];

    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class);
    }
}
