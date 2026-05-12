<?php

namespace App\Http\Resources;

use App\Models\core\DoctorSchedule;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorScheduleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'doctor_id' => $this->doctor_id,
            'clinic_id' => $this->clinic_id,
            'day_of_week' => $this->day_of_week,
            'day_name' => DoctorSchedule::DAYS[$this->day_of_week] ?? 'Unknown',
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'slot_duration' => $this->slot_duration,
            'is_active' => $this->is_active,
        ];
    }
}
