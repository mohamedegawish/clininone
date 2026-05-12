<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'governorate' => $this->governorate,
            'city' => $this->city,
            'specialty' => $this->specialty,
            'gender' => $this->gender,
            'experience_years' => $this->experience_years,
            'qualification' => $this->qualification,
            'bio' => $this->bio,
            'status' => $this->status,

            'clinics_count' => $this->whenCounted('clinics'),

            'clinics' => $this->whenLoaded('clinics', function () {
                return $this->clinics->map(function ($clinic) {
                    return [
                        'id' => $clinic->id,
                        'name' => $clinic->name,

                        'role' => $clinic->pivot->role ?? null,
                    ];
                });
            }),

            'created_at' => $this->when(
                $this->created_at,
                fn () => $this->created_at->toDateTimeString()
            ),
        ];
    }
}
