<?php

namespace App\Models\saas;

use Illuminate\Database\Eloquent\Model;
use App\Models\saas\Subscription;
class Plan extends Model
{
    protected $table = 'plans';
    protected $fillable = [
        'name',
        'price',
        'duration',
        'max_patients',
        'max_appointments',
        'features',
    ];

    public function subscriptions(){
        return $this->hasMany(Subscription::class);

    }

}
