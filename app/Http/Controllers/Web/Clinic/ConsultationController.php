<?php

namespace App\Http\Controllers\Web\Clinic;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\Clinic\Concerns\ResolvesClinic;
use App\Models\core\Appointment;
use App\Models\core\Consultation;
use App\Models\core\ConsultationMedication;
use App\Models\core\Diagnosis;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConsultationController extends Controller
{
    use ResolvesClinic;

    public function create(Appointment $appointment): View
    {
        $clinic   = $this->resolveClinic();
        abort_if($appointment->clinic_id !== $clinic->id, 403);

        $patient   = $appointment->patient;
        $diagnoses = Diagnosis::where('clinic_id', $clinic->id)->orderBy('name')->get();

        return view('clinic.consultations.create', compact('appointment', 'patient', 'diagnoses', 'clinic'));
    }

    public function store(Request $request, Appointment $appointment)
    {
        $clinicId = $this->resolveClinic()->id;
        abort_if($appointment->clinic_id !== $clinicId, 403); // @phpstan-ignore-line

        $validated = $request->validate([
            'symptoms'    => 'nullable|string',
            'diagnosis'   => 'required|string',
            'treatment'   => 'nullable|string',
            'notes'       => 'nullable|string',
            'bp'          => 'nullable|string|max:20',
            'temp'        => 'nullable|string|max:10',
            'pulse'       => 'nullable|string|max:10',
            'hr'          => 'nullable|string|max:10',
            'rr'          => 'nullable|string|max:10',
            'spo2'        => 'nullable|string|max:10',
            'weight'      => 'nullable|string|max:10',
            'height'      => 'nullable|string|max:10',
            'medications'                  => 'nullable|array',
            'medications.*.medication_id'  => 'nullable|integer|exists:medications,id',
            'medications.*.name'           => 'nullable|string|max:255',
            'medications.*.dosage'         => 'nullable|string|max:100',
            'medications.*.generic'        => 'nullable|string|max:255',
            'medications.*.frequency'      => 'nullable|string|max:100',
            'medications.*.route'          => 'nullable|string|max:100',
            'medications.*.duration'       => 'nullable|string|max:100',
            'medications.*.instructions'   => 'nullable|string',
        ]);

        $consultation = Consultation::create([
            'appointment_id' => $appointment->id,
            'patient_id'     => $appointment->patient_id,
            'doctor_id'      => $appointment->doctor_id,
            'clinic_id'      => $appointment->clinic_id,
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

            $medId = isset($med['medication_id']) && $med['medication_id']
                ? (int) $med['medication_id']
                : null;

            ConsultationMedication::create([
                'consultation_id' => $consultation->id,
                'medication_id'   => $medId,
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

        $appointment->update(['status' => Appointment::STATUS_COMPLETED]);

        return redirect()->route('clinic.consultations.show', $consultation->id)
            ->with('success', 'Consultation saved successfully.');
    }

    public function show(Consultation $consultation): View
    {
        $clinic = $this->resolveClinic();
        abort_if($consultation->clinic_id !== $clinic->id, 403);

        return view('clinic.consultations.show', compact('consultation', 'clinic'));
    }

    public function printView(Consultation $consultation): View
    {
        $clinicId = $this->resolveClinic()->id;
        abort_if($consultation->clinic_id !== $clinicId, 403);

        return view('prescriptions.print', compact('consultation'));
    }

    public function storeDiagnosis(Request $request)
    {
        $clinicId = $this->resolveClinic()->id;

        $request->validate(['name' => 'required|string|max:255']);

        $diagnosis = Diagnosis::firstOrCreate(
            ['clinic_id' => $clinicId, 'name' => trim($request->name)]
        );

        return response()->json(['id' => $diagnosis->id, 'name' => $diagnosis->name]);
    }
}
