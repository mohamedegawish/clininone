@extends('layouts.app')

@section('title', __('clinic.reports.title'))

@push('styles')
<style>
/* ── Chart ──────────────────────────────────────────────────────── */
.chart-wrap { position: relative; height: 200px; }
.chart-bars  { display: flex; align-items: flex-end; gap: 8px; height: 100%; padding-bottom: 24px; }
.chart-bar-col { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px; height: 100%; justify-content: flex-end; }
.chart-bar  { width: 100%; border-radius: 6px 6px 0 0; background: var(--primary, #1a56c8); min-height: 4px; transition: height .4s ease; }
.chart-bar:hover { opacity: .8; }
.chart-label { font-size: 10px; color: var(--text-muted); white-space: nowrap; }
.chart-val   { font-size: 10px; font-weight: 700; color: var(--text-muted); }

/* ── Top-patients mini list ─────────────────────────────────────── */
.top-patient-row { display: flex; align-items: center; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--border-color); }
.top-patient-row:last-child { border-bottom: none; }

/* ── Print ──────────────────────────────────────────────────────── */
@media print {
    .sidebar,.navbar,.page-header-actions,.mobile-toggle,.sidebar-overlay,.stat-trend,
    .no-print { display: none !important; }
    body,.main-wrapper,.content-area,.app-container { padding:0!important;margin:0!important;background:#fff!important; }
    .stats-grid { display:grid!important;grid-template-columns:repeat(4,1fr)!important;gap:16px!important;margin-bottom:24px!important; }
    .stat-card,.card { border:1px solid #e5e7eb!important;box-shadow:none!important;break-inside:avoid!important; }
    * { -webkit-print-color-adjust:exact!important;print-color-adjust:exact!important; }
}
</style>
@endpush

@section('content')

{{-- ── Page header ───────────────────────────────────────────────── --}}
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('clinic.reports.title') }}</h1>
        <p class="page-subtitle">
            {{ $dateFrom->translatedFormat('d M Y') }} — {{ $dateTo->translatedFormat('d M Y') }}
        </p>
    </div>
    <div class="page-header-actions d-flex gap-8 align-items-center no-print">
        <form method="GET" action="{{ route('clinic.reports.index') }}" class="m-0">
            <select name="type" class="form-control" onchange="this.form.submit()">
                <option value="daily"   {{ $type === 'daily'   ? 'selected' : '' }}>{{ __('clinic.reports.daily') }}</option>
                <option value="monthly" {{ $type === 'monthly' ? 'selected' : '' }}>{{ __('clinic.reports.monthly') }}</option>
                <option value="yearly"  {{ $type === 'yearly'  ? 'selected' : '' }}>{{ __('clinic.reports.yearly') }}</option>
            </select>
        </form>
        <button type="button" class="btn btn-secondary d-flex align-items-center gap-4" onclick="window.print()">
            <i class="ph-bold ph-printer" style="font-size:18px;"></i>
            {{ __('admin.common.print_report') }}
        </button>
    </div>
</div>

{{-- ── Stats row ─────────────────────────────────────────────────── --}}
<div class="stats-grid mb-24">
    {{-- Appointments --}}
    <div class="stat-card primary">
        <div class="stat-top">
            <span class="stat-label">{{ __('clinic.reports.total_consultations') }}</span>
            <div class="stat-icon"><i class="ph-fill ph-calendar-check"></i></div>
        </div>
        <div class="stat-value">{{ $completedAppointments }}</div>
        <div class="stat-trend" style="opacity:.7; font-size:11px; margin-top:4px;">
            {{ $appointments->count() }} {{ app()->getLocale() === 'ar' ? 'إجمالي' : 'total' }}
        </div>
    </div>

    {{-- Patients --}}
    <div class="stat-card info" style="--info:#0ea5e9;">
        <div class="stat-top">
            <span class="stat-label">{{ app()->getLocale() === 'ar' ? 'إجمالي المرضى' : 'Total Patients' }}</span>
            <div class="stat-icon"><i class="ph-fill ph-users"></i></div>
        </div>
        <div class="stat-value">{{ number_format($totalPatients) }}</div>
    </div>

    {{-- Revenue --}}
    <div class="stat-card success">
        <div class="stat-top">
            <span class="stat-label">{{ __('clinic.reports.total_revenue') }}</span>
            <div class="stat-icon"><i class="ph-fill ph-money"></i></div>
        </div>
        <div class="stat-value" dir="ltr">{{ number_format($totalRevenue, 0) }} {{ __('clinic.reports.egp') }}</div>
    </div>

    {{-- Net profit --}}
    <div class="stat-card {{ $netProfit >= 0 ? 'accent' : 'danger' }}">
        <div class="stat-top">
            <span class="stat-label">{{ __('clinic.reports.net_profit') }}</span>
            <div class="stat-icon">
                <i class="ph-fill {{ $netProfit >= 0 ? 'ph-chart-line-up' : 'ph-chart-line-down' }}"></i>
            </div>
        </div>
        <div class="stat-value" dir="ltr">{{ number_format($netProfit, 0) }} {{ __('clinic.reports.egp') }}</div>
        @if($totalExpenses > 0)
        <div class="stat-trend {{ $netProfit >= 0 ? 'up' : 'down' }}" style="font-size:11px;margin-top:4px;">
            {{ app()->getLocale() === 'ar' ? 'مصاريف:' : 'Expenses:' }} {{ number_format($totalExpenses, 0) }}
        </div>
        @endif
    </div>
</div>

{{-- ── Monthly revenue chart ────────────────────────────────────── --}}
<div class="card mb-24 no-print">
    <div class="card-header">
        <h3 class="card-title">
            <i class="ph-bold ph-chart-bar" style="margin-inline-end:6px;"></i>
            {{ app()->getLocale() === 'ar' ? 'الإيرادات الشهرية (آخر 6 أشهر)' : 'Monthly Revenue (Last 6 Months)' }}
        </h3>
    </div>
    <div class="card-body">
        @php
            $maxRev = collect($monthlyChart)->max('revenue') ?: 1;
        @endphp

        @if($maxRev == 1 && collect($monthlyChart)->sum('count') == 0)
            {{-- empty state --}}
            <div style="padding:40px;text-align:center;color:var(--text-muted);">
                <i class="ph ph-chart-bar" style="font-size:40px;opacity:.3;display:block;margin-bottom:8px;"></i>
                <p>{{ app()->getLocale() === 'ar' ? 'لا توجد بيانات بعد' : 'No data yet' }}</p>
            </div>
        @else
        <div class="chart-bars">
            @foreach($monthlyChart as $m)
            @php $pct = $maxRev > 0 ? ($m['revenue'] / $maxRev * 100) : 0; @endphp
            <div class="chart-bar-col" title="{{ $m['label'] }}: {{ number_format($m['revenue'],0) }} EGP / {{ $m['count'] }} appts">
                <span class="chart-val">{{ $m['count'] > 0 ? number_format($m['revenue']/1000,1).'k' : '' }}</span>
                <div class="chart-bar" style="height:{{ max($pct, $m['count'] > 0 ? 5 : 0) }}%;"></div>
                <span class="chart-label">{{ $m['label'] }}</span>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

{{-- ── Two columns: appointments table + top patients ─────────────── --}}
<div style="display:grid;grid-template-columns:1fr 300px;gap:24px;margin-bottom:24px;" class="report-grid">

    {{-- Appointments table --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('clinic.reports.revenue_breakdown') }}</h3>
            <span class="badge badge-secondary">{{ $appointments->count() }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ __('clinic.patients.name') }}</th>
                            <th>{{ __('clinic.reports.from') }}</th>
                            <th>{{ __('clinic.dashboard.time') }}</th>
                            <th>{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                            <th>{{ __('clinic.reports.paid') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($appointments as $appointment)
                        <tr>
                            <td>
                                <div class="fw-600">{{ $appointment->patient->full_name ?? '—' }}</div>
                                <div class="text-xs text-muted">
                                    {{ $appointment->type === 'followup'
                                        ? __('clinic.reports.type_followup')
                                        : __('clinic.reports.type_consultation') }}
                                    &bull; {{ $appointment->doctor->name ?? '—' }}
                                </div>
                            </td>
                            <td dir="ltr">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('Y-m-d') }}</td>
                            <td dir="ltr">
                                {{ $appointment->start_time
                                    ? \Carbon\Carbon::parse($appointment->start_time)->format('h:i A')
                                    : '—' }}
                            </td>
                            <td>
                                @php $st = $appointment->status; @endphp
                                <span class="badge badge-{{ $st === 'completed' ? 'success' : ($st === 'confirmed' ? 'primary' : 'warning') }}">
                                    {{ __('clinic.appointments.status_' . $st) }}
                                </span>
                            </td>
                            <td dir="ltr">
                                @if($appointment->is_paid)
                                    <span class="badge badge-success">
                                        {{ number_format($appointment->total_price, 0) }} {{ __('clinic.reports.egp') }}
                                    </span>
                                @else
                                    <span class="badge badge-warning">{{ __('clinic.reports.unpaid') }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5">
                                <div style="padding:40px;text-align:center;color:var(--text-muted);">
                                    <i class="ph ph-calendar-x" style="font-size:36px;opacity:.3;display:block;margin-bottom:8px;"></i>
                                    <p>{{ __('clinic.common.no_data') }}</p>
                                    <p style="font-size:12px;margin-top:4px;">
                                        {{ app()->getLocale() === 'ar'
                                            ? 'لا توجد مواعيد في هذه الفترة'
                                            : 'No appointments in this period' }}
                                    </p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Top patients sidebar --}}
    <div>
        <div class="card mb-24">
            <div class="card-header">
                <h3 class="card-title" style="font-size:13px;">
                    <i class="ph-bold ph-crown" style="margin-inline-end:4px;color:#f59e0b;"></i>
                    {{ app()->getLocale() === 'ar' ? 'أكثر المرضى زيارةً' : 'Top Patients' }}
                </h3>
            </div>
            <div class="card-body">
                @forelse($topPatients as $i => $tp)
                <div class="top-patient-row">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <span style="font-size:11px;font-weight:800;color:var(--text-muted);width:18px;">{{ $i+1 }}.</span>
                        <div>
                            <div style="font-size:13px;font-weight:600;">{{ $tp['name'] }}</div>
                            <div style="font-size:11px;color:var(--text-muted);">
                                {{ $tp['count'] }} {{ app()->getLocale() === 'ar' ? 'زيارة' : 'visits' }}
                            </div>
                        </div>
                    </div>
                    <span style="font-size:12px;font-weight:700;color:var(--success);">
                        {{ number_format($tp['paid'], 0) }}
                    </span>
                </div>
                @empty
                <p style="font-size:12px;color:var(--text-muted);text-align:center;padding:20px 0;">
                    {{ app()->getLocale() === 'ar' ? 'لا توجد بيانات' : 'No data' }}
                </p>
                @endforelse
            </div>
        </div>

        {{-- Expenses summary --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title" style="font-size:13px;">{{ __('clinic.reports.total_expenses') }}</h3>
            </div>
            <div class="card-body p-0">
                <table class="table" style="font-size:13px;">
                    <tbody>
                        @forelse($expenses as $expense)
                        <tr>
                            <td>
                                <div class="fw-600">{{ $expense->category }}</div>
                                @if($expense->description)
                                <div class="text-xs text-muted">{{ Str::limit($expense->description, 30) }}</div>
                                @endif
                            </td>
                            <td dir="ltr" class="fw-600 text-danger" style="white-space:nowrap;">
                                {{ number_format($expense->amount, 0) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" style="text-align:center;color:var(--text-muted);padding:20px;">
                                {{ __('clinic.reports.no_expenses') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ── Notes card ───────────────────────────────────────────────── --}}
<div class="card no-print">
    <div class="card-body" style="padding:16px;">
        <p class="text-muted" style="font-size:13px;">
            <i class="ph-bold ph-info" style="margin-inline-end:4px;"></i>
            {{ app()->getLocale() === 'ar' ? 'التقرير يغطي الفترة من' : 'Report covers' }}
            <strong>{{ $dateFrom->translatedFormat('d M Y') }}</strong>
            {{ app()->getLocale() === 'ar' ? 'إلى' : 'to' }}
            <strong>{{ $dateTo->translatedFormat('d M Y') }}</strong>.
            {{ app()->getLocale() === 'ar'
                ? '* الإيرادات تشمل المواعيد المدفوعة فقط.'
                : '* Revenue includes paid appointments only.' }}
        </p>
    </div>
</div>

@endsection

@push('scripts')
<style>
@media (max-width: 768px) {
    .report-grid { grid-template-columns: 1fr !important; }
}
</style>
@endpush
