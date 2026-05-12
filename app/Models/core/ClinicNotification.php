<?php

namespace App\Models\core;

use App\Models\Traits\BelongsToClinic;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClinicNotification extends Model
{
    use BelongsToClinic;

    protected $table = 'clinic_notifications';

    public const TYPE_APPOINTMENT = 'appointment';
    public const TYPE_SYSTEM      = 'system';
    public const TYPE_PAYMENT     = 'payment';

    protected $fillable = [
        'clinic_id',
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'is_read',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'data'     => 'array',
            'is_read'  => 'boolean',
            'read_at'  => 'datetime',
        ];
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Mark this notification as read. */
    public function markAsRead(): void
    {
        if (! $this->is_read) {
            $this->update(['is_read' => true, 'read_at' => now()]);
        }
    }

    /** Convenience factory: create a clinic notification. */
    public static function notify(
        int    $clinicId,
        string $title,
        string $message,
        string $type = self::TYPE_APPOINTMENT,
        array  $data = [],
        ?int   $userId = null,
    ): self {
        return static::create([
            'clinic_id' => $clinicId,
            'user_id'   => $userId,
            'type'      => $type,
            'title'     => $title,
            'message'   => $message,
            'data'      => $data,
        ]);
    }
}
