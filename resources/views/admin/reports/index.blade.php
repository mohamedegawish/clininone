@extends('layouts.app')

@section('title', 'مركز التقارير التحليلية')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">مركز التقارير والتحليلات الذكية</h1>
        <p class="page-subtitle">نظرة شاملة ومفصلة على أداء المنصة المالي والتشغيلي</p>
    </div>
    <div class="page-header-actions">
        <button class="btn btn-outline" onclick="window.print()">
            <i class="ph-bold ph-printer"></i>
            <span>طباعة</span>
        </button>
        <button class="btn btn-primary">
            <i class="ph-bold ph-download-simple"></i>
            <span>تصدير البيانات</span>
        </button>
    </div>
</div>

<!-- 1. التقارير المالية والإيرادات -->
<div class="mb-32">
    <div class="d-flex align-items-center gap-12 mb-16">
        <div style="width: 4px; height: 24px; background: var(--clr-primary-400); border-radius: 4px;"></div>
        <h2 style="font-size: 18px; font-weight: 700; color: var(--clr-n-800);">1. التقارير المالية والإيرادات</h2>
    </div>
    
    <div class="stats-grid mb-24">
        <div class="stat-card primary">
            <div class="stat-top">
                <span class="stat-label">إجمالي الإيرادات</span>
                <div class="stat-icon"><i class="ph-fill ph-bank"></i></div>
            </div>
            <div class="stat-value">{{ number_format($financialReports['total_revenue']) }} <small style="font-size: 14px;">ج.م</small></div>
            <div class="stat-trend up"><i class="ph-bold ph-trend-up"></i> إجمالي الأرباح المتراكمة</div>
        </div>
        <div class="stat-card success">
            <div class="stat-top">
                <span class="stat-label">الإيرادات الشهرية (MRR)</span>
                <div class="stat-icon"><i class="ph-fill ph-chart-line-up"></i></div>
            </div>
            <div class="stat-value">{{ number_format($financialReports['mrr']) }} <small style="font-size: 14px;">ج.م</small></div>
            <div class="stat-trend up"><i class="ph-bold ph-trend-up"></i> دخل متوقع هذا الشهر</div>
        </div>
        <div class="stat-card accent">
            <div class="stat-top">
                <span class="stat-label">الإيرادات السنوية (ARR)</span>
                <div class="stat-icon"><i class="ph-fill ph-calendar-check"></i></div>
            </div>
            <div class="stat-value">{{ number_format($financialReports['mrr'] * 12) }} <small style="font-size: 14px;">ج.م</small></div>
            <div class="stat-trend up"><i class="ph-bold ph-trend-up"></i> توقعات بناءً على MRR</div>
        </div>
    </div>

    <div class="dashboard-grid">
        <div class="card">
            <div class="card-header"><h3 class="card-title">توزيع الإيرادات حسب الباقة</h3></div>
            <div class="card-body">
                <canvas id="revenueByPlanChart" height="250"></canvas>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h3 class="card-title">مقارنة أداء الباقات</h3></div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>الباقة</th>
                            <th class="text-end">الإيرادات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($financialReports['revenue_by_plan'] as $plan)
                        <tr>
                            <td>{{ $plan->name }}</td>
                            <td class="text-end fw-700">{{ number_format($plan->total) }} ج.م</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- 2. تقارير الاشتراكات -->
<div class="mb-32">
    <div class="d-flex align-items-center gap-12 mb-16">
        <div style="width: 4px; height: 24px; background: var(--clr-accent-400); border-radius: 4px;"></div>
        <h2 style="font-size: 18px; font-weight: 700; color: var(--clr-n-800);">2. تقارير الاشتراكات والباقات</h2>
    </div>

    <div class="dashboard-grid">
        <div class="card">
            <div class="card-header"><h3 class="card-title">حالة الاشتراكات الحالية</h3></div>
            <div class="card-body">
                <canvas id="subStatusChart" height="250"></canvas>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h3 class="card-title">أكثر الباقات شعبية</h3></div>
            <div class="card-body">
                <canvas id="planPopularityChart" height="250"></canvas>
            </div>
        </div>
    </div>

    <div class="card mt-24">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">اشتراكات تقترب من الانتهاء (خلال 30 يوم)</h3>
            <span class="badge badge-warning">{{ $subscriptionReports['expiring_soon']->count() }} عيادة</span>
        </div>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>العيادة</th>
                        <th>الباقة</th>
                        <th>تاريخ الانتهاء</th>
                        <th>الأيام المتبقية</th>
                        <th class="text-end">الإجراء</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($subscriptionReports['expiring_soon'] as $sub)
                    <tr>
                        <td><strong>{{ $sub->clinic->name ?? 'N/A' }}</strong></td>
                        <td><span class="badge badge-neutral">{{ $sub->plan->name ?? 'N/A' }}</span></td>
                        <td>{{ $sub->end_at->format('Y-m-d') }}</td>
                        <td>
                            @php $days = now()->diffInDays($sub->end_at); @endphp
                            <span class="text-danger fw-700">{{ $days }} يوم</span>
                        </td>
                        <td class="text-end">
                            <button class="btn btn-primary btn-sm">تذكير بالتجديد</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- 3. تقارير العيادات ومعدل النمو -->
<div class="mb-32">
    <div class="d-flex align-items-center gap-12 mb-16">
        <div style="width: 4px; height: 24px; background: var(--clr-success); border-radius: 4px;"></div>
        <h2 style="font-size: 18px; font-weight: 700; color: var(--clr-n-800);">3. تقارير العيادات والنمو</h2>
    </div>

    <div class="dashboard-grid" style="grid-template-columns: 1fr 2fr;">
        <div class="card">
            <div class="card-header"><h3 class="card-title">حالة العيادات</h3></div>
            <div class="card-body">
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    @foreach($clinicReports['status_stats'] as $stat)
                    <div class="d-flex justify-content-between align-items-center p-12 bg-light border-radius-md">
                        <span class="fw-600">{{ $stat->status == 'active' ? 'نشطة' : 'غير نشطة' }}</span>
                        <span class="badge {{ $stat->status == 'active' ? 'badge-success' : 'badge-danger' }}">{{ $stat->total }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h3 class="card-title">معدل نمو المنصة (شهرياً)</h3></div>
            <div class="card-body">
                <canvas id="growthRateChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- 4. تقارير الاستهلاك والحدود -->
<div class="mb-32">
    <div class="d-flex align-items-center gap-12 mb-16">
        <div style="width: 4px; height: 24px; background: var(--clr-warning); border-radius: 4px;"></div>
        <h2 style="font-size: 18px; font-weight: 700; color: var(--clr-n-800);">4. تقارير الاستهلاك والحدود</h2>
    </div>

    <div class="stats-grid mb-24">
        <div class="stat-card">
            <div class="stat-label">إجمالي الأطباء</div>
            <div class="stat-value">{{ $usageReports['system_totals']['doctors'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">إجمالي المرضى</div>
            <div class="stat-value">{{ $usageReports['system_totals']['patients'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">إجمالي الحجوزات</div>
            <div class="stat-value">{{ $usageReports['system_totals']['appointments'] }}</div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3 class="card-title">عيادات تقترب من استهلاك الحد الأقصى (Upsell Opportunity)</h3></div>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>العيادة</th>
                        <th>الباقة</th>
                        <th>استهلاك المرضى</th>
                        <th>استهلاك المواعيد</th>
                        <th class="text-end">الإجراء</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usageReports['approaching_limits'] as $clinic)
                    <tr>
                        <td>{{ $clinic->name }}</td>
                        <td>{{ $clinic->activeSubscription->plan->name }}</td>
                        <td>
                            @php 
                                $maxP = $clinic->activeSubscription->plan->max_patients;
                                $percP = $maxP > 0 ? ($clinic->patients_count / $maxP) * 100 : 0;
                            @endphp
                            <div class="d-flex align-items-center gap-8">
                                <div class="progress sm flex-grow-1"><div class="progress-bar {{ $percP > 90 ? 'bg-danger' : 'bg-warning' }}" style="width: {{ $percP }}%"></div></div>
                                <span class="text-xs fw-700">{{ round($percP) }}%</span>
                            </div>
                        </td>
                        <td>
                            @php 
                                $maxA = $clinic->activeSubscription->plan->max_appointments;
                                $percA = $maxA > 0 ? ($clinic->appointments_count / $maxA) * 100 : 0;
                            @endphp
                            <div class="d-flex align-items-center gap-8">
                                <div class="progress sm flex-grow-1"><div class="progress-bar {{ $percA > 90 ? 'bg-danger' : 'bg-warning' }}" style="width: {{ $percA }}%"></div></div>
                                <span class="text-xs fw-700">{{ round($percA) }}%</span>
                            </div>
                        </td>
                        <td class="text-end">
                            <button class="btn btn-accent btn-sm">عرض ترقية الباقة</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- 5. تقارير صحة النظام -->
<div class="mb-32">
    <div class="d-flex align-items-center gap-12 mb-16">
        <div style="width: 4px; height: 24px; background: var(--clr-danger); border-radius: 4px;"></div>
        <h2 style="font-size: 18px; font-weight: 700; color: var(--clr-n-800);">5. تقارير صحة النظام</h2>
    </div>

    <div class="dashboard-grid">
        <div class="card">
            <div class="card-header"><h3 class="card-title">مراقبة العمليات والوظائف</h3></div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-16">
                    <span>الوظائف الفاشلة (Failed Jobs)</span>
                    <span class="badge {{ $healthReports['failed_jobs'] > 0 ? 'badge-danger' : 'badge-success' }}">{{ $healthReports['failed_jobs'] }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span>الجلسات النشطة حالياً</span>
                    <span class="badge badge-primary">{{ $healthReports['active_sessions'] }} مستخدم</span>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h3 class="card-title">ذروة استخدام المنصة (ساعات الذروة)</h3></div>
            <div class="card-body">
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    @foreach($usageReports['peak_usage'] as $peak)
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">الساعة {{ $peak->hour }}:00</span>
                        <span class="fw-700">{{ $peak->total }} عملية</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Revenue by Plan
    new Chart(document.getElementById('revenueByPlanChart'), {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($financialReports['revenue_by_plan']->pluck('name')) !!},
            datasets: [{
                data: {!! json_encode($financialReports['revenue_by_plan']->pluck('total')) !!},
                backgroundColor: ['#1e50ff', '#1ebfab', '#f59e0b', '#dc2626']
            }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });

    // 2. Subscription Status
    new Chart(document.getElementById('subStatusChart'), {
        type: 'pie',
        data: {
            labels: {!! json_encode($subscriptionReports['status_breakdown']->pluck('status')) !!},
            datasets: [{
                data: {!! json_encode($subscriptionReports['status_breakdown']->pluck('total')) !!},
                backgroundColor: ['#10b981', '#f59e0b', '#dc2626', '#64748b']
            }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });

    // 3. Plan Popularity
    new Chart(document.getElementById('planPopularityChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($subscriptionReports['plan_popularity']->pluck('name')) !!},
            datasets: [{
                label: 'عدد العيادات',
                data: {!! json_encode($subscriptionReports['plan_popularity']->pluck('subscriptions_count')) !!},
                backgroundColor: '#1ebfab'
            }]
        },
        options: { responsive: true, scales: { y: { beginAtZero: true } } }
    });

    // 4. Growth Rate
    new Chart(document.getElementById('growthRateChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($clinicReports['growth_rate']->pluck('month')->reverse()->values()) !!},
            datasets: [{
                label: 'العيادات الجديدة',
                data: {!! json_encode($clinicReports['growth_rate']->pluck('total')->reverse()->values()) !!},
                borderColor: '#1e50ff',
                tension: 0.4,
                fill: true,
                backgroundColor: 'rgba(30, 80, 255, 0.1)'
            }]
        },
        options: { responsive: true, scales: { y: { beginAtZero: true } } }
    });
});
</script>
@endpush

<style>
    .mb-32 { margin-bottom: 32px; }
    .mb-16 { margin-bottom: 16px; }
    .bg-light { background: var(--clr-n-50); }
    .border-radius-md { border-radius: var(--r-md); }
    .p-12 { padding: 12px; }
    .text-xs { font-size: 11px; }
</style>
@endsection
