<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDoctorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|min:3|max:255',

            'email' => 'required|email|unique:doctors,email|unique:users,email',

            'password' => 'required|string|min:6|confirmed',

            'phone' => [
                'required',
                'string',
                'max:20',
                'regex:/^01[0-2,5]{1}[0-9]{8}$/'
            ],

            'address' => 'required|string|max:500',
            'governorate' => 'required|string|max:255',
            'city' => 'required|string|max:255',

            'specialty' => 'required|string|max:255',

            'gender' => 'nullable|in:male,female',
            'experience_years' => 'nullable|integer|min:0',
            'qualification' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:2000',

            'status' => 'nullable|in:active,inactive',
        ];
    }
}
