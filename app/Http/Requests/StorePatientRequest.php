<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePatientRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->user();
        $clinicId = $this->resolveClinicId($user);

        $rules = [
            'full_name' => 'required|string|max:255',
            'english_name' => 'required|string|max:255',
            'phone' => [
                'required',
                'string',
                'max:20',
                Rule::unique('patients')->where(fn ($query) => $query->where('clinic_id', $clinicId)),
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
                Rule::unique('patients')->where(fn ($query) => $query->where('clinic_id', $clinicId)),
            ],
            'company' => 'nullable|string|max:255',
            'policy_name' => 'nullable|string|max:255',
            'class' => 'nullable|string|max:255',
            'card_no' => 'nullable|string|max:255',
            'status' => 'nullable|in:active,inactive',
        ];

        // Admins must specify which clinic the patient belongs to
        if (in_array($user?->role, ['admin', 'super_admin'])) {
            $rules['clinic_id'] = 'required|integer|exists:clinics,id';
        }

        return $rules;
    }

    /**
     * Resolve the clinic ID from the authenticated user.
     */
    private function resolveClinicId(?object $user): ?int
    {
        if (in_array($user?->role, ['admin', 'super_admin']) && $this->filled('clinic_id')) {
            return (int) $this->input('clinic_id');
        }

        return $user?->doctor?->clinics()->first()?->id;
    }
}
