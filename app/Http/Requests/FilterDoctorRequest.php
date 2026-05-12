<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterDoctorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'nullable|in:active,inactive',

            'specialty' => 'nullable|string|max:255',
            'governorate' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',

            'search' => 'nullable|string|max:255',

            'clinic_id' => 'nullable|exists:clinics,id',

            'sort_by' => 'nullable|in:id,name,email,created_at',

            'sort_dir' => 'nullable|in:asc,desc',

            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }
}
