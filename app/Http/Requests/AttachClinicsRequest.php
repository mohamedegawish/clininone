<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttachClinicsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'clinics' => 'required|array|min:1',

            'clinics.*.id' => 'required|exists:clinics,id',

            'clinics.*.role' => 'nullable|in:admin,doctor',
        ];
    }
}
