<?php

namespace App\Models\core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PrescriptionTemplate extends Model
{
    protected $fillable = ['doctor_id', 'name'];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PrescriptionTemplateItem::class, 'template_id')->orderBy('sort_order');
    }
}
