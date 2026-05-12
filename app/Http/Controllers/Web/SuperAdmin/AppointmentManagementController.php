<?php

namespace App\Http\Controllers\Web\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\core\Appointment;
use App\Models\core\Clinic;
use App\Models\core\Doctor;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppointmentManagementController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'clinic_id' => 'nullable|integer|exists:clinics,id',
            'doctor_id' => 'nullable|integer|exists:doctors,id',
            'status' => 'nullable|in:pending,confirmed,cancelled,completed',
            'date' => 'nullable|date',
        ]);

        $appointments = Appointment::query()
            ->with([
                'patient:id,full_name',
                'doctor:id,name',
                'clinic:id,name',
            ])
            ->when($filters['clinic_id'] ?? null, fn ($query, $value) => $query->where('clinic_id', $value))
            ->when($filters['doctor_id'] ?? null, fn ($query, $value) => $query->where('doctor_id', $value))
            ->when($filters['status'] ?? null, fn ($query, $value) => $query->where('status', $value))
            ->when($filters['date'] ?? null, fn ($query, $value) => $query->whereDate('appointment_date', $value))
            ->latest('appointment_date')
            ->paginate(12);

        $clinics = Clinic::query()->latest()->get(['id', 'name']);
        $doctors = Doctor::query()->latest()->get(['id', 'name']);

        return view('admin.appointments.index', compact('appointments', 'clinics', 'doctors', 'filters'));
    }
}
