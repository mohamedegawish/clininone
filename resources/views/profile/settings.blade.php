@extends('layouts.app')

@section('title', 'الإعدادات')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">إعدادات النظام</h1>
        <p class="page-subtitle">تخصيص هوية النظام والخيارات العامة</p>
    </div>
</div>

<div class="dashboard-grid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">هوية النظام</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div style="display: flex; flex-direction: column; gap: 24px;">
                    <!-- اسم النظام -->
                    <div class="form-group">
                        <label class="form-label">اسم النظام <span class="req">*</span></label>
                        <div class="input-wrap">
                            <input type="text" name="system_name" class="form-control" value="{{ old('system_name', $system_name) }}" required>
                            <i class="ph-bold ph-desktop input-icon"></i>
                        </div>
                        <p class="form-hint">هذا الاسم سيظهر في الشريط الجانبي وعنوان المتصفح.</p>
                    </div>

                    <!-- اللوجو -->
                    <div class="form-group">
                        <label class="form-label">شعار النظام (Dashboard Logo)</label>
                        
                        <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 12px;">
                            <div style="width: 80px; height: 80px; background: var(--clr-n-50); border: 1px dashed var(--clr-n-200); border-radius: var(--r-md); display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                @if($system_logo)
                                    <img src="{{ asset('uploads/settings/' . $system_logo) }}" alt="Logo" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                @else
                                    <i class="ph-bold ph-image" style="font-size: 24px; color: var(--clr-n-300);"></i>
                                @endif
                            </div>
                            <div style="flex: 1;">
                                <input type="file" name="system_logo" class="form-control" accept="image/*">
                                <p class="form-hint" style="margin-top: 6px;">شعار لوحة التحكم والسايدبار. يفضل خلفية شفافة.</p>
                            </div>
                        </div>
                    </div>

                    <!-- شعار الصفحة العامة -->
                    <div class="form-group">
                        <label class="form-label">شعار الصفحة العامة (Public Page Logo)</label>
                        
                        <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 12px;">
                            <div style="width: 80px; height: 80px; background: var(--clr-n-50); border: 1px dashed var(--clr-n-200); border-radius: var(--r-md); display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                @if($public_logo)
                                    <img src="{{ asset('uploads/settings/' . $public_logo) }}" alt="Public Logo" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                @else
                                    <i class="ph-bold ph-globe" style="font-size: 24px; color: var(--clr-n-300);"></i>
                                @endif
                            </div>
                            <div style="flex: 1;">
                                <input type="file" name="public_logo" class="form-control" accept="image/*">
                                <p class="form-hint" style="margin-top: 6px;">الشعار الذي يظهر في مقدمة الموقع للجمهور.</p>
                            </div>
                        </div>
                    </div>

                    <!-- خلفية الصفحة الرئيسية -->
                    <div class="form-group">
                        <label class="form-label">خلفية الشاشة الرئيسية (Landing Background)</label>
                        
                        <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 12px;">
                            @if($landing_bg)
                                <div style="width: 100%; height: 120px; border-radius: var(--r-md); overflow: hidden; border: 1px solid var(--clr-n-200);">
                                    <img src="{{ asset('uploads/settings/' . $landing_bg) }}" alt="Background" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                            @endif
                            <input type="file" name="landing_bg" class="form-control" accept="image/*">
                            <p class="form-hint">الصورة الكبيرة في خلفية الصفحة الرئيسية. يفضل دقة عالية (HD).</p>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary btn-lg" style="min-width: 160px;">
                            <i class="ph-bold ph-check"></i>
                            <span>حفظ الإعدادات</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">إعدادات العرض</h3>
        </div>
        <div class="card-body">
            <div style="padding: 20px 0; text-align: center;">
                <div style="width: 60px; height: 60px; background: var(--clr-primary-50); color: var(--clr-primary-400); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 24px;">
                    <i class="ph-bold ph-paint-brush"></i>
                </div>
                <h4 style="font-size: 15px; font-weight: 700; color: var(--clr-n-800); margin-bottom: 8px;">الوضع الليلي والألوان</h4>
                <p style="color: var(--clr-n-400); font-size: 13px; margin-bottom: 20px;">تخصيص ألوان الواجهة والتبديل بين الوضع الفاتح والمظلم.</p>
                <button class="btn btn-ghost btn-sm" disabled style="width: 100%;">
                    <span>قيد التطوير</span>
                </button>
            </div>
            
            <div class="divider"></div>

            <div style="padding: 10px 0; text-align: center;">
                <div style="width: 60px; height: 60px; background: var(--clr-accent-50); color: var(--clr-accent-600); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 24px;">
                    <i class="ph-bold ph-bell"></i>
                </div>
                <h4 style="font-size: 15px; font-weight: 700; color: var(--clr-n-800); margin-bottom: 8px;">التنبيهات</h4>
                <p style="color: var(--clr-n-400); font-size: 13px; margin-bottom: 20px;">إعدادات تنبيهات النظام والبريد الإلكتروني.</p>
                <button class="btn btn-ghost btn-sm" disabled style="width: 100%;">
                    <span>قيد التطوير</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
