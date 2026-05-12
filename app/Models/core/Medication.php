<?php

namespace App\Models\core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medication extends Model
{
    protected $fillable = [
        'name', 'generic', 'created_by',
        'default_dosage', 'default_frequency', 'default_route',
        'default_duration', 'default_instructions',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'created_by');
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(FavoriteMedication::class);
    }

    public function isFavoritedBy(int $doctorId): bool
    {
        return $this->favorites()->where('doctor_id', $doctorId)->exists();
    }
}
