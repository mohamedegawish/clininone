<?php

namespace App\Http\Controllers\Api\v1\Clinic;

use App\Http\Controllers\Controller;
use App\Models\core\Appointment;
use App\Traits\HttpResponses;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QueueController extends Controller
{
    use HttpResponses;

    /**
     * Resolve the clinic ID from the authenticated user.
     */
    private function resolveClinicId(Request $request): ?int
    {
        $user = $request->user();

        return $user?->clinic_id
            ?? $user?->doctor?->clinics()->value('clinics.id');
    }

    /**
     * Get the current queue state for a specific doctor today.
     */
    public function show(int $doctorId): JsonResponse
    {
        $today = Carbon::today()->format('Y-m-d');

        $clinicId = $this->resolveClinicId(request());

        if (! $clinicId) {
            return $this->error('Clinic could not be determined', 400);
        }

        $currentAppointment = Appointment::where('clinic_id', $clinicId)
            ->where('appointment_date', $today)
            ->whereIn('status', [Appointment::STATUS_PENDING, Appointment::STATUS_CONFIRMED])
            ->orderBy('queue_number', 'asc')
            ->orderBy('start_time', 'asc')
            ->with('patient')
            ->first();

        $remainingCount = Appointment::where('clinic_id', $clinicId)
            ->where('appointment_date', $today)
            ->whereIn('status', [Appointment::STATUS_PENDING, Appointment::STATUS_CONFIRMED])
            ->count();

        return $this->success([
            'current_appointment' => $currentAppointment,
            'remaining_count' => $remainingCount,
        ]);
    }

    /**
     * Advance the queue (complete the current appointment and get the next one).
     */
    public function advance(int $doctorId): JsonResponse
    {
        $today = Carbon::today()->format('Y-m-d');
        $clinicId = $this->resolveClinicId(request());

        if (! $clinicId) {
            return $this->error('Clinic could not be determined', 400);
        }

        $currentAppointment = Appointment::where('clinic_id', $clinicId)
            ->where('appointment_date', $today)
            ->whereIn('status', [Appointment::STATUS_PENDING, Appointment::STATUS_CONFIRMED])
            ->orderBy('queue_number', 'asc')
            ->orderBy('start_time', 'asc')
            ->first();

        if ($currentAppointment) {
            $currentAppointment->update(['status' => Appointment::STATUS_COMPLETED]);
        }

        return $this->show($doctorId);
    }
}
