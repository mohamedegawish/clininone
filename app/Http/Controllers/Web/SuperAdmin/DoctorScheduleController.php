<?php

namespace App\Http\Controllers\Web\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\core\Doctor;
use App\Models\core\DoctorSchedule;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DoctorScheduleController extends Controller
{
    public function edit(Doctor $doctor): View
    {
        $doctor->load('clinics:id,name');
        $clinicId = $doctor->clinics()->value('clinics.id');

        $schedules = DoctorSchedule::query()
            ->where('doctor_id', $doctor->id)
            ->where('clinic_id', $clinicId)
            ->orderBy('day_of_week')
            ->get()
            ->keyBy('day_of_week');

        return view('admin.doctors.schedule', compact('doctor', 'schedules', 'clinicId'));
    }

    public function update(Request $request, Doctor $doctor): RedirectResponse
    {
        $validated = $request->validate([
            'schedules' => 'required|array',
            'schedules.*.is_active' => 'nullable|boolean',
            'schedules.*.start_time' => 'nullable',
            'schedules.*.end_time' => 'nullable',
            'schedules.*.slot_duration' => 'nullable|integer|min:5|max:180',
        ]);

        $clinicId = $doctor->clinics()->value('clinics.id');

        if (!$clinicId) {
            return redirect()->back()->with('error', 'يجب ربط الطبيب بعيادة أولاً قبل تعديل الجدول.');
        }

        foreach ($validated['schedules'] as $day => $scheduleData) {
            $isActive = (bool) ($scheduleData['is_active'] ?? false);
            
            // Format time to HH:MM if present
            $startTime = $scheduleData['start_time'] ? date('H:i', strtotime($scheduleData['start_time'])) : null;
            $endTime = $scheduleData['end_time'] ? date('H:i', strtotime($scheduleData['end_time'])) : null;

            DoctorSchedule::query()->updateOrCreate(
                [
                    'doctor_id' => $doctor->id,
                    'clinic_id' => $clinicId,
                    'day_of_week' => (int) $day,
                ],
                [
                    'is_active' => $isActive,
                    'start_time' => $isActive ? ($startTime ?? '09:00') : $startTime,
                    'end_time' => $isActive ? ($endTime ?? '17:00') : $endTime,
                    'slot_duration' => (int) ($scheduleData['slot_duration'] ?? 30),
                ]
            );
        }

        return redirect()->route('admin.doctors.schedule.edit', $doctor)->with('success', 'تم تحديث جدول الطبيب بنجاح.');
    }
}
