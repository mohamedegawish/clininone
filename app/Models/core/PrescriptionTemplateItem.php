<?php

namespace App\Models\core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrescriptionTemplateItem extends Model
{
    protected $fillable = [
        'template_id',
        'medication_id',
        'name',
        'dosage',
        'frequency',
        'route',
        'duration',
        'instructions',
        'sort_order',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(PrescriptionTemplate::class);
    }

    public function medication(): BelongsTo
    {
        return $this->belongsTo(Medication::class);
    }
}
