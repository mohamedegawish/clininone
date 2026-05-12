<?php

namespace App\Http\Controllers\Web\Clinic;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\Clinic\Concerns\ResolvesClinic;
use App\Models\core\Appointment;
use App\Models\core\ClinicNotification;
use App\Models\core\Doctor;
use App\Models\core\Patient;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    use ResolvesClinic;

    public function index(Request $request): View
    {
        $clinicId = $this->resolveClinic()->id;

        $query = Appointment::with(['patient:id,full_name,phone', 'doctor:id,name', 'consultation:id,appointment_id'])
            ->select(['id', 'patient_id', 'doctor_id', 'clinic_id', 'appointment_date', 'start_time', 'status', 'source', 'is_paid', 'total_price', 'queue_number', 'type', 'created_at'])
            ->where('clinic_id', $clinicId);

        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('appointment_date', $request->date);
        }

        $appointments = $query->latest()->paginate(15);

        return view('clinic.appointments.index', compact('appointments'));
    }

    public function create(): View
    {
        $clinicId = $this->resolveClinic()->id;

        $patients = Patient::where('clinic_id', $clinicId)->get();
        $doctors  = Doctor::whereHas('clinics', fn ($q) => $q->where('clinics.id', $clinicId))->get();

        $doctorServices = \App\Models\core\DoctorService::where('clinic_id', $clinicId)->get();

        return view('clinic.appointments.create', compact('patients', 'doctors', 'doctorServices'));
    }

    public function store(Request $request)
    {
        $clinic   = $this->resolveClinic();
        $clinicId = $clinic->id;

        $validated = $request->validate([
            'patient_id'       => 'required|exists:patients,id',
            'doctor_id'        => 'required|exists:doctors,id',
            'appointment_date' => 'required|date',
            'start_time'       => 'required|date_format:H:i',
            'source'           => 'required|in:clinic,online',
            'type'             => 'required|in:consultation,followup',
            'is_paid'          => 'boolean',
            'notes'            => 'nullable|string',
            'services'         => 'nullable|array',
            'services.*'       => 'exists:doctor_services,id',
        ]);

        // Ownership check: patient must belong to this clinic
        abort_unless(
            Patient::where('id', $validated['patient_id'])->where('clinic_id', $clinicId)->exists(),
            403,
            'Patient does not belong to this clinic.'
        );

        $doctor = Doctor::findOrFail($validated['doctor_id']);

        $basePrice     = $validated['type'] === 'followup' ? ($doctor->followup_price ?? 0) : ($doctor->price ?? 0);
        $servicesTotal = 0;

        if (! empty($validated['services'])) {
            $servicesTotal = \App\Models\core\DoctorService::whereIn('id', $validated['services'])->sum('price');
        }

        $validated['total_price'] = $basePrice + $servicesTotal;
        $validated['end_time']    = \Carbon\Carbon::parse($validated['start_time'])->addMinutes(30)->format('H:i');
        $validated['clinic_id']   = $clinicId;
        $validated['status']      = Appointment::STATUS_CONFIRMED;
        $validated['is_paid']     = $request->has('is_paid');

        $lastQueue = Appointment::where('clinic_id', $clinicId)
            ->whereDate('appointment_date', $validated['appointment_date'])
            ->max('queue_number');

        $validated['queue_number'] = $lastQueue ? $lastQueue + 1 : 1;

        $appointment = Appointment::create(\Illuminate\Support\Arr::except($validated, ['services']));

        if (! empty($validated['services'])) {
            $prices      = \App\Models\core\DoctorService::whereIn('id', $validated['services'])->pluck('price', 'id');
            $servicesData = $prices->mapWithKeys(fn ($price, $id) => [$id => ['price' => $price]])->all();
            $appointment->services()->sync($servicesData);
        }

        // Notify clinic staff about new appointment
        $patient = Patient::find($validated['patient_id']);
        ClinicNotification::notify(
            clinicId: $clinicId,
            title:    __('New Appointment Booked'),
            message:  __(':patient has a new appointment with Dr. :doctor on :date', [
                'patient' => $patient?->full_name ?? 'Unknown',
                'doctor'  => $doctor->name,
                'date'    => \Carbon\Carbon::parse($validated['appointment_date'])->format('d M Y'),
            ]),
            type:     ClinicNotification::TYPE_APPOINTMENT,
            data:     ['appointment_id' => $appointment->id],
        );

        return redirect()->route('clinic.appointments.index')->with('success', 'Appointment booked successfully.');
    }

    public function confirm(Appointment $appointment)
    {
        $clinicId = $this->resolveClinic()->id;
        abort_if($appointment->clinic_id !== $clinicId, 403);

        if ($appointment->status === Appointment::STATUS_PENDING) {
            $lastQueue = Appointment::where('clinic_id', $clinicId)
                ->whereDate('appointment_date', $appointment->appointment_date)
                ->max('queue_number');

            $appointment->update([
                'status'       => Appointment::STATUS_CONFIRMED,
                'queue_number' => $lastQueue ? $lastQueue + 1 : 1,
            ]);

            return back()->with('success', 'Appointment confirmed and added to queue.');
        }

        return back()->with('error', 'Appointment cannot be confirmed.');
    }

    public function markPaid(Appointment $appointment)
    {
        $clinicId = $this->resolveClinic()->id;
        abort_if($appointment->clinic_id !== $clinicId, 403);

        $totalPrice = $appointment->total_price;
        if ($totalPrice == 0 && $appointment->doctor) {
            $basePrice    = $appointment->type === 'followup' ? ($appointment->doctor->followup_price ?? 0) : ($appointment->doctor->price ?? 0);
            $servicesPrice = $appointment->services()->sum('appointment_service.price');
            $totalPrice    = $basePrice + $servicesPrice;
        }

        $appointment->update(['is_paid' => true, 'total_price' => $totalPrice]);

        // Notify about payment
        ClinicNotification::notify(
            clinicId: $clinicId,
            title:    __('Payment Received'),
            message:  __('Appointment #:id marked as paid. Amount: :amount', [
                'id'     => $appointment->id,
                'amount' => number_format($totalPrice, 2),
            ]),
            type:     ClinicNotification::TYPE_PAYMENT,
            data:     ['appointment_id' => $appointment->id],
        );

        return back()->with('success', 'Appointment marked as paid.');
    }
}
