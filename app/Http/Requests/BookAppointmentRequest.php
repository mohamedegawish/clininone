<?php

namespace App\Http\Requests;

use App\Models\core\Appointment;
use App\Services\AppointmentService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookAppointmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Http\Exceptions\HttpResponseException(
            response()->json([
                'status' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422)
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->user();
        $isAdmin = in_array($user?->role, ['admin', 'super_admin']);

        $rules = [
            'doctor_id' => 'required|integer|exists:doctors,id',
            'patient_id' => 'required|integer|exists:patients,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'notes' => 'nullable|string|max:1000',
        ];

        if ($isAdmin) {
            $rules['clinic_id'] = 'required|integer|exists:clinics,id';
        }

        return $rules;
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(\Illuminate\Validation\Validator $validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $user = $this->user();
            $clinicId = in_array($user?->role, ['admin', 'super_admin'])
                ? $this->input('clinic_id')
                : $user?->doctor?->clinics()->first()?->id;

            if (! $clinicId) {
                $validator->errors()->add('clinic_id', 'Unable to determine clinic.');

                return;
            }

            $service = app(AppointmentService::class);
            $isAvailable = $service->isSlotAvailable(
                $this->input('doctor_id'),
                $this->input('appointment_date'),
                $this->input('start_time'),
            );

            if (! $isAvailable) {
                $validator->errors()->add('start_time', 'This time slot is already booked.');
            }
        });
    }
}
