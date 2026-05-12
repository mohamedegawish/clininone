<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDoctorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'name' => 'sometimes|string|min:3|max:255',

            'email' => "sometimes|email|unique:doctors,email,$id",

            'phone' => [
                'sometimes',
                'string',
                'max:20',
                'regex:/^01[0-2,5]{1}[0-9]{8}$/'
            ],

            'address' => 'sometimes|string|max:500',
            'governorate' => 'sometimes|string|max:255',
            'city' => 'sometimes|string|max:255',

            'specialty' => 'sometimes|string|max:255',
            'gender' => 'sometimes|nullable|in:male,female',
            'experience_years' => 'sometimes|nullable|integer|min:0',
            'qualification' => 'sometimes|nullable|string|max:255',
            'bio' => 'sometimes|nullable|string|max:2000',
            'status' => 'sometimes|in:active,inactive',
        ];
    }
}
