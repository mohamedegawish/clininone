<?php

namespace App\Services;

use App\Models\core\DoctorSchedule;
use Illuminate\Support\Facades\DB;

class DoctorScheduleService
{
    /**
     * Get schedules for a doctor in a clinic.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, DoctorSchedule>
     */
    public function list(int $doctorId, int $clinicId): \Illuminate\Database\Eloquent\Collection
    {
        return DoctorSchedule::where('doctor_id', $doctorId)
            ->where('clinic_id', $clinicId)
            ->orderBy('day_of_week')
            ->get();
    }

    /**
     * Create or update a schedule entry.
     */
    public function upsert(array $data, int $clinicId): DoctorSchedule
    {
        return DB::transaction(function () use ($data, $clinicId) {
            return DoctorSchedule::updateOrCreate(
                [
                    'doctor_id' => $data['doctor_id'],
                    'clinic_id' => $clinicId,
                    'day_of_week' => $data['day_of_week'],
                ],
                [
                    'start_time' => $data['start_time'],
                    'end_time' => $data['end_time'],
                    'slot_duration' => $data['slot_duration'] ?? 30,
                    'is_active' => $data['is_active'] ?? true,
                ],
            );
        });
    }

    /**
     * Bulk set schedule for a doctor (replace all days at once).
     *
     * @param  array<int, array{day_of_week: int, start_time: string, end_time: string, slot_duration?: int, is_active?: bool}>  $schedules
     * @return \Illuminate\Database\Eloquent\Collection<int, DoctorSchedule>
     */
    public function bulkSet(int $doctorId, int $clinicId, array $schedules): \Illuminate\Database\Eloquent\Collection
    {
        return DB::transaction(function () use ($doctorId, $clinicId, $schedules) {
            foreach ($schedules as $schedule) {
                DoctorSchedule::updateOrCreate(
                    [
                        'doctor_id' => $doctorId,
                        'clinic_id' => $clinicId,
                        'day_of_week' => $schedule['day_of_week'],
                    ],
                    [
                        'start_time' => $schedule['start_time'],
                        'end_time' => $schedule['end_time'],
                        'slot_duration' => $schedule['slot_duration'] ?? 30,
                        'is_active' => $schedule['is_active'] ?? true,
                    ],
                );
            }

            return $this->list($doctorId, $clinicId);
        });
    }

    /**
     * Delete a single schedule entry.
     */
    public function delete(int $scheduleId): bool
    {
        return DoctorSchedule::findOrFail($scheduleId)->delete();
    }
}
