<?php

namespace App\Http\Controllers\Api\v1\Clinic;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Http\Resources\PatientCollection;
use App\Http\Resources\PatientResource;
use App\Models\core\Patient;
use App\Services\PatientService;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PatientController extends Controller
{
    use HttpResponses, AuthorizesRequests;

    public function __construct(
        protected PatientService $service
    ) {}

    /**
     * Display a listing of the patients.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Patient::class);

        $clinicId = $this->resolveClinicId($request);

        if (! $clinicId) {
            return $this->error('Clinic could not be determined', 400);
        }

        $patients = $this->service->list($request->all(), $clinicId);

        return $this->success(new PatientCollection($patients));
    }

    /**
     * Store a newly created patient in storage.
     */
    public function store(StorePatientRequest $request): JsonResponse
    {
        $this->authorize('create', Patient::class);

        $clinicId = $this->resolveClinicId($request);

        if (! $clinicId) {
            return $this->error('Clinic could not be determined', 400);
        }

        $patient = $this->service->store($request->validated(), $clinicId);

        return $this->success(new PatientResource($patient), 'Patient created successfully', 201);
    }

    /**
     * Display the specified patient.
     */
    public function show(int $id, Request $request): JsonResponse
    {
        $clinicId = $this->resolveClinicId($request);

        if (! $clinicId) {
            return $this->error('Clinic could not be determined', 400);
        }

        $patient = $this->service->show($id, $clinicId);

        $this->authorize('view', $patient);

        return $this->success(new PatientResource($patient));
    }

    /**
     * Update the specified patient in storage.
     */
    public function update(UpdatePatientRequest $request, int $id): JsonResponse
    {
        $clinicId = $this->resolveClinicId($request);
        $patient = $this->service->show($id, $clinicId);

        $this->authorize('update', $patient);

        $updatedPatient = $this->service->update($patient, $request->validated());

        return $this->success(new PatientResource($updatedPatient), 'Patient updated successfully');
    }

    /**
     * Remove the specified patient from storage.
     */
    public function destroy(int $id, Request $request): JsonResponse
    {
        $clinicId = $this->resolveClinicId($request);
        $patient = $this->service->show($id, $clinicId);

        $this->authorize('delete', $patient);

        $this->service->delete($patient);

        return $this->success(null, 'Patient deleted successfully');
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
