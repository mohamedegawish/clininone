<?php

namespace App\Http\Requests;

use App\Services\AppointmentService;
use Illuminate\Foundation\Http\FormRequest;

class PublicBookAppointmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('start_time')) {
            $time = $this->input('start_time');
            $englishTime = str_replace(['ص', 'م'], ['AM', 'PM'], $time);
            try {
                $this->merge([
                    'start_time' => \Carbon\Carbon::parse($englishTime)->format('H:i')
                ]);
            } catch (\Exception $e) {
                // Ignore parsing errors, validation will fail naturally
            }
        }

        if (empty($this->input('clinic_id')) && !empty($this->input('doctor_id'))) {
            $doctor = \App\Models\core\Doctor::find($this->input('doctor_id'));
            if ($doctor && $clinic = $doctor->clinics()->first()) {
                $this->merge([
                    'clinic_id' => $clinic->id
                ]);
            }
        }
    }
    
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Http\Exceptions\HttpResponseException(
            response()->json([
                'status' => false,
                'message' => 'validation error',
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
        return [
            'clinic_id' => 'nullable|integer|exists:clinics,id',
            'doctor_id' => 'required|integer|exists:doctors,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            // Patient fields
            'phone' => 'required|string|max:20',
            'full_name' => 'required|string|max:255',
            'english_name' => 'nullable|string|max:255',
            'gender' => 'nullable|in:male,female',
            'birth_date' => 'nullable|date|before:today',
            // Optional email for booking confirmation
            'email' => 'nullable|email|max:255',
            // Appointment details
            'notes' => 'nullable|string|max:1000',
        ];
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
