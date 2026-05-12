<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PrescriptionTemplateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'doctor_id' => $this->doctor_id,
            'name'      => $this->name,
            'items'     => $this->whenLoaded('items', fn () =>
                $this->items->map(fn ($item) => [
                    'id'           => $item->id,
                    'medication_id'=> $item->medication_id,
                    'name'         => $item->name,
                    'dosage'       => $item->dosage,
                    'frequency'    => $item->frequency,
                    'duration'     => $item->duration,
                    'instructions' => $item->instructions,
                    'sort_order'   => $item->sort_order,
                ])
            ),
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
