<?php

namespace App\Http\Controllers\Api\v1\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\PublicBookAppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Services\AppointmentService;
use App\Services\PublicBookingService;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppointmentBookingController extends Controller
{
    use HttpResponses;

    public function __construct(
        protected AppointmentService $appointmentService,
        protected PublicBookingService $publicBookingService
    ) {}

    /**
     * Get available slots for a doctor on a specific date (Public).
     */
    public function availableSlots(Request $request): JsonResponse
    {
        $request->validate([
            'doctor_id' => 'required|integer|exists:doctors,id',
            'date' => 'required|date|after_or_equal:today',
            'clinic_id' => 'nullable|integer|exists:clinics,id',
        ]);

        $doctorId = $request->input('doctor_id');
        $clinicId = $request->input('clinic_id');

        if (!$clinicId) {
            $doctor = \App\Models\core\Doctor::find($doctorId);
            $clinicId = $doctor->clinics()->first()?->id;
        }

        if (!$clinicId) {
            return $this->error('No clinic found for this doctor', 404);
        }

        $slots = $this->appointmentService->getAvailableSlots(
            (int) $doctorId,
            (int) $clinicId,
            $request->input('date'),
        );

        return $this->success([
            'clinic_id' => (int) $clinicId,
            'doctor_id' => (int) $doctorId,
            'date' => $request->input('date'),
            'available_slots' => $slots,
            'total_slots' => count($slots),
        ]);
    }

    /**
     * Book a new appointment without authentication (Public).
     */
    public function store(PublicBookAppointmentRequest $request): JsonResponse
    {
        $validated = $request->validated();
        
        if (empty($validated['clinic_id'])) {
            $doctor = \App\Models\core\Doctor::find($validated['doctor_id']);
            $validated['clinic_id'] = $doctor->clinics()->first()?->id;
        }

        if (empty($validated['clinic_id'])) {
            return $this->error('No clinic found for this doctor', 404);
        }

        $appointment = $this->publicBookingService->book($validated);

        return $this->success(
            new AppointmentResource($appointment->load(['patient', 'doctor', 'clinic'])),
            'Appointment booked successfully',
            201,
        );
    }
}
