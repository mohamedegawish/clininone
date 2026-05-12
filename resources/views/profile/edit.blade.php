@extends('layouts.app')

@section('title', 'الملف الشخصي')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">إعدادات الملف الشخصي</h1>
        <p class="page-subtitle">إدارة معلوماتك الشخصية وكلمة المرور</p>
    </div>
</div>

<div class="dashboard-grid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">المعلومات الشخصية</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="form-grid">
                    <div class="form-group span-2">
                        <label class="form-label">الاسم الكامل <span class="req">*</span></label>
                        <div class="input-wrap">
                            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                            <i class="ph-bold ph-user input-icon"></i>
                        </div>
                    </div>

                    <div class="form-group span-2">
                        <label class="form-label">البريد الإلكتروني <span class="req">*</span></label>
                        <div class="input-wrap">
                            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                            <i class="ph-bold ph-envelope input-icon"></i>
                        </div>
                    </div>

                    <div class="divider span-2" style="margin: 20px 0; font-size: 12px; font-weight: 700; color: var(--clr-primary-600); text-transform: uppercase; letter-spacing: 0.5px;">تغيير كلمة المرور (اختياري)</div>

                    <div class="form-group">
                        <label class="form-label">كلمة المرور الجديدة</label>
                        <div class="input-wrap">
                            <input type="password" name="password" class="form-control" placeholder="اتركها فارغة إذا لم ترد التغيير">
                            <i class="ph-bold ph-lock input-icon"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">تأكيد كلمة المرور</label>
                        <div class="input-wrap">
                            <input type="password" name="password_confirmation" class="form-control">
                            <i class="ph-bold ph-lock-key input-icon"></i>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end" style="margin-top: 30px;">
                    <button type="submit" class="btn btn-primary btn-lg" style="min-width: 160px;">
                        <i class="ph-bold ph-check"></i>
                        <span>حفظ التغييرات</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">معلومات الحساب</h3>
        </div>
        <div class="card-body">
            <div style="display: flex; flex-direction: column; gap: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px; background: var(--clr-n-50); border-radius: var(--r-md);">
                    <span class="text-muted" style="font-size: 13px;">نوع الحساب</span>
                    <span class="badge badge-primary">{{ $user->role === 'super_admin' ? 'مدير النظام' : 'إدارة العيادة' }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px; background: var(--clr-n-50); border-radius: var(--r-md);">
                    <span class="text-muted" style="font-size: 13px;">تاريخ الانضمام</span>
                    <span style="font-weight: 600; font-size: 13px; color: var(--clr-n-800);">{{ $user->created_at->format('Y/m/d') }}</span>
                </div>
                
                <div class="alert alert-info" style="margin-top: 10px;">
                    <i class="ph-bold ph-info"></i>
                    <div>هذه البيانات يتم إدارتها من قبل الإدارة المركزية لضمان أمن النظام.</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
