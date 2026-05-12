<?php

namespace App\Http\Controllers\Api\v1\Clinic;

use App\Http\Controllers\Controller;
use App\Models\core\Appointment;
use App\Models\core\Expense;
use App\Models\core\Patient;
use App\Models\core\Clinic;
use App\Models\Scopes\ClinicScope;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StatsController extends Controller
{
    use HttpResponses;

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $doctor = $user?->doctor;
        $clinicId = $this->resolveClinicId($request);

        if (!$clinicId) {
            return $this->error('Clinic could not be determined', 400);
        }

        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        // ─── Exactly matching Web DashboardController logic ────────────
        // Web uses: Appointment::where('clinic_id', $clinic->id)
        // WITHOUT any doctor filter — so we do the same.

        $todayAppointments = Appointment::withoutGlobalScope(ClinicScope::class)
            ->where('clinic_id', $clinicId)
            ->whereDate('appointment_date', $today)->count();

        $completedToday = Appointment::withoutGlobalScope(ClinicScope::class)
            ->where('clinic_id', $clinicId)
            ->whereDate('appointment_date', $today)
            ->where('status', Appointment::STATUS_COMPLETED)
            ->count();

        $pendingToday = Appointment::withoutGlobalScope(ClinicScope::class)
            ->where('clinic_id', $clinicId)
            ->whereDate('appointment_date', $today)
            ->whereIn('status', [Appointment::STATUS_PENDING, Appointment::STATUS_CONFIRMED])
            ->count();

        $revenueToday = Appointment::withoutGlobalScope(ClinicScope::class)
            ->where('clinic_id', $clinicId)
            ->whereDate('appointment_date', $today)
            ->where('is_paid', true)
            ->sum('total_price');

        $totalPatients = Patient::withoutGlobalScope(ClinicScope::class)
            ->where('clinic_id', $clinicId)->count();

        $totalAppointments = Appointment::withoutGlobalScope(ClinicScope::class)
            ->where('clinic_id', $clinicId)->count();

        $completedAll = Appointment::withoutGlobalScope(ClinicScope::class)
            ->where('clinic_id', $clinicId)
            ->where('status', Appointment::STATUS_COMPLETED)->count();

        $pendingAll = Appointment::withoutGlobalScope(ClinicScope::class)
            ->where('clinic_id', $clinicId)
            ->whereIn('status', [Appointment::STATUS_PENDING, Appointment::STATUS_CONFIRMED])->count();

        $revenueTotal = Appointment::withoutGlobalScope(ClinicScope::class)
            ->where('clinic_id', $clinicId)
            ->where('is_paid', true)
            ->sum('total_price');

        $revenueMonth = Appointment::withoutGlobalScope(ClinicScope::class)
            ->where('clinic_id', $clinicId)
            ->where('is_paid', true)
            ->where('appointment_date', '>=', $startOfMonth)
            ->sum('total_price');

        $expensesTotal = Expense::withoutGlobalScope(ClinicScope::class)
            ->where('clinic_id', $clinicId)->sum('amount');
        $expensesMonth = Expense::withoutGlobalScope(ClinicScope::class)
            ->where('clinic_id', $clinicId)
            ->where('date', '>=', $startOfMonth)
            ->sum('amount');

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

        // ── Subscription status ───────────────────────────────────────────────
        $clinic       = Clinic::find($clinicId);
        $subscription = $clinic?->activeSubscription;

        return $this->success([
            'appointments' => [
                'total'           => $totalAppointments,
                'today'           => $todayAppointments,
                'pending'         => $pendingAll,
                'completed'       => $completedAll,
                'completed_today' => $completedToday,
                'pending_today'   => $pendingToday,
            ],
            'patients' => [
                'total' => $totalPatients,
            ],
            'financials' => [
                'revenue_today'  => (float) $revenueToday,
                'revenue_total'  => (float) $revenueTotal,
                'revenue_month'  => (float) $revenueMonth,
                'expenses_total' => (float) $expensesTotal,
                'expenses_month' => (float) $expensesMonth,
                'net_income'     => (float) ($revenueTotal - $expensesTotal),
                'net_income_month' => (float) ($revenueMonth - $expensesMonth),
            ],
            'monthly_chart' => $monthlyChart,
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

        return $user?->clinic_id
            ?? $user?->doctor?->clinics()->value('clinics.id');
    }
}
