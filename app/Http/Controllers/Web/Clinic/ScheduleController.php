<?php

namespace App\Http\Controllers\Web\Clinic;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\Clinic\Concerns\ResolvesClinic;
use App\Models\core\DoctorSchedule;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    use ResolvesClinic;

    public function index(): View
    {
        $clinic   = $this->resolveClinic();
        $clinicId = $clinic->id;
        $doctorId = auth()->user()?->doctor?->id;

        $schedules = DoctorSchedule::where('doctor_id', $doctorId)
            ->where('clinic_id', $clinicId)
            ->get()
            ->keyBy('day_of_week');

        return view('clinic.schedule.index', compact('schedules'));
    }

    public function store(Request $request)
    {
        $clinicId = $this->resolveClinic()->id;
        $doctorId = auth()->user()?->doctor?->id;

        $validated = $request->validate([
            'schedules' => 'required|array',
            'schedules.*.day_of_week' => 'required|integer|min:0|max:6',
            'schedules.*.start_time' => 'nullable|date_format:H:i',
            'schedules.*.end_time' => 'nullable|date_format:H:i|after:schedules.*.start_time',
            'schedules.*.slot_duration' => 'nullable|integer|min:5',
            'schedules.*.is_active' => 'nullable|boolean',
        ]);

        foreach ($validated['schedules'] as $scheduleData) {
            $isActive = $scheduleData['is_active'] ?? false;
            
            DoctorSchedule::updateOrCreate(
                [
                    'doctor_id' => $doctorId,
                    'clinic_id' => $clinicId,
                    'day_of_week' => $scheduleData['day_of_week'],
                ],
                [
                    'start_time' => $isActive ? $scheduleData['start_time'] : null,
                    'end_time' => $isActive ? $scheduleData['end_time'] : null,
                    'slot_duration' => $scheduleData['slot_duration'] ?? 30,
                    'is_active' => $isActive,
                ]
            );
        }

        return redirect()->route('clinic.schedule.index')->with('success', 'Schedule updated successfully.');
    }
}
