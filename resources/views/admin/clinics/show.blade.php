@extends('layouts.app')

@section('title', 'تفاصيل العيادة | ' . $clinic->name)

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ $clinic->name }}</h1>
        <p class="page-subtitle">نظرة شاملة على نشاط العيادة وتفاصيل الاشتراك</p>
    </div>
    <div class="page-header-actions">
        <!-- 6. الإجراءات الإدارية -->
        <a href="#" class="btn btn-warning" onclick="alert('سيتم تفعيل الدخول كأدمن قريباً')">
            <i class="ph-bold ph-user-switch"></i>
            <span>دخول كـ Admin</span>
        </a>
        <form action="{{ route('admin.clinics.toggle-status', $clinic) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn {{ $clinic->status == 'active' ? 'btn-danger' : 'btn-success' }}">
                <i class="ph-bold ph-power"></i>
                <span>{{ $clinic->status == 'active' ? 'إيقاف العيادة' : 'تفعيل العيادة' }}</span>
            </button>
        </form>
        <form action="{{ route('admin.clinics.destroy', $clinic) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف العيادة؟');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline" style="color: var(--clr-danger); border-color: var(--clr-danger);">
                <i class="ph-bold ph-trash"></i>
            </button>
        </form>
    </div>
</div>

<div class="dashboard-grid">
    <!-- 1. ملخص الهوية والنشاط -->
    <div class="card">
        <div class="card-header"><h3 class="card-title">بيانات العيادة الأساسية</h3></div>
        <div class="card-body">
            <div class="text-center mb-24">
                <div class="user-avatar-initials mx-auto mb-16" style="width: 72px; height: 72px; font-size: 28px;">
                    {{ mb_substr($clinic->name, 0, 1) }}
                </div>
                <h3 class="fw-700 mb-4">{{ $clinic->name }}</h3>
                <span class="badge {{ $clinic->status === 'active' ? 'badge-success' : 'badge-danger' }} px-16 py-8">
                    {{ $clinic->status === 'active' ? 'نشطة' : 'متوقفة' }}
                </span>
            </div>
            <div class="divider"></div>
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted text-sm">العنوان:</span>
                    <span class="fw-600 text-sm">{{ $clinic->address ?: 'غير مسجل' }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted text-sm">تاريخ الانضمام:</span>
                    <span class="fw-600 text-sm">{{ $clinic->created_at->format('Y-m-d') }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted text-sm">مدير العيادة (Admin):</span>
                    @if($adminUser)
                        <span class="fw-600 text-sm text-primary">{{ $adminUser->name }}</span>
                    @else
                        <span class="badge badge-danger text-xs">غير محدد</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- 2. تفاصيل الاشتراك المالي -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">تفاصيل الاشتراك المالي</h3>
            <a href="{{ route('admin.clinics.assign-plan', $clinic) }}" class="btn btn-primary btn-sm">ترقية/تجديد</a>
        </div>
        <div class="card-body">
            @if($clinic->activeSubscription)
                @php $plan = $clinic->activeSubscription->plan; @endphp
                <div class="d-flex justify-content-between align-items-center p-12 bg-light border-radius-md mb-16">
                    <div>
                        <h4 class="fw-700 mb-4">{{ $plan->name }}</h4>
                        <span class="text-xs text-muted">{{ number_format($plan->price) }} ج.م / شهر</span>
                    </div>
                    <span class="badge badge-success">نشط</span>
                </div>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted text-sm">تاريخ الانتهاء:</span>
                        <span class="fw-600 text-sm text-danger">{{ $clinic->activeSubscription->end_at->format('Y-m-d') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted text-sm">التجديد التلقائي:</span>
                        <span class="badge {{ $clinic->activeSubscription->auto_renew ? 'badge-primary' : 'badge-neutral' }}">
                            {{ $clinic->activeSubscription->auto_renew ? 'مفعل' : 'معطل' }}
                        </span>
                    </div>
                </div>
            @else
                <div class="alert alert-warning mb-0">لا يوجد اشتراك نشط لهذه العيادة حالياً.</div>
            @endif

            <h4 class="mt-24 mb-16" style="font-size: 14px; font-weight: 700;">سجل الفواتير والاشتراكات</h4>
            <div class="table-wrapper">
                <table class="table" style="font-size: 12px;">
                    <thead>
                        <tr>
                            <th>الباقة</th>
                            <th>المبلغ</th>
                            <th>تاريخ التفعيل</th>
                            <th>الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($billingHistory as $history)
                        <tr>
                            <td>{{ $history->plan->name ?? '-' }}</td>
                            <td>{{ isset($history->plan) ? number_format($history->plan->price) : 0 }} ج.م</td>
                            <td>{{ $history->start_at->format('Y-m-d') }}</td>
                            <td>
                                <span class="badge {{ $history->status == 'active' ? 'badge-success' : ($history->status == 'expired' ? 'badge-danger' : 'badge-neutral') }}">
                                    {{ $history->status }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted">لا توجد سجلات مالية</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="dashboard-grid mt-24">
    <!-- 3. مراقبة حدود الاستهلاك -->
    <div class="card">
        <div class="card-header"><h3 class="card-title">مراقبة حدود الاستهلاك</h3></div>
        <div class="card-body">
            @if($clinic->activeSubscription && $clinic->activeSubscription->plan)
                @php 
                    $plan = $clinic->activeSubscription->plan; 
                    $maxP = $plan->max_patients;
                    $maxA = $plan->max_appointments;
                    
                    $percP = $maxP > 0 ? ($stats['patients_count'] / $maxP) * 100 : 0;
                    $percA = $maxA > 0 ? ($stats['appointments_count'] / $maxA) * 100 : 0;
                @endphp
                
                <div class="mb-24">
                    <div class="d-flex justify-content-between mb-8">
                        <span class="text-sm fw-600">استهلاك المرضى</span>
                        <span class="text-sm fw-700 {{ $percP >= 90 ? 'text-danger' : 'text-primary' }}">{{ $stats['patients_count'] }} / {{ $maxP == 0 ? 'غير محدود' : $maxP }}</span>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar {{ $percP >= 90 ? 'bg-danger' : 'bg-primary' }}" style="width: {{ $percP > 100 ? 100 : $percP }}%"></div>
                    </div>
                    @if($percP >= 90)
                        <p class="text-xs text-danger mt-8"><i class="ph-bold ph-warning"></i> تنبيه: تم استهلاك معظم الحد المسموح.</p>
                    @endif
                </div>

                <div>
                    <div class="d-flex justify-content-between mb-8">
                        <span class="text-sm fw-600">استهلاك الحجوزات</span>
                        <span class="text-sm fw-700 {{ $percA >= 90 ? 'text-danger' : 'text-primary' }}">{{ $stats['appointments_count'] }} / {{ $maxA == 0 ? 'غير محدود' : $maxA }}</span>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar {{ $percA >= 90 ? 'bg-danger' : 'bg-primary' }}" style="width: {{ $percA > 100 ? 100 : $percA }}%"></div>
                    </div>
                    @if($percA >= 90)
                        <p class="text-xs text-danger mt-8"><i class="ph-bold ph-warning"></i> تنبيه: تم استهلاك معظم الحد المسموح.</p>
                    @endif
                </div>
            @else
                <div class="alert alert-info">يرجى تفعيل اشتراك أولاً لعرض حدود الاستهلاك.</div>
            @endif
        </div>
    </div>

    <!-- 5. مؤشرات الأداء والعمليات -->
    <div class="card">
        <div class="card-header"><h3 class="card-title">مؤشرات الأداء (التقييمات والحجوزات)</h3></div>
        <div class="card-body">
            <div class="d-flex align-items-center gap-16 mb-24">
                <div style="font-size: 36px; font-weight: 700; color: var(--clr-warning);">
                    {{ number_format($averageRating, 1) }} 
                </div>
                <div class="d-flex flex-column">
                    <div class="d-flex gap-4 mb-4 text-warning">
                        @for($i=1; $i<=5; $i++)
                            <i class="{{ $i <= round($averageRating) ? 'ph-fill' : 'ph-regular' }} ph-star" style="font-size: 18px;"></i>
                        @endfor
                    </div>
                    <span class="text-sm text-muted">متوسط تقييم المرضى للعيادة بناءً على الحجوزات السابقة.</span>
                </div>
            </div>
            
            <div class="divider"></div>
            
            <h4 style="font-size: 13px; font-weight: 700; margin-bottom: 12px;">حالة الحجوزات</h4>
            <div style="height: 200px;">
                @if(count($appointmentStatus) > 0)
                    <canvas id="appointmentsStatusChart"></canvas>
                @else
                    <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                        لا توجد حجوزات لعرض البيانات
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- 4. إحصائيات الفريق الطبي -->
<div class="card mt-24">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">الفريق الطبي والمواعيد</h3>
        <span class="badge badge-primary-light">{{ count($doctors) }} طبيب مسجل</span>
    </div>
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>اسم الطبيب</th>
                    <th>التخصص</th>
                    <th>الحالة</th>
                    <th>أيام العمل (المواعيد)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($doctors as $doctor)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-12">
                            <div class="user-avatar-initials sm">{{ mb_substr($doctor->name, 0, 1) }}</div>
                            <span class="fw-600">{{ $doctor->name }}</span>
                        </div>
                    </td>
                    <td>{{ $doctor->specialty ?? 'غير محدد' }}</td>
                    <td><span class="badge {{ $doctor->status == 'active' ? 'badge-success' : 'badge-danger' }}">{{ $doctor->status == 'active' ? 'نشط' : 'متوقف' }}</span></td>
                    <td>
                        <div class="d-flex gap-4 flex-wrap">
                            @foreach($doctor->schedules as $schedule)
                                <span class="badge badge-neutral text-xs" style="border: 1px solid var(--clr-n-200);">
                                    {{ $schedule->day_of_week }} ({{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }})
                                </span>
                            @endforeach
                            @if($doctor->schedules->isEmpty())
                                <span class="text-muted text-xs">لا يوجد جدول مسجل</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted py-24">لا يوجد أطباء مسجلين في هذه العيادة حالياً.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusData = {!! json_encode($appointmentStatus) !!};
    
    if(statusData.length > 0 && document.getElementById('appointmentsStatusChart')) {
        const labels = statusData.map(item => {
            const translation = {
                'completed': 'مكتمل',
                'pending': 'معلق',
                'cancelled': 'ملغي',
                'confirmed': 'مؤكد'
            };
            return translation[item.status] || item.status;
        });
        
        const data = statusData.map(item => item.count);
        
        const colorMap = {
            'completed': '#10b981',
            'pending': '#f59e0b',
            'cancelled': '#dc2626',
            'confirmed': '#1e50ff'
        };
        const bgColors = statusData.map(item => colorMap[item.status] || '#64748b');

        new Chart(document.getElementById('appointmentsStatusChart'), {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: bgColors,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: { position: 'right', labels: { font: { family: 'Ubuntu Arabic' } } }
                }
            }
        });
    }
});
</script>
@endpush

<style>
    .mt-24 { margin-top: 24px; }
    .mt-8 { margin-top: 8px; }
    .mb-24 { margin-bottom: 24px; }
    .mb-16 { margin-bottom: 16px; }
    .mb-8 { margin-bottom: 8px; }
    .mb-4 { margin-bottom: 4px; }
    .mb-0 { margin-bottom: 0; }
    .py-24 { padding-top: 24px; padding-bottom: 24px; }
    .bg-light { background: var(--clr-n-50); }
    .border-radius-md { border-radius: var(--r-md); }
    .p-12 { padding: 12px; }
    .text-xs { font-size: 11px; }
    .text-sm { font-size: 13px; }
    .progress { background: var(--clr-n-100); border-radius: var(--r-pill); overflow: hidden; }
    .progress-bar { height: 100%; border-radius: var(--r-pill); transition: width 0.6s ease; }
    .bg-danger { background-color: var(--clr-danger); }
    .bg-warning { background-color: var(--clr-warning); }
    .bg-primary { background-color: var(--clr-primary-400); }
    .text-danger { color: var(--clr-danger); }
    .text-primary { color: var(--clr-primary-600); }
    .text-warning { color: var(--clr-warning); }
    .d-inline { display: inline-block; }
    .badge-primary-light { background: rgba(30, 80, 255, 0.1); color: var(--clr-primary-600); }
</style>
@endsection
