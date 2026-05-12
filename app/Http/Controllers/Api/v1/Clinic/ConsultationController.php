<?php

namespace App\Http\Controllers\Api\v1\Clinic;

use App\Http\Controllers\Controller;
use App\Http\Resources\ConsultationResource;
use App\Models\core\Appointment;
use App\Models\core\Consultation;
use App\Models\core\ConsultationMedication;
use App\Models\core\Diagnosis;
use App\Models\Scopes\ClinicScope;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    use HttpResponses;

    /**
     * GET /clinic/consultations
     * List consultations for the clinic (paginated, newest first).
     */
    public function index(Request $request): JsonResponse
    {
        $clinicId = $this->resolveClinicId($request);
        if (! $clinicId) {
            return $this->error('Clinic could not be determined', 400);
        }

        $query = Consultation::withoutGlobalScope(ClinicScope::class)
            ->where('clinic_id', $clinicId)
            ->with(['patient:id,full_name,phone', 'doctor:id,name', 'appointment:id,appointment_date,status,type'])
            ->latest();

        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }
        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }
        if ($request->filled('appointment_id')) {
            $query->where('appointment_id', $request->appointment_id);
        }

        $consultations = $query->paginate($request->integer('per_page', 15));

        return $this->success([
            'data'         => ConsultationResource::collection($consultations->items()),
            'total'        => $consultations->total(),
            'current_page' => $consultations->currentPage(),
            'last_page'    => $consultations->lastPage(),
            'per_page'     => $consultations->perPage(),
        ]);
    }

    /**
     * GET /clinic/consultations/{id}
     * Full consultation detail including medications.
     */
    public function show(int $id, Request $request): JsonResponse
    {
        $clinicId = $this->resolveClinicId($request);
        if (! $clinicId) {
            return $this->error('Clinic could not be determined', 400);
        }

        $consultation = Consultation::withoutGlobalScope(ClinicScope::class)
            ->where('clinic_id', $clinicId)
            ->with(['patient', 'doctor', 'appointment', 'medicationRecords'])
            ->findOrFail($id);

        return $this->success(new ConsultationResource($consultation));
    }

    /**
     * POST /clinic/consultations/{appointment_id}
     * Create a consultation for an existing appointment.
     */
    public function store(int $appointmentId, Request $request): JsonResponse
    {
        $clinicId = $this->resolveClinicId($request);
        if (! $clinicId) {
            return $this->error('Clinic could not be determined', 400);
        }

        $appointment = Appointment::withoutGlobalScope(ClinicScope::class)
            ->where('clinic_id', $clinicId)
            ->findOrFail($appointmentId);

        if ($appointment->consultation()->exists()) {
            return $this->error('Consultation already exists for this appointment', 422);
        }

        $validated = $request->validate([
            'symptoms'                    => 'nullable|string',
            'diagnosis'                   => 'required|string',
            'treatment'                   => 'nullable|string',
            'notes'                       => 'nullable|string',
            'bp'                          => 'nullable|string|max:20',
            'temp'                        => 'nullable|string|max:10',
            'pulse'                       => 'nullable|string|max:10',
            'hr'                          => 'nullable|string|max:10',
            'rr'                          => 'nullable|string|max:10',
            'spo2'                        => 'nullable|string|max:10',
            'weight'                      => 'nullable|string|max:10',
            'height'                      => 'nullable|string|max:10',
            'is_paid'                      => 'nullable|boolean',
            'medications'                  => 'nullable|array',
            'medications.*.medication_id'  => 'nullable|integer|exists:medications,id',
            'medications.*.name'           => 'required_with:medications|string|max:255',
            'medications.*.generic'        => 'nullable|string|max:255',
            'medications.*.dosage'         => 'nullable|string|max:100',
            'medications.*.frequency'      => 'nullable|string|max:100',
            'medications.*.route'          => 'nullable|string|max:100',
            'medications.*.duration'       => 'nullable|string|max:100',
            'medications.*.instructions'   => 'nullable|string',
        ]);

        $consultation = \DB::transaction(function () use ($validated, $appointment, $clinicId) {
            $consultation = Consultation::create([
                'appointment_id' => $appointment->id,
                'patient_id'     => $appointment->patient_id,
                'doctor_id'      => $appointment->doctor_id,
                'clinic_id'      => $clinicId,
                'symptoms'       => $validated['symptoms'] ?? null,
                'diagnosis'      => $validated['diagnosis'],
                'treatment'      => $validated['treatment'] ?? null,
                'notes'          => $validated['notes'] ?? null,
                'bp'             => $validated['bp'] ?? null,
                'temp'           => $validated['temp'] ?? null,
                'pulse'          => $validated['pulse'] ?? null,
                'hr'             => $validated['hr'] ?? null,
                'rr'             => $validated['rr'] ?? null,
                'spo2'           => $validated['spo2'] ?? null,
                'weight'         => $validated['weight'] ?? null,
                'height'         => $validated['height'] ?? null,
            ]);

            foreach (($validated['medications'] ?? []) as $idx => $med) {
                if (empty($med['name'])) {
                    continue;
                }
                ConsultationMedication::create([
                    'consultation_id' => $consultation->id,
                    'medication_id'   => $med['medication_id'] ?? null,
                    'name'            => $med['name'],
                    'generic'         => $med['generic'] ?? null,
                    'dosage'          => $med['dosage'] ?? null,
                    'frequency'       => $med['frequency'] ?? null,
                    'route'           => $med['route'] ?? null,
                    'duration'        => $med['duration'] ?? null,
                    'instructions'    => $med['instructions'] ?? null,
                    'sort_order'      => $idx,
                ]);
            }

            $appointment->update([
                'status'  => Appointment::STATUS_COMPLETED,
                'is_paid' => $validated['is_paid'] ?? $appointment->is_paid,
            ]);

            return $consultation;
        });

        $consultation->load(['patient', 'doctor', 'appointment', 'medicationRecords']);

        return $this->success(
            new ConsultationResource($consultation),
            'Consultation saved successfully.',
            201
        );
    }

    /**
     * GET /clinic/consultations/{id}/print
     * Returns consultation data formatted for prescription printing.
     */
    public function printData(int $id, Request $request): JsonResponse
    {
        $clinicId = $this->resolveClinicId($request);
        if (! $clinicId) {
            return $this->error('Clinic could not be determined', 400);
        }

        $consultation = Consultation::withoutGlobalScope(ClinicScope::class)
            ->where('clinic_id', $clinicId)
            ->with(['patient', 'doctor', 'appointment', 'medicationRecords', 'clinic'])
            ->findOrFail($id);

        return $this->success([
            'consultation' => new ConsultationResource($consultation),
            'clinic' => [
                'name'          => $consultation->clinic?->name,
                'phone'         => $consultation->clinic?->phone,
                'address'       => $consultation->clinic?->address,
                'logo'          => $consultation->clinic?->logo
                    ? asset('storage/' . $consultation->clinic->logo)
                    : null,
                'primary_color' => $consultation->clinic?->primaryColor(),
            ],
        ]);
    }

    /**
     * GET /clinic/diagnoses
     * List clinic-specific diagnoses (for autocomplete).
     */
    public function listDiagnoses(Request $request): JsonResponse
    {
        $clinicId = $this->resolveClinicId($request);
        if (! $clinicId) {
            return $this->error('Clinic could not be determined', 400);
        }

        $diagnoses = Diagnosis::withoutGlobalScope(ClinicScope::class)
            ->where('clinic_id', $clinicId)
            ->when($request->filled('q'), fn ($q) => $q->where('name', 'like', '%' . $request->q . '%'))
            ->orderBy('name')
            ->get(['id', 'name']);

        return $this->success($diagnoses);
    }

    /**
     * POST /clinic/diagnoses
     * Create or find a clinic diagnosis.
     */
    public function storeDiagnosis(Request $request): JsonResponse
    {
        $clinicId = $this->resolveClinicId($request);
        if (! $clinicId) {
            return $this->error('Clinic could not be determined', 400);
        }

        $request->validate(['name' => 'required|string|max:255']);

        $diagnosis = Diagnosis::firstOrCreate(
            ['clinic_id' => $clinicId, 'name' => trim($request->name)]
        );

        return $this->success(['id' => $diagnosis->id, 'name' => $diagnosis->name], '', 201);
    }

    /**
     * GET /clinic/consultations/{id}/prescription
     * Returns the full prescription HTML string for mobile WebView / PDF generation.
     */
    public function prescriptionHtml(int $id, Request $request): JsonResponse
    {
        $clinicId = $this->resolveClinicId($request);
        if (! $clinicId) {
            return $this->error('Clinic could not be determined', 400);
        }

        $consultation = Consultation::withoutGlobalScope(ClinicScope::class)
            ->where('clinic_id', $clinicId)
            ->with(['patient', 'doctor', 'appointment', 'medicationRecords', 'clinic'])
            ->findOrFail($id);

        $html = view('prescriptions.print', compact('consultation'))->render();

        return $this->success(['html' => $html]);
    }

    private function resolveClinicId(Request $request): ?int
    {
        $user = $request->user();
        return $user?->clinic_id ?? $user?->doctor?->clinics()->value('clinics.id');
    }
}
