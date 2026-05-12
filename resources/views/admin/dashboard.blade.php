@extends('layouts.app')

@section('title', __('admin.dashboard.title'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('admin.common.welcome', ['name' => auth()->user()->name]) }}</h1>
        <p class="page-subtitle">{{ __('admin.common.summary') }}</p>
    </div>
    <div class="page-header-actions">
        <button class="btn btn-ghost">
            <i class="ph-bold ph-calendar-blank"></i>
            <span>{{ __('admin.common.today', ['date' => now()->format('d M Y')]) }}</span>
        </button>
        <button class="btn btn-primary" onclick="window.print()">
            <i class="ph-bold ph-download-simple"></i>
            <span>{{ __('admin.common.print_report') }}</span>
        </button>
    </div>
</div>

<!-- 1. إحصائيات الأرقام الضخمة (Summary Widgets) -->
<div class="stats-grid mb-24">
    <div class="stat-card primary">
        <div class="stat-top">
            <span class="stat-label">{{ __('admin.common.total_revenue') }}</span>
            <div class="stat-icon"><i class="ph-fill ph-bank"></i></div>
        </div>
        <div class="stat-value">{{ number_format($stats['total_revenue']) }} <small class="text-xs">{{ __('admin.common.currency') }}</small></div>
    </div>
    <div class="stat-card success">
        <div class="stat-top">
            <span class="stat-label">{{ __('admin.common.active_clinics') }}</span>
            <div class="stat-icon"><i class="ph-fill ph-hospital"></i></div>
        </div>
        <div class="stat-value">{{ number_format($stats['active_clinics']) }}</div>
    </div>
    <div class="stat-card accent">
        <div class="stat-top">
            <span class="stat-label">{{ __('admin.common.total_patients') }}</span>
            <div class="stat-icon"><i class="ph-fill ph-users"></i></div>
        </div>
        <div class="stat-value">{{ number_format($stats['total_patients']) }}</div>
    </div>
    <div class="stat-card warning">
        <div class="stat-top">
            <span class="stat-label">{{ __('admin.common.todays_appointments') }}</span>
            <div class="stat-icon"><i class="ph-fill ph-calendar-check"></i></div>
        </div>
        <div class="stat-value">{{ number_format($stats['todays_appointments']) }}</div>
    </div>
</div>

<!-- 2. رسوم بيانية للنمو والأداء -->
<div class="dashboard-grid mb-24">
    <div class="card">
        <div class="card-header"><h3 class="card-title">{{ __('admin.dashboard.clinics_growth') }}</h3></div>
        <div class="card-body">
            <canvas id="clinicsGrowthChart" height="250"></canvas>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3 class="card-title">{{ __('admin.dashboard.subscription_distribution') }}</h3></div>
        <div class="card-body">
            <canvas id="subDistributionChart" height="250"></canvas>
        </div>
    </div>
</div>
<div class="card mb-24">
    <div class="card-header"><h3 class="card-title">{{ __('admin.dashboard.monthly_revenue') }}</h3></div>
    <div class="card-body">
        <canvas id="monthlyRevenueChart" height="250"></canvas>
    </div>
</div>

<!-- 3. جداول التنبيهات والعمليات العاجلة -->
<div class="dashboard-grid mb-24">
    <!-- أحدث العيادات المنضمة -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">{{ __('admin.dashboard.recent_clinics') }}</h3>
            <a href="{{ route('admin.clinics.index') }}" class="btn btn-sm btn-ghost">{{ __('admin.common.view_all') }}</a>
        </div>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('admin.common.clinic') }}</th>
                        <th>{{ __('admin.common.status') }}</th>
                        <th>{{ __('admin.common.join_date') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($alerts['recent_clinics'] as $clinic)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-8">
                                <div class="user-avatar-initials sm">{{ mb_substr($clinic->name, 0, 1) }}</div>
                                <span class="fw-600">{{ $clinic->name }}</span>
                            </div>
                        </td>
                        <td><span class="badge {{ $clinic->status === 'active' ? 'badge-success' : 'badge-danger' }}">{{ $clinic->status === 'active' ? __('admin.common.active') : __('admin.common.inactive') }}</span></td>
                        <td>{{ $clinic->created_at->format('Y-m-d') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-center text-muted">{{ __('admin.common.no_data') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- التنبيهات العاجلة -->
    <div class="card">
        <div class="card-header"><h3 class="card-title">{{ __('admin.dashboard.urgent_alerts') }}</h3></div>
        <div class="card-body">
            @if($alerts['system_failures'] > 0)
            <div class="alert alert-danger mb-16 d-flex align-items-center gap-12">
                <i class="ph-bold ph-warning-circle" style="font-size: 24px;"></i>
                <div>
                    <h4 class="fw-700 m-0">{{ __('admin.dashboard.system_warning') }}</h4>
                    <span class="text-sm">{{ __('admin.dashboard.failed_jobs', ['count' => $alerts['system_failures']]) }}</span>
                </div>
            </div>
            @endif

            <h4 class="fw-700 mb-12 text-sm">{{ __('admin.dashboard.expiring_subscriptions') }}</h4>
            @if($alerts['expiring_subscriptions']->count() > 0)
                <div class="table-wrapper">
                    <table class="table table-sm">
                        <tbody>
                            @foreach($alerts['expiring_subscriptions'] as $sub)
                            <tr>
                                <td>{{ $sub->clinic->name ?? 'N/A' }}</td>
                                <td><span class="badge badge-warning">{{ __('admin.dashboard.days_left', ['count' => $sub->end_at->diffInDays(now())]) }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-success"><i class="ph-bold ph-check-circle"></i> {{ __('admin.dashboard.no_expiring_subs') }}</div>
            @endif
        </div>
    </div>
</div>

<!-- 4. نشاط النظام الحالي & 5. مؤشرات كفاءة الأطباء -->
<div class="dashboard-grid mb-24">
    <!-- Real-time Activity -->
    <div class="card">
        <div class="card-header"><h3 class="card-title">{{ __('admin.dashboard.live_activity') }}</h3></div>
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-24 p-16 bg-light border-radius-md">
                <span class="fw-700">{{ __('admin.dashboard.online_users') }}</span>
                <span class="badge badge-primary" style="font-size: 16px;">
                    <i class="ph-fill ph-users"></i> {{ $activity['active_sessions'] }}
                </span>
            </div>

            <h4 class="fw-700 mb-12 text-sm">{{ __('admin.dashboard.latest_confirmed') }}</h4>
            <div class="table-wrapper">
                <table class="table table-sm">
                    <tbody>
                        @forelse($activity['latest_confirmed_appointments'] as $appt)
                        <tr>
                            <td>
                                <div class="fw-600">{{ $appt->patient->full_name ?? 'مريض' }}</div>
                                <div class="text-xs text-muted">{{ $appt->clinic->name ?? 'عيادة' }}</div>
                            </td>
                            <td class="text-end">
                                <div class="text-sm">{{ \Carbon\Carbon::parse($appt->appointment_date)->format('Y-m-d') }}</div>
                                <span class="badge badge-success-light text-xs">{{ __('admin.dashboard.confirmed') }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="text-center text-muted">{{ __('admin.common.no_data') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Doctor Insights -->
    <div class="card">
        <div class="card-header"><h3 class="card-title">{{ __('admin.dashboard.doctor_insights') }}</h3></div>
        <div class="card-body">
            <h4 class="fw-700 mb-12 text-sm">{{ __('admin.dashboard.top_rated_doctors') }}</h4>
            @forelse($doctorInsights['top_rated'] as $doc)
            <div class="d-flex justify-content-between align-items-center mb-12 pb-12 border-bottom">
                <div>
                    <div class="fw-600">{{ $doc->name }}</div>
                    <div class="text-xs text-muted">{{ $doc->specialty ?? __('admin.dashboard.general_doctor') }} ({{ $doc->reviews_count }} {{ __('admin.dashboard.reviews') }})</div>
                </div>
                <div class="text-warning fw-700">
                    {{ number_format($doc->avg_rating, 1) }} <i class="ph-fill ph-star"></i>
                </div>
            </div>
            @empty
                <div class="text-muted text-center mb-16">{{ __('admin.common.no_data') }}</div>
            @endforelse

            <h4 class="fw-700 mt-24 mb-12 text-sm">{{ __('admin.dashboard.top_specialties') }}</h4>
            <div class="d-flex flex-wrap gap-8">
                @forelse($doctorInsights['top_specialties'] as $spec)
                    <span class="badge badge-neutral" style="border: 1px solid var(--clr-n-200);">
                        {{ $spec->specialty }} ({{ $spec->total }})
                    </span>
                @empty
                    <span class="text-muted text-sm">{{ __('admin.common.no_data') }}</span>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chartsData = {!! json_encode($chartsData) !!};

    // 1. Clinics Growth (Line)
    new Chart(document.getElementById('clinicsGrowthChart'), {
        type: 'line',
        data: {
            labels: chartsData.clinicsGrowth.labels,
            datasets: [{
                label: 'عيادات جديدة',
                data: chartsData.clinicsGrowth.data,
                borderColor: '#1e50ff',
                backgroundColor: 'rgba(30, 80, 255, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    // 2. Subscription Distribution (Doughnut)
    new Chart(document.getElementById('subDistributionChart'), {
        type: 'doughnut',
        data: {
            labels: chartsData.subDistribution.labels,
            datasets: [{
                data: chartsData.subDistribution.data,
                backgroundColor: ['#10b981', '#1e50ff', '#f59e0b', '#8b5cf6', '#ef4444'],
                borderWidth: 0
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right' } } }
    });

    // 3. Monthly Revenue (Bar)
    new Chart(document.getElementById('monthlyRevenueChart'), {
        type: 'bar',
        data: {
            labels: chartsData.monthlyRevenue.labels,
            datasets: [{
                label: 'الإيرادات (ج.م)',
                data: chartsData.monthlyRevenue.data,
                backgroundColor: '#10b981',
                borderRadius: 4
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });
});
</script>
@endpush

@endsection
