<?php

namespace App\Http\Controllers\Web\Clinic;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\Clinic\Concerns\ResolvesClinic;
use App\Models\core\Appointment;
use App\Models\core\Expense;
use App\Models\core\Patient;
use App\Models\Scopes\ClinicScope;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    use ResolvesClinic;

    public function index(Request $request): View
    {
        $clinic   = $this->resolveClinic();
        $clinicId = $clinic->id;

        // Default to monthly so there's always meaningful data visible
        $type = $request->query('type', 'monthly');

        /*
         * Use withoutGlobalScope to avoid double-filtering:
         * the controller already applies clinic_id explicitly, so the
         * BelongsToClinic global scope would produce a redundant (and
         * potentially mismatched) second WHERE clause.
         */
        $apptBase = Appointment::withoutGlobalScope(ClinicScope::class)
            ->where('clinic_id', $clinicId)
            ->whereNotIn('status', [Appointment::STATUS_CANCELLED]);

        $expBase  = Expense::withoutGlobalScope(ClinicScope::class)
            ->where('clinic_id', $clinicId);

        // ── Date range ──────────────────────────────────────────────────────
        [$dateFrom, $dateTo] = $this->dateRange($type);

        $apptQuery = (clone $apptBase)
            ->whereBetween('appointment_date', [$dateFrom, $dateTo]);

        $expQuery = (clone $expBase)
            ->whereBetween('date', [$dateFrom, $dateTo]);

        // ── Fetch data ───────────────────────────────────────────────────────
        $appointments = $apptQuery
            ->select([
                'id', 'patient_id', 'doctor_id', 'clinic_id',
                'appointment_date', 'start_time', 'end_time',
                'status', 'type', 'is_paid', 'total_price',
            ])
            ->with([
                'patient:id,full_name',
                'doctor:id,name',
            ])
            ->orderByDesc('appointment_date')
            ->orderByDesc('start_time')
            ->get();

        $expenses = $expQuery
            ->orderByDesc('date')
            ->get();

        // ── Summary stats ────────────────────────────────────────────────────
        $completedAppointments = $appointments
            ->whereIn('status', [Appointment::STATUS_COMPLETED, Appointment::STATUS_CONFIRMED])
            ->count();

        $totalRevenue  = $appointments->where('is_paid', true)->sum('total_price');
        $totalExpenses = $expenses->sum('amount');
        $netProfit     = $totalRevenue - $totalExpenses;

        // Total unique patients (period-independent, clinic-wide)
        $totalPatients = Patient::withoutGlobalScope(ClinicScope::class)
            ->where('clinic_id', $clinicId)
            ->count();

        // ── Monthly chart data (last 6 months) ───────────────────────────────
        $monthlyChart = $this->buildMonthlyChart($clinicId);

        // ── Top patients by appointment count ────────────────────────────────
        $topPatients = $appointments
            ->groupBy('patient_id')
            ->map(fn ($group) => [
                'name'  => $group->first()->patient?->full_name ?? '—',
                'count' => $group->count(),
                'paid'  => $group->where('is_paid', true)->sum('total_price'),
            ])
            ->sortByDesc('count')
            ->take(5)
            ->values();

        return view('clinic.reports.index', compact(
            'clinic',
            'type',
            'dateFrom',
            'dateTo',
            'appointments',
            'expenses',
            'completedAppointments',
            'totalRevenue',
            'totalExpenses',
            'netProfit',
            'totalPatients',
            'monthlyChart',
            'topPatients',
        ));
    }

    /** Return [Carbon $from, Carbon $to] for the selected period. */
    private function dateRange(string $type): array
    {
        return match ($type) {
            'daily'   => [now()->startOfDay(),   now()->endOfDay()],
            'yearly'  => [now()->startOfYear(),  now()->endOfYear()],
            default   => [now()->startOfMonth(), now()->endOfMonth()], // monthly
        };
    }

    /** Build revenue + appointment count for the last 6 calendar months. */
    private function buildMonthlyChart(int $clinicId): array
    {
        $months = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $from  = $month->copy()->startOfMonth();
            $to    = $month->copy()->endOfMonth();

            $rows = Appointment::withoutGlobalScope(ClinicScope::class)
                ->where('clinic_id', $clinicId)
                ->whereBetween('appointment_date', [$from, $to])
                ->whereNotIn('status', [Appointment::STATUS_CANCELLED])
                ->selectRaw('COUNT(*) as count, SUM(CASE WHEN is_paid = 1 THEN total_price ELSE 0 END) as revenue')
                ->first();

            $months[] = [
                'label'   => $month->translatedFormat('M Y'),
                'count'   => (int) ($rows->count ?? 0),
                'revenue' => (float) ($rows->revenue ?? 0),
            ];
        }

        return $months;
    }
}
