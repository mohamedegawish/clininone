<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'created_by' => $this->created_by,
            'is_favorite'=> $this->whenPivotLoaded('favorite_medications', fn () => true, false),
        ];
    }
}
