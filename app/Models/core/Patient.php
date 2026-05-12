<?php

namespace App\Models\core;

use App\Models\Traits\BelongsToClinic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasFactory, SoftDeletes, BelongsToClinic;

    protected $table = 'patients';

    public const GENDER_MALE = 'male';
    public const GENDER_FEMALE = 'female';

    protected $fillable = [
        'full_name',
        'english_name',
        'phone',
        'ssn',
        'birth_date',
        'age',
        'gender',
        'nationality',
        'address',
        'email',
        'company',
        'policy_name',
        'class',
        'card_no',
        'status',
        'clinic_id',
    ];

    /**
     * Get the clinic that owns the patient.
     */
    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    /**
     * Get the appointments for the patient.
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'age' => 'integer',
            'status' => 'string',
        ];
    }
}
