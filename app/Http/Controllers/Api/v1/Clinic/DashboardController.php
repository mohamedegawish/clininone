<?php

namespace App\Http\Controllers\Api\v1\Clinic;

use App\Http\Controllers\Controller;
use App\Models\core\Appointment;
use App\Models\core\ClinicNotification;
use App\Models\core\Expense;
use App\Models\core\Patient;
use App\Models\Scopes\ClinicScope;
use App\Traits\HttpResponses;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Full dashboard data for the mobile home screen.
 * Mirrors Web\Clinic\DashboardController plus extra analytics for mobile.
 */
class DashboardController extends Controller
{
    use HttpResponses;

    public function index(Request $request): JsonResponse
    {
        $clinicId = $this->resolveClinicId($request);
        if (! $clinicId) {
            return $this->error('Clinic could not be determined.', 400);
        }

        $user   = $request->user();
        $doctor = $user->doctor;
        $clinic = \App\Models\core\Clinic::find($clinicId);

        $today         = Carbon::today();
        $startOfMonth  = Carbon::now()->startOfMonth();

        // ── Today's counters ─────────────────────────────────────────────────
        $todayAppts = Appointment::withoutGlobalScope(ClinicScope::class)
            ->where('clinic_id', $clinicId)
            ->whereDate('appointment_date', $today);

        $todayTotal     = (clone $todayAppts)->count();
        $todayCompleted = (clone $todayAppts)->where('status', Appointment::STATUS_COMPLETED)->count();
        $todayPending   = (clone $todayAppts)
            ->whereIn('status', [Appointment::STATUS_PENDING, Appointment::STATUS_CONFIRMED])
            ->count();
        $todayRevenue   = (clone $todayAppts)->where('is_paid', true)->sum('total_price');

        // ── All-time clinic counters ──────────────────────────────────────────
        $totalPatients     = Patient::withoutGlobalScope(ClinicScope::class)
            ->where('clinic_id', $clinicId)->count();

        $totalAppointments = Appointment::withoutGlobalScope(ClinicScope::class)
            ->where('clinic_id', $clinicId)->count();

        $totalRevenue = Appointment::withoutGlobalScope(ClinicScope::class)
            ->where('clinic_id', $clinicId)
            ->where('is_paid', true)
            ->sum('total_price');

        $monthRevenue = Appointment::withoutGlobalScope(ClinicScope::class)
            ->where('clinic_id', $clinicId)
            ->where('is_paid', true)
            ->whereBetween('appointment_date', [$startOfMonth, now()])
            ->sum('total_price');

        $totalExpenses = Expense::withoutGlobalScope(ClinicScope::class)
            ->where('clinic_id', $clinicId)->sum('amount');

        $monthExpenses = Expense::withoutGlobalScope(ClinicScope::class)
            ->where('clinic_id', $clinicId)
            ->whereBetween('date', [$startOfMonth, now()])
            ->sum('amount');

        // ── Recent appointments (last 5, today) ──────────────────────────────
        $recentAppointments = Appointment::withoutGlobalScope(ClinicScope::class)
            ->where('clinic_id', $clinicId)
            ->whereDate('appointment_date', $today)
            ->with(['patient:id,full_name', 'doctor:id,name'])
            ->orderBy('start_time')
            ->limit(10)
            ->get()
            ->map(fn ($a) => [
                'id'               => $a->id,
                'patient_name'     => $a->patient?->full_name ?? '—',
                'doctor_name'      => $a->doctor?->name ?? '—',
                'start_time'       => $a->start_time,
                'appointment_date' => $a->appointment_date?->format('Y-m-d'),
                'status'           => $a->status,
                'queue_number'     => $a->queue_number,
                'is_paid'          => $a->is_paid,
                'total_price'      => (float) $a->total_price,
                'type'             => $a->type,
            ]);

        // ── Monthly chart (last 6 months) ─────────────────────────────────────
        $monthlyChart = [];
        for ($i = 5; $i >= 0; $i--) {
            $m    = Carbon::now()->subMonths($i);
            $from = $m->copy()->startOfMonth();
            $to   = $m->copy()->endOfMonth();

            $row = Appointment::withoutGlobalScope(ClinicScope::class)
                ->where('clinic_id', $clinicId)
                ->whereBetween('appointment_date', [$from, $to])
                ->whereNotIn('status', [Appointment::STATUS_CANCELLED])
                ->selectRaw('COUNT(*) as count, SUM(CASE WHEN is_paid = 1 THEN total_price ELSE 0 END) as revenue')
                ->first();

            $monthlyChart[] = [
                'month'   => $m->format('Y-m'),
                'label'   => $m->format('M Y'),
                'count'   => (int) ($row->count ?? 0),
                'revenue' => (float) ($row->revenue ?? 0),
            ];
        }

        // ── Unread notifications count ────────────────────────────────────────
        $unreadNotifications = ClinicNotification::withoutGlobalScope(ClinicScope::class)
            ->where('clinic_id', $clinicId)
            ->where('is_read', false)
            ->count();

        // ── Subscription status ───────────────────────────────────────────────
        $subscription = $clinic?->activeSubscription;

        return $this->success([
            'clinic' => [
                'id'            => $clinic?->id,
                'name'          => $clinic?->name,
                'logo'          => $clinic?->logoUrl(),
                'primary_color' => $clinic?->primaryColor(),
                'phone'         => $clinic?->phone,
                'address'       => $clinic?->address,
            ],
            'doctor' => $doctor ? [
                'id'             => $doctor->id,
                'name'           => $doctor->name,
                'specialty'      => $doctor->specialty,
                'price'          => (float) $doctor->price,
                'followup_price' => (float) $doctor->followup_price,
            ] : null,
            'today' => [
                'total'     => $todayTotal,
                'completed' => $todayCompleted,
                'pending'   => $todayPending,
                'revenue'   => (float) $todayRevenue,
            ],
            'totals' => [
                'patients'          => $totalPatients,
                'appointments'      => $totalAppointments,
                'revenue'           => (float) $totalRevenue,
                'revenue_month'     => (float) $monthRevenue,
                'expenses'          => (float) $totalExpenses,
                'expenses_month'    => (float) $monthExpenses,
                'net_profit'        => (float) ($totalRevenue - $totalExpenses),
                'net_profit_month'  => (float) ($monthRevenue - $monthExpenses),
            ],
            'recent_appointments' => $recentAppointments,
            'monthly_chart'       => $monthlyChart,
            'unread_notifications'=> $unreadNotifications,
            'subscription' => $subscription ? [
                'plan'    => $subscription->plan?->name,
                'status'  => $subscription->status,
                'ends_at' => $subscription->end_at?->toDateString(),
                'expired' => $subscription->end_at?->isPast() ?? false,
            ] : null,
        ]);
    }

    private function resolveClinicId(Request $request): ?int
    {
        $user = $request->user();
        return $user?->clinic_id ?? $user?->doctor?->clinics()->value('clinics.id');
    }
}
