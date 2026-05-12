<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Models\core\Appointment;
use App\Models\core\Clinic;
use App\Models\core\Doctor;
use App\Models\core\Patient;
use App\Traits\HttpResponses;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class StatsController extends Controller
{
    use HttpResponses;

    /**
     * Get system-wide KPIs for super admin.
     */
    public function index(): JsonResponse
    {
        $today = Carbon::today()->format('Y-m-d');

        $appointmentsToday = Appointment::where('appointment_date', $today)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $stats = [
            'total_doctors' => Doctor::count(),
            'total_clinics' => Clinic::count(),
            'total_patients' => Patient::count(),
            'appointments_today' => [
                'total' => array_sum($appointmentsToday),
                'pending' => $appointmentsToday['pending'] ?? 0,
                'confirmed' => $appointmentsToday['confirmed'] ?? 0,
                'completed' => $appointmentsToday['completed'] ?? 0,
                'cancelled' => $appointmentsToday['cancelled'] ?? 0,
            ]
        ];

        return $this->success($stats);
    }
}
