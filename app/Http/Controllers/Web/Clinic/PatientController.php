<?php

namespace App\Http\Controllers\Web\Clinic;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\Clinic\Concerns\ResolvesClinic;
use App\Models\core\Patient;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class PatientController extends Controller
{
    use ResolvesClinic;

    public function index(Request $request): View
    {
        $clinic = $this->resolveClinic();
        
        $search = $request->query('search');
        
        $patients = Patient::query()
            ->where('clinic_id', $clinic->id)
            ->when($search, function ($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('full_name', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->withCount('appointments')
            ->withMax('appointments', 'appointment_date')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $counts = Patient::query()
            ->where('clinic_id', $clinic->id)
            ->selectRaw('COUNT(*) as total, SUM(status = "active") as active, SUM(status != "active") as inactive')
            ->first();

        $summary = [
            'total'    => (int) $counts->total,
            'active'   => (int) $counts->active,
            'inactive' => (int) $counts->inactive,
        ];

        return view('clinic.patients.index', compact('patients', 'summary', 'search'));
    }

    public function create(): View
    {
        return view('clinic.patients.create');
    }

    public function store(Request $request)
    {
        $clinic = $this->resolveClinic();
        
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'english_name' => 'required|string|max:255',
            'phone' => [
                'required',
                'string',
                'max:20',
                Rule::unique('patients')->where(fn ($q) => $q->where('clinic_id', $clinic->id))
            ],
            'ssn' => 'nullable|string|max:50',
            'birth_date' => 'nullable|date',
            'age' => 'nullable|integer',
            'gender' => 'required|in:male,female',
            'nationality' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'company' => 'nullable|string|max:255',
            'policy_name' => 'nullable|string|max:255',
            'class' => 'nullable|string|max:50',
            'card_no' => 'nullable|string|max:100',
        ]);

        // Auto-calculate age or birth date
        if (!empty($validated['birth_date']) && empty($validated['age'])) {
            $validated['age'] = \Carbon\Carbon::parse($validated['birth_date'])->age;
        } elseif (!empty($validated['age']) && empty($validated['birth_date'])) {
            $validated['birth_date'] = now()->subYears($validated['age'])->startOfYear()->format('Y-m-d');
        }

        $validated['clinic_id'] = $clinic->id;
        $validated['status'] = 'active';

        Patient::create($validated);

        return redirect()->route('clinic.patients.index')->with('success', 'Patient added successfully.');
    }

    public function show(Patient $patient): View
    {
        $clinic = $this->resolveClinic();
        abort_if($patient->clinic_id !== $clinic->id, 403);

        $patient->load(['appointments' => fn ($query) => $query->with('doctor:id,name')->latest('appointment_date')]);

        return view('clinic.patients.show', compact('patient'));
    }

    public function edit(Patient $patient): View
    {
        $clinic = $this->resolveClinic();
        abort_if($patient->clinic_id !== $clinic->id, 403);

        return view('clinic.patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $clinic = $this->resolveClinic();
        abort_if($patient->clinic_id !== $clinic->id, 403);

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'english_name' => 'required|string|max:255',
            'phone' => [
                'required',
                'string',
                'max:20',
                Rule::unique('patients')->where(fn ($q) => $q->where('clinic_id', $clinic->id))->ignore($patient->id)
            ],
            'ssn' => 'nullable|string|max:50',
            'birth_date' => 'nullable|date',
            'age' => 'nullable|integer',
            'gender' => 'required|in:male,female',
            'nationality' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'company' => 'nullable|string|max:255',
            'policy_name' => 'nullable|string|max:255',
            'class' => 'nullable|string|max:50',
            'card_no' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive',
        ]);

        // Auto-calculate age or birth date
        if (!empty($validated['birth_date']) && empty($validated['age'])) {
            $validated['age'] = \Carbon\Carbon::parse($validated['birth_date'])->age;
        } elseif (!empty($validated['age']) && empty($validated['birth_date'])) {
            $validated['birth_date'] = now()->subYears($validated['age'])->startOfYear()->format('Y-m-d');
        }

        $patient->update($validated);

        return redirect()->route('clinic.patients.index')->with('success', 'Patient updated successfully.');
    }

    public function destroy(Patient $patient)
    {
        $clinic = $this->resolveClinic();
        abort_if($patient->clinic_id !== $clinic->id, 403);

        $patient->delete();

        return redirect()->route('clinic.patients.index')->with('success', 'Patient deleted successfully.');
    }

}
