<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(\Illuminate\Http\Request $request): array
    {
        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'english_name' => $this->english_name,
            'phone' => $this->phone,
            'ssn' => $this->ssn,
            'birth_date' => $this->birth_date ? $this->birth_date->format('Y-m-d') : null,
            'age' => $this->age,
            'gender' => $this->gender,
            'nationality' => $this->nationality,
            'address' => $this->address,
            'email' => $this->email,
            'company' => $this->company,
            'policy_name' => $this->policy_name,
            'class' => $this->class,
            'card_no' => $this->card_no,
            'status' => $this->status,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'clinic' => new ClinicResource($this->whenLoaded('clinic')),
        ];
    }
}
