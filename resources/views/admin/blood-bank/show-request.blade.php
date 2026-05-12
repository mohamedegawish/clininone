@extends('layouts.app')

@section('title', 'مراجعة وتنسيق الطلب - ' . $request->name)

@section('content')
<div class="page-header mb-4 px-2">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('admin.blood-bank.index') }}" class="btn btn-icon btn-light rounded-circle shadow-sm hover-translate-y">
            <i class="ph-bold ph-arrow-left"></i>
        </a>
        <div>
            <h1 class="page-title fw-900 mb-1" style="color: var(--clr-primary-800); font-family: 'Cairo', sans-serif;">📋 مراجعة وتنسيق الطلب</h1>
            <p class="page-subtitle text-muted mb-0">نظام المطابقة الذكية المتكامل</p>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Request Details Column -->
    <div class="col-xl-4 col-lg-5">
        <div class="card border-0 shadow-lg position-relative overflow-hidden" style="border-radius: 32px; background: #fff;">
            @if($request->type === 'urgent')
            <div class="urgency-ribbon-v2">
                <span class="pulse-white"></span> عاجل
            </div>
            @endif

            <div class="card-body p-4 pt-5">
                <div class="text-center mb-4 pt-2">
                    <div class="blood-type-display mx-auto mb-3">
                        <span class="type">{{ $request->blood_type }}</span>
                        <span class="label">فصيلة الدم</span>
                    </div>
                    <h3 class="fw-900 text-dark mb-1">{{ $request->name }}</h3>
                    <div class="badge-status-pill {{ $request->status }}">
                        {{ $request->status === 'new' ? 'طلب جديد' : ($request->status === 'contacting' ? 'جاري التنسيق' : ($request->status === 'completed' ? 'تم الإنجاز' : 'ملغي')) }}
                    </div>
                </div>

                <div class="contact-panel p-4 rounded-4 mb-4 shadow-sm">
                    <div class="small fw-bold text-muted mb-2 text-center text-uppercase ls-1">بيانات التواصل</div>
                    <div class="phone-num-large mb-3 text-center" dir="ltr">{{ $request->phone }}</div>
                    <div class="d-flex gap-2">
                        <a href="tel:{{ $request->phone }}" class="btn btn-primary flex-grow-1 rounded-pill py-2 fw-900 shadow-md">
                            <i class="ph-bold ph-phone-call me-1"></i> اتصال
                        </a>
                        <button class="btn btn-white border rounded-circle p-2" onclick="copyPhone('{{ $request->phone }}')">
                            <i class="ph-bold ph-copy"></i>
                        </button>
                    </div>
                </div>

                <div class="info-stack">
                    <div class="info-item-v2">
                        <div class="icon-circle bg-danger-subtle text-danger"><i class="ph-fill ph-map-pin"></i></div>
                        <div>
                            <div class="val">{{ $request->governorate }}</div>
                            <div class="lbl">{{ $request->city ?? 'المدينة غير محددة' }}</div>
                        </div>
                    </div>
                    <div class="info-item-v2">
                        <div class="icon-circle bg-primary-subtle text-primary"><i class="ph-fill ph-hospital"></i></div>
                        <div>
                            <div class="val">{{ $request->hospital ?? 'غير محدد' }}</div>
                            <div class="lbl">جهة الاستلام</div>
                        </div>
                    </div>
                </div>

                <!-- Smaller Status Update Form -->
                <div class="status-update-compact mt-4 p-3 rounded-4 bg-dark text-white shadow-lg">
                    <form action="{{ route('admin.blood-bank.requests.update-status', $request) }}" method="POST">
                        @csrf
                        <div class="d-flex gap-2 align-items-center">
                            <div class="flex-grow-1">
                                <select name="status" class="form-select form-select-sm border-0 bg-white fw-800 rounded-3 text-dark" style="height: 38px; font-size: 13px;">
                                    <option value="new" {{ $request->status === 'new' ? 'selected' : '' }}>🔵 طلب معلق</option>
                                    <option value="contacting" {{ $request->status === 'contacting' ? 'selected' : '' }}>🟡 جاري التنسيق</option>
                                    <option value="completed" {{ $request->status === 'completed' ? 'selected' : '' }}>🟢 تم الإتمام</option>
                                    <option value="cancelled" {{ $request->status === 'cancelled' ? 'selected' : '' }}>🔴 إلغاء</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm px-3 fw-900 rounded-3 border-0 h-100" style="height: 38px; min-width: 100px;">
                                تحديث الحالة
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Matched Donors Column -->
    <div class="col-xl-8 col-lg-7">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 32px; background: #f8fafc;">
            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <div class="sparkle-icon-bg"><i class="ph-bold ph-sparkle fs-4 text-success"></i></div>
                    <div>
                        <h5 class="fw-900 m-0 text-dark">المتبرعون المطابقون</h5>
                        <p class="text-muted extra-small mb-0">نظام المطابقة المتقدم ({{ $matchedDonors->count() }} نتائج)</p>
                    </div>
                </div>
            </div>
            <div class="card-body p-3 p-md-4">
                <div class="row g-3">
                    @forelse($matchedDonors as $donor)
                    <div class="col-md-6 col-12">
                        <div class="donor-card-premium-v4">
                            <!-- Match Tag - Moved to a cleaner position -->
                            <div class="match-tag-v4">مطابقة ذكية</div>
                            
                            <div class="p-3">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <!-- Blood Badge - Clearly positioned on the left/start -->
                                    <div class="blood-badge-fixed">
                                        {{ $donor->blood_type }}
                                    </div>
                                    
                                    <div class="flex-grow-1 min-w-0">
                                        <h6 class="fw-900 text-dark mb-0 text-truncate">{{ $donor->name }}</h6>
                                        <div class="text-primary fw-900 small" dir="ltr" style="font-size: 15px;">{{ $donor->phone }}</div>
                                        <div class="d-flex align-items-center gap-1 extra-small text-muted fw-bold">
                                            <i class="ph ph-map-pin text-danger"></i>
                                            <span class="text-truncate">{{ $donor->city ?? $donor->governorate }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center gap-2 mt-2">
                                    <a href="tel:{{ $donor->phone }}" class="btn btn-call-compact flex-grow-1">
                                        <i class="ph-bold ph-phone-call"></i> <span>اتصال</span>
                                    </a>
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $donor->phone) }}" target="_blank" class="btn btn-wa-compact">
                                        <i class="ph-bold ph-whatsapp-logo"></i>
                                    </a>
                                    <button class="btn btn-copy-compact" onclick="copyPhone('{{ $donor->phone }}')">
                                        <i class="ph-bold ph-copy"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center py-5">
                        <div class="empty-state-v3">
                            <i class="ph ph-users-four"></i>
                            <h6 class="fw-900 mt-3 text-muted">لم يتم العثور على متبرعين</h6>
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>
            <div class="card-footer bg-white border-0 p-3 rounded-bottom-5 text-center">
                <span class="extra-small fw-800 text-muted">
                    <i class="ph-bold ph-shield-check text-success me-1"></i> يتم الحفاظ بخصوصية البيانات والتواصل يتم عبر الإدارة فقط.
                </span>
            </div>
        </div>
    </div>
</div>

<style>
    .fw-900 { font-weight: 900; }
    .fw-800 { font-weight: 800; }
    .extra-small { font-size: 11px; }
    .ls-1 { letter-spacing: 0.5px; }

    /* Urgency Ribbon */
    .urgency-ribbon-v2 {
        position: absolute; top: 18px; left: -30px; background: #dc2626; color: #fff;
        padding: 5px 35px; transform: rotate(-45deg); font-weight: 900; font-size: 10px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1); z-index: 10; display: flex; align-items: center; gap: 4px;
    }
    .pulse-white { width: 6px; height: 6px; background: #fff; border-radius: 50%; animation: pulse-w 1s infinite; }
    @keyframes pulse-w { 0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.7); } 70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(255, 255, 255, 0); } 100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(255, 255, 255, 0); } }

    /* Blood Display */
    .blood-type-display {
        width: 84px; height: 84px; background: #fff; border: 3px solid #fee2e2; border-radius: 50%;
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        box-shadow: 0 10px 25px rgba(220, 38, 38, 0.05);
    }
    .blood-type-display .type { font-size: 26px; font-weight: 900; color: #dc2626; line-height: 1; }
    .blood-type-display .label { font-size: 8px; font-weight: 800; color: #991b1b; text-transform: uppercase; margin-top: 2px; }

    /* Info Stack */
    .info-stack { display: flex; flex-direction: column; gap: 10px; }
    .info-item-v2 {
        display: flex; align-items: center; gap: 12px; background: #fcfdfe; border: 1px solid #f1f5f9;
        padding: 12px; border-radius: 18px;
    }
    .info-item-v2 .icon-circle { width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
    .info-item-v2 .val { font-weight: 800; color: #1e293b; font-size: 13px; }
    .info-item-v2 .lbl { font-size: 10px; color: #64748b; font-weight: 700; }

    /* Donor Card V4 - Fixed overlap and layout */
    .donor-card-premium-v4 {
        background: #fff; border-radius: 24px; border: 1px solid #e2e8f0; position: relative;
        transition: all 0.2s ease-in-out; box-shadow: 0 4px 12px rgba(0,0,0,0.03);
    }
    .donor-card-premium-v4:hover { border-color: #3b82f6; transform: translateY(-3px); }
    
    .match-tag-v4 {
        position: absolute; top: 10px; left: 15px; background: #f0fdf4; color: #16a34a;
        font-size: 10px; font-weight: 900; padding: 2px 10px; border-radius: 50px;
        border: 1px solid #dcfce7;
    }

    .blood-badge-fixed {
        width: 48px; height: 48px; background: linear-gradient(135deg, #dc2626, #991b1b);
        color: #fff; border-radius: 14px; display: flex; align-items: center; justify-content: center;
        font-weight: 900; font-size: 15px; box-shadow: 0 4px 10px rgba(220, 38, 38, 0.2); flex-shrink: 0;
    }

    /* Compact Buttons */
    .btn-call-compact { background: #2563eb; color: #fff; border: none; border-radius: 50px; padding: 10px 15px; font-weight: 900; display: flex; align-items: center; justify-content: center; font-size: 13px; transition: 0.3s; }
    .btn-wa-compact { width: 44px; height: 44px; background: #22c55e; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; transition: 0.3s; }
    .btn-copy-compact { width: 44px; height: 44px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 18px; transition: 0.3s; }

    .badge-status-pill { padding: 4px 12px; border-radius: 50px; font-size: 10px; font-weight: 800; display: inline-block; }
    .badge-status-pill.new { background: #eff6ff; color: #2563eb; }
    .badge-status-pill.contacting { background: #fffbeb; color: #d97706; }
    .badge-status-pill.completed { background: #f0fdf4; color: #16a34a; }

    .contact-panel { background: #f0f7ff; border: 1px solid #dbeafe; }
    .phone-num-large { font-size: 22px; font-weight: 900; color: #1e40af; }

    @media (max-width: 768px) {
        .page-title { font-size: 1.4rem !important; }
        .blood-type-display { width: 70px; height: 70px; }
        .phone-num-large { font-size: 1.4rem !important; }
        .donor-card-premium-v4 { border-radius: 20px; }
        .match-tag-v4 { top: -10px; left: auto; right: 15px; } /* Adjust on mobile */
        
        /* Fix the status update compact for mobile */
        .status-update-compact form > div {
            flex-direction: column !important;
            gap: 8px !important;
        }
        .status-update-compact button {
            width: 100% !important;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function copyPhone(phone) {
        navigator.clipboard.writeText(phone);
        Swal.fire({
            icon: 'success', title: 'تم النسخ!', text: 'الرقم جاهز للصق',
            timer: 1500, showConfirmButton: false, toast: true, position: 'top-end'
        });
    }
</script>
@endsection
