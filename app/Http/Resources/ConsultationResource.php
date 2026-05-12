<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConsultationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'appointment_id' => $this->appointment_id,
            'patient_id'     => $this->patient_id,
            'doctor_id'      => $this->doctor_id,
            'clinic_id'      => $this->clinic_id,

            // Vitals
            'vitals' => [
                'bp'     => $this->bp,
                'temp'   => $this->temp,
                'pulse'  => $this->pulse,
                'hr'     => $this->hr,
                'rr'     => $this->rr,
                'spo2'   => $this->spo2,
                'weight' => $this->weight,
                'height' => $this->height,
            ],

            // Clinical notes
            'symptoms'  => $this->symptoms,
            'diagnosis' => $this->diagnosis,
            'treatment' => $this->treatment,
            'notes'     => $this->notes,

            // Relations (loaded on demand)
            'medications' => $this->whenLoaded('medicationRecords', fn () =>
                $this->medicationRecords->map(fn ($m) => [
                    'id'           => $m->id,
                    'medication_id'=> $m->medication_id,
                    'name'         => $m->name,
                    'generic'      => $m->generic,
                    'dosage'       => $m->dosage,
                    'frequency'    => $m->frequency,
                    'route'        => $m->route,
                    'duration'     => $m->duration,
                    'instructions' => $m->instructions,
                    'sort_order'   => $m->sort_order,
                ])
            ),

            'patient' => $this->whenLoaded('patient', fn () => [
                'id'           => $this->patient->id,
                'full_name'    => $this->patient->full_name,
                'english_name' => $this->patient->english_name,
                'phone'        => $this->patient->phone,
                'birth_date'   => $this->patient->birth_date?->format('Y-m-d'),
                'age'          => $this->patient->age,
                'gender'       => $this->patient->gender,
            ]),

            'doctor' => $this->whenLoaded('doctor', fn () => [
                'id'        => $this->doctor->id,
                'name'      => $this->doctor->name,
                'specialty' => $this->doctor->specialty,
            ]),

            'appointment' => $this->whenLoaded('appointment', fn () => [
                'id'               => $this->appointment->id,
                'appointment_date' => $this->appointment->appointment_date?->format('Y-m-d'),
                'start_time'       => $this->appointment->start_time,
                'type'             => $this->appointment->type,
                'status'           => $this->appointment->status,
            ]),

            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
