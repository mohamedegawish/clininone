<?php

namespace App\Models\saas;
use App\Models\core\Clinic;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'clinic_id',
        'plan_id',
        'status',
        'start_at',
        'end_at',
        'auto_renew'
    ];

    protected function casts(): array
    {
        return [
            'auto_renew' => 'boolean',
            'start_at' => 'datetime',
            'end_at' => 'datetime',
        ];
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }
}
