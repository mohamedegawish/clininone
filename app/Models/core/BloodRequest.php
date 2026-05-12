<?php

namespace App\Models\core;

use Illuminate\Database\Eloquent\Model;

class BloodRequest extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'blood_type',
        'quantity',
        'governorate',
        'hospital',
        'type',
        'urgency_level',
        'status',
        'city',
        'address',
    ];
}
