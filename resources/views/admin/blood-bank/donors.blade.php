@extends('layouts.app')

@section('title', 'قاعدة بيانات المتبرعين')

@section('content')
<div class="page-header mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('admin.blood-bank.index') }}" class="btn btn-icon btn-light rounded-circle shadow-sm hover-translate-y">
            <i class="ph-bold ph-arrow-left"></i>
        </a>
        <div>
            <h1 class="page-title fw-800 mb-1" style="color: var(--clr-primary-800); font-family: 'Cairo', sans-serif;">👥 قاعدة بيانات المتبرعين</h1>
            <p class="page-subtitle text-muted mb-0">تحكم وإدارة جميع المتبرعين المسجلين في النظام</p>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4" style="border-radius: var(--r-xl); background: var(--clr-n-0);">
    <div class="card-body p-4">
        <form action="{{ route('admin.blood-bank.donors') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-lg-3 col-md-6">
                <label class="form-label small fw-800 text-muted mb-2">فصيلة الدم</label>
                <select name="blood_type" class="form-select border-0 bg-light rounded-3 py-2 fw-bold" style="font-size: 14px;">
                    <option value="">كل الفصائل</option>
                    @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $type)
                        <option value="{{ $type }}" {{ request('blood_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-4 col-md-6">
                <label class="form-label small fw-800 text-muted mb-2">المحافظة / المدينة</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-0 rounded-start-3"><i class="ph ph-map-pin text-danger"></i></span>
                    <input type="text" name="governorate" class="form-control border-0 bg-light rounded-end-3 py-2 fw-bold" placeholder="بحث بالموقع..." value="{{ request('governorate') }}" style="font-size: 14px;">
                </div>
            </div>
            <div class="col-lg-2 col-md-12">
                <button type="submit" class="btn btn-primary w-100 py-2 shadow-sm fw-800 rounded-3">
                    <i class="ph-bold ph-funnel me-2"></i> تصفية
                </button>
            </div>
            <div class="col-lg-3 col-md-12 text-lg-end">
                <div class="d-inline-flex flex-column text-end">
                    <span class="text-muted small fw-bold mb-1">إجمالي المتبرعين المسجلين</span>
                    <h4 class="fw-900 text-primary mb-0">{{ $donors->total() }}</h4>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm" style="border-radius: var(--r-xl); overflow: hidden;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3 border-0 small text-uppercase text-muted fw-800">المتبرع</th>
                        <th class="py-3 border-0 small text-uppercase text-muted fw-800">الفصيلة</th>
                        <th class="py-3 border-0 small text-uppercase text-muted fw-800">الموقع</th>
                        <th class="py-3 border-0 small text-uppercase text-muted fw-800 text-center">الحالة</th>
                        <th class="py-3 border-0 small text-uppercase text-muted fw-800 text-center">آخر تبرع</th>
                        <th class="px-4 py-3 border-0 text-end small text-uppercase text-muted fw-800">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($donors as $donor)
                    <tr class="transition">
                        <td class="px-4 py-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-box-green">
                                    {{ mb_substr($donor->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="fw-800 text-dark">{{ $donor->name }}</div>
                                    <div class="text-primary fw-bold small" dir="ltr">{{ $donor->phone }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4">
                            <div class="blood-badge-green">
                                {{ $donor->blood_type }}
                            </div>
                        </td>
                        <td class="py-4">
                            <div class="small fw-bold text-dark"><i class="ph ph-map-pin text-primary me-1"></i>{{ $donor->governorate }}</div>
                            <div class="text-muted fs-xs">{{ $donor->city ?? 'غير محدد' }}</div>
                        </td>
                        <td class="py-4 text-center">
                            @if($donor->status === 'active')
                                <span class="status-pill-green">نشط</span>
                            @else
                                <span class="status-pill-gray">غير نشط</span>
                            @endif
                        </td>
                        <td class="py-4 text-center">
                            <div class="small fw-bold text-muted">
                                {{ $donor->last_donation_date ?? 'لم يسبق' }}
                            </div>
                        </td>
                        <td class="px-4 py-4 text-end">
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="tel:{{ $donor->phone }}" class="btn btn-sm btn-icon-round bg-success-subtle text-success border-0" title="اتصال">
                                    <i class="ph-bold ph-phone"></i>
                                </a>
                                <form action="{{ route('admin.blood-bank.donors.toggle-status', $donor) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-icon-round {{ $donor->status === 'active' ? 'bg-danger-subtle text-danger' : 'bg-primary-subtle text-primary' }} border-0" title="{{ $donor->status === 'active' ? 'إيقاف' : 'تفعيل' }}">
                                        <i class="ph-bold {{ $donor->status === 'active' ? 'ph-prohibit' : 'ph-check-circle' }}"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="ph-bold ph-users fs-1 text-muted opacity-20 d-block mb-3"></i>
                            <h6 class="text-muted fw-bold">لا يوجد متبرعون مسجلون حالياً</h6>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-top">
            {{ $donors->links() }}
        </div>
    </div>
</div>

<style>
    .fw-900 { font-weight: 900; }
    .fw-800 { font-weight: 800; }
    .fs-xs { font-size: 11px; }

    .avatar-box-green {
        width: 40px;
        height: 40px;
        background: #f0fdf4;
        color: #16a34a;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 14px;
        border: 1px solid #dcfce7;
    }

    .blood-badge-green {
        width: 38px;
        height: 38px;
        background: linear-gradient(135deg, #16a34a, #15803d);
        color: #fff;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        font-size: 13px;
        box-shadow: 0 4px 12px rgba(22, 163, 74, 0.2);
    }

    .status-pill-green {
        background: #f0fdf4;
        color: #16a34a;
        padding: 4px 14px;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 800;
        border: 1px solid #dcfce7;
    }
    
    .status-pill-gray {
        background: #f8fafc;
        color: #64748b;
        padding: 4px 14px;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 800;
        border: 1px solid #e2e8f0;
    }

    .btn-icon-round {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }
    .btn-icon-round:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    }

    .hover-translate-y:hover { transform: translateY(-3px); }
    .table tbody tr:hover { background: #fcfdfe; }

    /* Fix Pagination Giant Icons */
    .pagination svg {
        width: 1rem !important;
        height: 1rem !important;
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 12px;
        }
        .card-body form {
            flex-direction: column;
        }
        .card-body form .col-lg-3, .card-body form .col-lg-4, .card-body form .col-lg-2 {
            width: 100%;
        }
        .avatar-box-green {
            width: 36px;
            height: 36px;
            font-size: 12px;
        }
        .blood-badge-green {
            width: 32px;
            height: 32px;
            font-size: 11px;
        }
        .status-pill-green, .status-pill-gray {
            padding: 3px 8px;
            font-size: 9px;
        }
        .btn-icon-round {
            width: 32px;
            height: 32px;
        }
    }
</style>
@endsection
