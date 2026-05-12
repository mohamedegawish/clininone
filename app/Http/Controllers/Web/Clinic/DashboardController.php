<?php

namespace App\Http\Controllers\Web\Clinic;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\Clinic\Concerns\ResolvesClinic;
use App\Models\core\Appointment;
use App\Models\core\Clinic;
use App\Models\core\Patient;
use App\Models\saas\Subscription;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    use ResolvesClinic;
    public function index(): View
    {
        $clinic = $this->resolveClinic();

        $completedCount = Appointment::query()
            ->where('clinic_id', $clinic->id)
            ->whereDate('appointment_date', now())
            ->where('status', Appointment::STATUS_COMPLETED)
            ->count();

        $stats = [
            'total_patients' => Patient::query()->where('clinic_id', $clinic->id)->count(),
            'today_appointments' => Appointment::query()->where('clinic_id', $clinic->id)->whereDate('appointment_date', now())->count(),
            'completed_today' => $completedCount,
            'pending_today' => Appointment::query()
                ->where('clinic_id', $clinic->id)
                ->whereDate('appointment_date', now())
                ->whereIn('status', [Appointment::STATUS_PENDING, Appointment::STATUS_CONFIRMED])
                ->count(),
            'revenue_today' => Appointment::query()
                ->where('clinic_id', $clinic->id)
                ->whereDate('appointment_date', now())
                ->where('is_paid', true)
                ->sum('total_price'),
        ];

        $recentAppointments = Appointment::query()
            ->with(['patient:id,full_name', 'doctor:id,name'])
            ->where('clinic_id', $clinic->id)
            ->latest('appointment_date')
            ->limit(5)
            ->get();

        return view('clinic.dashboard', compact('clinic', 'stats', 'recentAppointments'));
    }

}
