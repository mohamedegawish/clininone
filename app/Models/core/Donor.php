<?php

namespace App\Models\core;

use Illuminate\Database\Eloquent\Model;

class Donor extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'blood_type',
        'governorate',
        'city',
        'address',
        'status',
        'last_donation_date',
    ];
}
