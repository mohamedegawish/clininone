<?php

namespace App\Services;

use App\Mail\BookingConfirmationMail;
use App\Models\core\Appointment;
use App\Models\core\Patient;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PublicBookingService
{
    public function __construct(
        protected AppointmentService $appointmentService
    ) {}

    public function book(array $data): Appointment
    {
        $appointment = DB::transaction(function () use ($data) {
            $patient = Patient::firstOrCreate(
                [
                    'phone'     => $data['phone'],
                    'clinic_id' => $data['clinic_id'],
                ],
                [
                    'full_name'    => $data['full_name'],
                    'english_name' => $data['english_name'] ?? $data['full_name'],
                    'gender'       => $data['gender'] ?? null,
                    'birth_date'   => $data['birth_date'] ?? null,
                    'email'        => $data['email'] ?? null,
                ]
            );

            // Update email if patient already existed but email was not set
            if (!empty($data['email']) && empty($patient->email)) {
                $patient->update(['email' => $data['email']]);
            }

            $bookingData = [
                'doctor_id'        => $data['doctor_id'],
                'patient_id'       => $patient->id,
                'appointment_date' => $data['appointment_date'],
                'start_time'       => $data['start_time'],
                'source'           => 'online',
                'notes'            => $data['notes'] ?? null,
            ];

            return $this->appointmentService->book($bookingData, $data['clinic_id']);
        });

        // Load relationships needed for the confirmation email
        $appointment->load(['patient', 'doctor', 'clinic']);

        // Send confirmation email if patient provided one
        $email = $data['email'] ?? $appointment->patient?->email;
        if ($email) {
            Mail::to($email)->send(new BookingConfirmationMail($appointment));
        }

        // Invalidate notification cache so the clinic sees the new appointment immediately
        Cache::forget('notifications.' . $appointment->clinic_id);

        return $appointment;
    }
}
