<?php

namespace App\Http\Controllers\Web\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\core\Clinic;
use App\Models\core\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PatientManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $clinicId = request()->integer('clinic_id');

        $patients = Patient::query()
            ->with('clinic:id,name')
            ->withCount('appointments')
            ->withMax('appointments', 'appointment_date')
            ->when($clinicId, fn ($query) => $query->where('clinic_id', $clinicId))
            ->latest()
            ->paginate(12);

        $clinics = Clinic::query()->latest()->get(['id', 'name']);

        return view('admin.patients.index', compact('patients', 'clinics', 'clinicId'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $clinics = Clinic::query()->latest()->get(['id', 'name']);
        return view('admin.patients.create', compact('clinics'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
            'gender' => 'required|in:male,female',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string|max:255',
            'clinic_id' => 'required|exists:clinics,id',
        ]);

        Patient::create([
            ...$validated,
            'english_name' => $validated['full_name'],
        ]);

        return redirect()->route('admin.patients.index')->with('success', 'تم إنشاء المريض بنجاح.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Patient $patient): View
    {
        $patient->load([
            'clinic:id,name',
            'appointments' => fn ($query) => $query
                ->with(['doctor:id,name', 'clinic:id,name'])
                ->latest('appointment_date'),
        ]);

        return view('admin.patients.show', compact('patient'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Patient $patient): View
    {
        $clinics = Clinic::query()->latest()->get(['id', 'name']);
        return view('admin.patients.edit', compact('patient', 'clinics'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Patient $patient): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
            'gender' => 'required|in:male,female',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string|max:255',
            'clinic_id' => 'required|exists:clinics,id',
        ]);

        $patient->update([
            ...$validated,
            'english_name' => $validated['full_name'],
        ]);

        return redirect()->route('admin.patients.index')->with('success', 'تم تحديث بيانات المريض بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient): RedirectResponse
    {
        $patient->delete();
        return redirect()->route('admin.patients.index')->with('success', 'تم حذف المريض.');
    }
}
