<?php

namespace App\Services;

use App\Models\core\Appointment;
use App\Models\core\ClinicNotification;
use App\Models\core\DoctorSchedule;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class AppointmentService
{
    /**
     * Get available time slots for a doctor on a specific date.
     *
     * @return array<int, array{start_time: string, end_time: string}>
     */
    public function getAvailableSlots(int $doctorId, int $clinicId, string $date): array
    {
        $dateObj = Carbon::parse($date);
        $dayOfWeek = $dateObj->dayOfWeek;

        // Get the doctor's schedule for this day
        $schedule = DoctorSchedule::where('doctor_id', $doctorId)
            ->where('clinic_id', $clinicId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->first();

        if (! $schedule) {
            // Provide a default schedule for testing if none exists
            $schedule = new DoctorSchedule([
                'start_time' => '09:00:00',
                'end_time' => '17:00:00',
                'slot_duration' => 30,
            ]);
        }

        // Generate all possible slots
        $allSlots = $this->generateSlots(
            $schedule->start_time,
            $schedule->end_time,
            $schedule->slot_duration,
        );

        // Get already booked slots for this doctor on this date
        $bookedSlots = Appointment::where('doctor_id', $doctorId)
            ->where('appointment_date', $date)
            ->whereIn('status', [Appointment::STATUS_PENDING, Appointment::STATUS_CONFIRMED])
            ->pluck('start_time')
            ->map(fn ($time) => Carbon::parse($time)->format('H:i'))
            ->toArray();

        // Filter out booked slots and past slots
        $now = Carbon::now();
        $isToday = $dateObj->isToday();

        return collect($allSlots)
            ->filter(function ($slot) use ($bookedSlots, $isToday, $now) {
                // Exclude booked slots
                if (in_array($slot['raw_time'], $bookedSlots)) {
                    return false;
                }

                // Exclude past slots if the date is today
                if ($isToday && Carbon::parse($slot['raw_time'])->lte($now)) {
                    return false;
                }

                return true;
            })
            ->map(function ($slot) {
                unset($slot['raw_time']); // Remove raw_time before returning
                return $slot;
            })
            ->values()
            ->toArray();
    }

    /**
     * Generate time slots based on start, end, and duration.
     *
     * @return array<int, array{start_time: string, end_time: string}>
     */
    private function generateSlots(string $startTime, string $endTime, int $durationMinutes): array
    {
        $slots = [];
        $current = Carbon::parse($startTime);
        $end = Carbon::parse($endTime);

        while ($current->copy()->addMinutes($durationMinutes)->lte($end)) {
            $slotEnd = $current->copy()->addMinutes($durationMinutes);
            $slots[] = [
                'start_time' => str_replace(['AM', 'PM'], ['ص', 'م'], $current->format('h:i A')),
                'end_time' => str_replace(['AM', 'PM'], ['ص', 'م'], $slotEnd->format('h:i A')),
                'raw_time' => $current->format('H:i'),
            ];
            $current = $slotEnd;
        }

        return $slots;
    }

    /**
     * Book an appointment.
     */
    public function book(array $data, int $clinicId): Appointment
    {
        return DB::transaction(function () use ($data, $clinicId) {
            // Determine end_time from doctor's schedule
            $dateObj = Carbon::parse($data['appointment_date']);
            $schedule = DoctorSchedule::where('doctor_id', $data['doctor_id'])
                ->where('clinic_id', $clinicId)
                ->where('day_of_week', $dateObj->dayOfWeek)
                ->where('is_active', true)
                ->first();

            $slotDuration = $schedule ? $schedule->slot_duration : 30;

            $normalizedTime = str_replace(['ص', 'م'], ['AM', 'PM'], $data['start_time']);
            $startTime = Carbon::parse($normalizedTime);
            $endTime = $startTime->copy()->addMinutes($slotDuration);

            $maxQueue = Appointment::where('doctor_id', $data['doctor_id'])
                ->where('appointment_date', $data['appointment_date'])
                ->max('queue_number') ?? 0;

            $appointment = Appointment::create([
                'patient_id'       => $data['patient_id'],
                'doctor_id'        => $data['doctor_id'],
                'clinic_id'        => $clinicId,
                'appointment_date' => $data['appointment_date'],
                'start_time'       => $startTime->format('H:i'),
                'end_time'         => $endTime->format('H:i'),
                'queue_number'     => $data['queue_number'] ?? ($maxQueue + 1),
                'status'           => $data['status'] ?? Appointment::STATUS_PENDING,
                'source'           => $data['source'] ?? 'clinic',
                'is_paid'          => $data['is_paid'] ?? false,
                'notes'            => $data['notes'] ?? null,
            ]);

            // Notify clinic about the new appointment
            $appointment->load(['patient', 'doctor']);
            ClinicNotification::notify(
                clinicId: $clinicId,
                title:    'New Appointment Booked',
                message:  sprintf(
                    '%s booked an appointment with Dr. %s on %s at %s',
                    $appointment->patient?->full_name ?? 'A patient',
                    $appointment->doctor?->name ?? 'Unknown',
                    Carbon::parse($data['appointment_date'])->format('d M Y'),
                    $startTime->format('h:i A'),
                ),
                type:     ClinicNotification::TYPE_APPOINTMENT,
                data:     [
                    'appointment_id' => $appointment->id,
                    'source'         => $data['source'] ?? 'clinic',
                ],
            );

            return $appointment;
        });
    }

    /**
     * Cancel an appointment.
     */
    public function cancel(Appointment $appointment, ?string $reason = null): Appointment
    {
        return DB::transaction(function () use ($appointment, $reason) {
            $appointment->update([
                'status' => Appointment::STATUS_CANCELLED,
                'cancellation_reason' => $reason,
            ]);

            return $appointment->fresh();
        });
    }

    /**
     * Confirm an appointment.
     */
    public function confirm(Appointment $appointment): Appointment
    {
        return DB::transaction(function () use ($appointment) {
            $appointment->update([
                'status' => Appointment::STATUS_CONFIRMED,
            ]);

            return $appointment->fresh();
        });
    }

    /**
     * Complete an appointment.
     */
    public function complete(Appointment $appointment): Appointment
    {
        return DB::transaction(function () use ($appointment) {
            $appointment->update([
                'status' => Appointment::STATUS_COMPLETED,
            ]);

            return $appointment->fresh();
        });
    }

    /**
     * List appointments with filtering.
     */
    public function list(array $filters, int $clinicId): LengthAwarePaginator
    {
        $query = Appointment::where('clinic_id', $clinicId)
            ->with(['patient', 'doctor']);

        if (! empty($filters['doctor_id'])) {
            $query->where('doctor_id', $filters['doctor_id']);
        }

        if (! empty($filters['patient_id'])) {
            $query->where('patient_id', $filters['patient_id']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['date'])) {
            $query->whereDate('appointment_date', $filters['date']);
        }

        if (! empty($filters['date_from'])) {
            $query->whereDate('appointment_date', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('appointment_date', '<=', $filters['date_to']);
        }

        $query->orderBy('appointment_date')->orderBy('start_time');

        return $query->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Show a single appointment.
     */
    public function show(int $id, int $clinicId): Appointment
    {
        return Appointment::where('clinic_id', $clinicId)
            ->with(['patient', 'doctor', 'clinic'])
            ->findOrFail($id);
    }

    /**
     * Check if a slot is available.
     */
    public function isSlotAvailable(int $doctorId, string $date, string $startTime, ?int $excludeId = null): bool
    {
        $query = Appointment::where('doctor_id', $doctorId)
            ->where('appointment_date', $date)
            ->where('start_time', $startTime)
            ->whereIn('status', [Appointment::STATUS_PENDING, Appointment::STATUS_CONFIRMED]);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return ! $query->exists();
    }
}
