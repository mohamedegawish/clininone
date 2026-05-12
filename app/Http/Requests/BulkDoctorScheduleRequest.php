<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkDoctorScheduleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
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
            'schedules' => 'required|array|min:1',
            'schedules.*.day_of_week' => 'required|integer|between:0,6',
            'schedules.*.start_time' => 'required|date_format:H:i',
            'schedules.*.end_time' => 'required|date_format:H:i|after:schedules.*.start_time',
            'schedules.*.slot_duration' => 'nullable|integer|in:10,15,20,30,45,60',
            'schedules.*.is_active' => 'nullable|boolean',
        ];

        if ($isAdmin) {
            $rules['clinic_id'] = 'required|integer|exists:clinics,id';
        }

        return $rules;
    }
}
