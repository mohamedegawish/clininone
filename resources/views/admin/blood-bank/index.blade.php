@extends('layouts.app')

@section('title', 'إدارة بنك الدم')

@section('content')
<div class="page-header mb-4 px-2 text-center text-md-start">
    <div class="d-flex flex-column gap-2">
        <h1 class="page-title fw-900 mb-0" style="color: var(--clr-primary-800); font-family: 'Cairo', sans-serif; font-size: 1.75rem;">🩸 إدارة بنك الدم</h1>
        <p class="page-subtitle text-muted mb-2">نظام المتابعة والتنسيق المتكامل لعمليات التبرع</p>
        <div>
            <a href="{{ route('admin.blood-bank.donors') }}" class="btn btn-primary shadow-sm px-4 py-2 w-100 w-md-auto" style="border-radius: var(--r-md); background: linear-gradient(135deg, var(--clr-danger), var(--clr-danger-dk)); border: none;">
                <i class="ph-bold ph-users-three me-1"></i>
                قاعدة بيانات المتبرعين
            </a>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Stats Cards -->
    <div class="col-xl-3 col-md-6">
        <div class="card h-100 border-0 shadow-sm overflow-hidden stat-card-hover" style="border-radius: var(--r-xl); background: #fff;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="stats-icon-bg bg-danger-subtle text-danger">
                        <i class="ph-fill ph-drop"></i>
                    </div>
                    <div class="text-end">
                        <h2 class="fw-900 mb-0 stat-number">{{ $pendingRequestsCount }}</h2>
                        <span class="text-muted small fw-bold">طلبات معلقة</span>
                    </div>
                </div>
                <div class="progress" style="height: 4px; background: #fef2f2;">
                    <div class="progress-bar bg-danger" style="width: 70%"></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card h-100 border-0 shadow-sm overflow-hidden stat-card-hover" style="border-radius: var(--r-xl); background: #fff;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="stats-icon-bg bg-success-subtle text-success">
                        <i class="ph-fill ph-users-three"></i>
                    </div>
                    <div class="text-end">
                        <h2 class="fw-900 mb-0 stat-number">{{ $donorsCount }}</h2>
                        <span class="text-muted small fw-bold">متبرع متاح</span>
                    </div>
                </div>
                <div class="progress" style="height: 4px; background: #f0fdf4;">
                    <div class="progress-bar bg-success" style="width: 100%"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card h-100 border-0 shadow-sm overflow-hidden stat-card-hover" style="border-radius: var(--r-xl); background: #fff;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="stats-icon-bg bg-info-subtle text-info">
                        <i class="ph-fill ph-hand-heart"></i>
                    </div>
                    <div class="text-end">
                        <h2 class="fw-900 mb-0 stat-number">{{ \App\Models\core\BloodRequest::where('status', 'completed')->count() }}</h2>
                        <span class="text-muted small fw-bold">حالات ناجحة</span>
                    </div>
                </div>
                <div class="progress" style="height: 4px; background: #eff6ff;">
                    <div class="progress-bar bg-info" style="width: 85%"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card h-100 border-0 shadow-sm overflow-hidden stat-card-hover" style="border-radius: var(--r-xl); background: #fff;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="stats-icon-bg bg-warning-subtle text-warning">
                        <i class="ph-fill ph-clock"></i>
                    </div>
                    <div class="text-end">
                        <h2 class="fw-900 mb-0 stat-number">{{ \App\Models\core\BloodRequest::where('status', 'contacting')->count() }}</h2>
                        <span class="text-muted small fw-bold">جاري التواصل</span>
                    </div>
                </div>
                <div class="progress" style="height: 4px; background: #fffbeb;">
                    <div class="progress-bar bg-warning" style="width: 50%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm" style="border-radius: var(--r-xl); background: var(--clr-n-0);">
    <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center">
        <h5 class="fw-800 mb-0 text-dark"><i class="ph-bold ph-list-numbers me-2 text-primary"></i>طلبات الدم الأخيرة</h5>
        <div class="d-flex gap-2">
            <button class="btn btn-light btn-sm rounded-pill px-3 fw-bold border"><i class="ph-bold ph-funnel me-1"></i> تصفية</button>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3 border-0 small text-uppercase text-muted fw-800">صاحب الطلب</th>
                        <th class="py-3 border-0 small text-uppercase text-muted fw-800">الفصيلة</th>
                        <th class="py-3 border-0 small text-uppercase text-muted fw-800 d-none d-md-table-cell">الموقع</th>
                        <th class="py-3 border-0 small text-uppercase text-muted fw-800 d-none d-md-table-cell">الحالة</th>
                        <th class="px-4 py-3 border-0 text-end small text-uppercase text-muted fw-800">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $request)
                    <tr class="transition">
                        <td class="px-4 py-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-sm bg-danger-subtle text-danger rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px; font-size: 14px;">
                                    {{ mb_substr($request->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="fw-800 text-dark">{{ $request->name }}</div>
                                    <div class="text-primary fw-bold small" dir="ltr">{{ $request->phone }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3">
                            <div class="blood-badge-sm">
                                {{ $request->blood_type }}
                            </div>
                        </td>
                        <td class="py-3 d-none d-md-table-cell">
                            <div class="small fw-bold text-dark"><i class="ph ph-map-pin text-danger me-1"></i>{{ $request->governorate }}</div>
                            <div class="text-muted fs-xs">{{ $request->city ?? 'غير محدد' }}</div>
                        </td>
                        <td class="py-3 d-none d-md-table-cell">
                            @php
                                $statusMap = [
                                    'new' => ['primary', 'طلب جديد'],
                                    'contacting' => ['warning', 'جاري التنسيق'],
                                    'completed' => ['success', 'تم الإنجاز'],
                                    'cancelled' => ['secondary', 'ملغي'],
                                ];
                                $st = $statusMap[$request->status] ?? ['secondary', $request->status];
                            @endphp
                            <span class="status-badge status-{{ $st[0] }}">
                                {{ $st[1] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-end">
                            <a href="{{ route('admin.blood-bank.requests.show', $request) }}" class="btn btn-dark btn-sm rounded-pill px-4 py-2 fw-800 transition shadow-sm">
                                <i class="ph ph-magnifying-glass me-1"></i> مراجعة
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <h6 class="text-muted fw-bold">لا توجد طلبات دم حالياً</h6>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top">
            {{ $requests->links() }}
        </div>
    </div>
</div>

<style>
    .fw-800 { font-weight: 800; }
    .fw-900 { font-weight: 900; }
    .fs-xs { font-size: 11px; }
    
    .stats-icon-bg {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }
    
    .stat-number {
        font-size: 1.75rem;
        line-height: 1.2;
    }
    
    .blood-badge-sm {
        width: 38px;
        height: 38px;
        background: linear-gradient(135deg, #dc2626, #991b1b);
        color: #fff;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        font-size: 13px;
    }

    .status-badge {
        padding: 5px 14px;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 800;
    }
    .status-primary { background: #eff6ff; color: #2563eb; }
    .status-warning { background: #fffbeb; color: #d97706; }
    .status-success { background: #f0fdf4; color: #16a34a; }
    
    .stat-card-hover {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .stat-card-hover:hover {
        transform: translateY(-5px);
        box-shadow: var(--sh-md) !important;
    }

    .pagination svg {
        width: 1rem !important;
        height: 1rem !important;
    }
</style>
@endsection
