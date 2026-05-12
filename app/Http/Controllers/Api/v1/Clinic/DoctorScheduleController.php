<?php

namespace App\Http\Controllers\Api\v1\Clinic;

use App\Http\Controllers\Controller;
use App\Http\Requests\BulkDoctorScheduleRequest;
use App\Http\Requests\StoreDoctorScheduleRequest;
use App\Http\Resources\DoctorScheduleResource;
use App\Services\DoctorScheduleService;
use App\Traits\HttpResponses;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DoctorScheduleController extends Controller
{
    use AuthorizesRequests, HttpResponses;

    public function __construct(
        protected DoctorScheduleService $service
    ) {}

    /**
     * List schedules for a doctor.
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'doctor_id' => 'required|integer|exists:doctors,id',
        ]);

        $clinicId = $this->resolveClinicId($request);

        if (! $clinicId) {
            return $this->error('Clinic could not be determined', 400);
        }

        $schedules = $this->service->list(
            $request->input('doctor_id'),
            $clinicId,
        );

        return $this->success(DoctorScheduleResource::collection($schedules));
    }

    /**
     * Create or update a single schedule day.
     */
    public function store(StoreDoctorScheduleRequest $request): JsonResponse
    {
        $clinicId = $this->resolveClinicId($request);

        if (! $clinicId) {
            return $this->error('Clinic could not be determined', 400);
        }

        $schedule = $this->service->upsert($request->validated(), $clinicId);

        return $this->success(
            new DoctorScheduleResource($schedule),
            'Schedule saved successfully',
            201,
        );
    }

    /**
     * Bulk set schedules for a doctor (multiple days at once).
     */
    public function bulkSet(BulkDoctorScheduleRequest $request): JsonResponse
    {
        $clinicId = $this->resolveClinicId($request);

        if (! $clinicId) {
            return $this->error('Clinic could not be determined', 400);
        }

        $schedules = $this->service->bulkSet(
            $request->input('doctor_id'),
            $clinicId,
            $request->input('schedules'),
        );

        return $this->success(
            DoctorScheduleResource::collection($schedules),
            'Schedules saved successfully',
        );
    }

    /**
     * Delete a schedule entry.
     */
    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);

        return $this->success(null, 'Schedule deleted successfully');
    }

    /**
     * Resolve the clinic ID from the authenticated user.
     */
    private function resolveClinicId(Request $request): ?int
    {
        $user = $request->user();

        // Super-admin may pass clinic_id explicitly
        if ($user?->role === 'admin' && $request->filled('clinic_id')) {
            return (int) $request->input('clinic_id');
        }

        return $user?->clinic_id
            ?? $user?->doctor?->clinics()->value('clinics.id');
    }
}
