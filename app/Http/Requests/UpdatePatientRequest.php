<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rule;

class UpdatePatientRequest extends \Illuminate\Foundation\Http\FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by Policy
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $doctor = $this->user()?->doctor;
        $clinicId = $doctor ? $doctor->clinics()->first()?->id : null;
        $patientId = $this->route('patient') ?? $this->route('id');

        return [
            'full_name' => 'sometimes|required|string|max:255',
            'english_name' => 'sometimes|required|string|max:255',
            'phone' => [
                'sometimes',
                'required',
                'string',
                'max:20',
                Rule::unique('patients')
                    ->where(fn ($query) => $query->where('clinic_id', $clinicId))
                    ->ignore($patientId),
            ],
            'ssn' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date|before:today',
            'age' => 'nullable|integer',
            'gender' => 'nullable|in:male,female',
            'nationality' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('patients')
                    ->where(fn ($query) => $query->where('clinic_id', $clinicId))
                    ->ignore($patientId),
            ],
            'company' => 'nullable|string|max:255',
            'policy_name' => 'nullable|string|max:255',
            'class' => 'nullable|string|max:255',
            'card_no' => 'nullable|string|max:255',
            'status' => 'nullable|in:active,inactive',
        ];
    }
}
