<?php

namespace App\Services;

use App\Models\core\Clinic;
use Illuminate\Support\Facades\DB;

class ClinicService
{
    
    public function store(array $clinicData, int $doctorId): Clinic
    {
        return DB::transaction(function () use ($clinicData, $doctorId) {
            $clinic = Clinic::create($clinicData);

            $clinic->doctors()->attach($doctorId, [
                'role' => 'admin'
            ]);

            return $clinic;
        });
    }
}
