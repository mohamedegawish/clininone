<?php

namespace App\Http\Controllers\Web\Clinic;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\Clinic\Concerns\ResolvesClinic;
use App\Models\core\Appointment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QueueController extends Controller
{
    use ResolvesClinic;

    public function show(): View
    {
        $clinicId = $this->resolveClinic()->id;
        return view('clinic.queue.show', compact('clinicId'));
    }

    public function data(Request $request)
    {
        $clinicId = $this->resolveClinic()->id;

        $baseQuery = Appointment::select(['id', 'patient_id', 'queue_number'])
            ->with('patient:id,full_name')
            ->where('clinic_id', $clinicId)
            ->whereDate('appointment_date', now())
            ->where('status', Appointment::STATUS_CONFIRMED)
            ->orderBy('queue_number', 'asc');

        $currentPatient = (clone $baseQuery)->first();

        $nextPatients = (clone $baseQuery)
            ->where('id', '!=', $currentPatient?->id ?? 0)
            ->limit(5)
            ->get();

        return response()->json([
            'current' => $currentPatient ? [
                'id' => $currentPatient->id,
                'name' => $currentPatient->patient->full_name,
                'queue_number' => $currentPatient->queue_number,
            ] : null,
            'next' => $nextPatients->map(fn ($app) => [
                'id' => $app->id,
                'name' => $app->patient->full_name,
                'queue_number' => $app->queue_number,
            ])
        ]);
    }
}
