<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
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
            'appointment_date' => $this->appointment_date?->format('Y-m-d'),
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'queue_number' => $this->queue_number,
            'status' => $this->status,
            'is_paid' => (bool) ($this->is_paid ?? false),
            'total_price' => (float) ($this->total_price ?? 0),
            'notes' => $this->notes,
            'cancellation_reason' => $this->cancellation_reason,

            'patient' => $this->whenLoaded('patient', function () {
                if (!$this->patient) return null;
                return [
                    'id' => $this->patient->id,
                    'full_name' => $this->patient->full_name ?? '',
                    'english_name' => $this->patient->english_name ?? '',
                    'phone' => $this->patient->phone ?? '',
                ];
            }),

            'doctor' => $this->whenLoaded('doctor', function () {
                if (!$this->doctor) return null;
                return [
                    'id' => $this->doctor->id,
                    'name' => $this->doctor->name ?? '',
                    'specialty' => $this->doctor->specialty ?? '',
                    'phone' => $this->doctor->phone ?? '',
                ];
            }),

            'clinic' => new ClinicResource($this->whenLoaded('clinic')),

            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
