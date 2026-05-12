<?php

namespace App\Http\Controllers\Api\v1\Clinic;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookAppointmentRequest;
use App\Http\Requests\CancelAppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Models\core\Appointment;
use App\Services\AppointmentService;
use App\Traits\HttpResponses;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    use AuthorizesRequests, HttpResponses;

    public function __construct(
        protected AppointmentService $service
    ) {}

    /**
     * List appointments in the clinic.
     */
    public function index(Request $request): JsonResponse
    {
        $clinicId = $this->resolveClinicId($request);

        if (! $clinicId) {
            return $this->error('Clinic could not be determined', 400);
        }

        $appointments = $this->service->list($request->all(), $clinicId);

        return $this->success(
            AppointmentResource::collection($appointments)->response()->getData(true)
        );
    }

    /**
     * Get available slots for a doctor on a specific date.
     */
    public function availableSlots(Request $request): JsonResponse
    {
        $request->validate([
            'doctor_id' => 'required|integer|exists:doctors,id',
            'date' => 'required|date|after_or_equal:today',
        ]);

        $clinicId = $this->resolveClinicId($request);

        if (! $clinicId) {
            return $this->error('Clinic could not be determined', 400);
        }

        $slots = $this->service->getAvailableSlots(
            $request->input('doctor_id'),
            $clinicId,
            $request->input('date'),
        );

        return $this->success([
            'doctor_id' => (int) $request->input('doctor_id'),
            'date' => $request->input('date'),
            'available_slots' => $slots,
            'total_slots' => count($slots),
        ]);
    }

    /**
     * Book a new appointment.
     */
    public function store(BookAppointmentRequest $request): JsonResponse
    {
        $clinicId = $this->resolveClinicId($request);

        if (! $clinicId) {
            return $this->error('Clinic could not be determined', 400);
        }

        $appointment = $this->service->book($request->validated(), $clinicId);

        return $this->success(
            new AppointmentResource($appointment->load(['patient', 'doctor', 'clinic'])),
            'Appointment booked successfully',
            201,
        );
    }

    /**
     * Show a single appointment.
     */
    public function show(int $id, Request $request): JsonResponse
    {
        $clinicId = $this->resolveClinicId($request);

        if (! $clinicId) {
            return $this->error('Clinic could not be determined', 400);
        }

        $appointment = $this->service->show($id, $clinicId);

        return $this->success(new AppointmentResource($appointment));
    }

    /**
     * Cancel an appointment.
     */
    public function cancel(int $id, CancelAppointmentRequest $request): JsonResponse
    {
        $clinicId = $this->resolveClinicId($request);

        if (! $clinicId) {
            return $this->error('Clinic could not be determined', 400);
        }

        $appointment = $this->service->show($id, $clinicId);

        if (! $appointment->isCancellable()) {
            return $this->error('This appointment cannot be cancelled', 422);
        }

        $cancelled = $this->service->cancel($appointment, $request->input('cancellation_reason'));

        return $this->success(
            new AppointmentResource($cancelled->load(['patient', 'doctor'])),
            'Appointment cancelled successfully',
        );
    }

    /**
     * Confirm an appointment.
     */
    public function confirm(int $id, Request $request): JsonResponse
    {
        $clinicId = $this->resolveClinicId($request);

        if (! $clinicId) {
            return $this->error('Clinic could not be determined', 400);
        }

        $appointment = $this->service->show($id, $clinicId);

        if ($appointment->status !== Appointment::STATUS_PENDING) {
            return $this->error('Only pending appointments can be confirmed', 422);
        }

        $confirmed = $this->service->confirm($appointment);

        return $this->success(
            new AppointmentResource($confirmed->load(['patient', 'doctor'])),
            'Appointment confirmed successfully',
        );
    }

    /**
     * Complete an appointment.
     */
    public function complete(int $id, Request $request): JsonResponse
    {
        $clinicId = $this->resolveClinicId($request);

        if (! $clinicId) {
            return $this->error('Clinic could not be determined', 400);
        }

        $appointment = $this->service->show($id, $clinicId);

        if (! $appointment->isCompletable()) {
            return $this->error('Only confirmed appointments can be completed', 422);
        }

        $completed = $this->service->complete($appointment);

        return $this->success(
            new AppointmentResource($completed->load(['patient', 'doctor'])),
            'Appointment completed successfully',
        );
    }

    /**
     * Resolve the clinic ID from the authenticated user.
     * Never falls back to an arbitrary clinic — returns null and callers abort.
     */
    private function resolveClinicId(Request $request): ?int
    {
        $user = $request->user();

        return $user?->clinic_id
            ?? $user?->doctor?->clinics()->value('clinics.id');
    }
}
