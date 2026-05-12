@extends('layouts.app')

@section('title', 'إضافة خطة اشتراك جديدة')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">إضافة خطة اشتراك</h1>
        <p class="page-subtitle">تحديد باقات الاشتراك المتاحة للعيادات في النظام</p>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">بيانات الخطة</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.plans.store') }}" method="POST">
            @csrf
            
            <div class="form-grid">
                <div class="form-group span-2">
                    <label class="form-label">اسم الخطة <span class="req">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="مثلاً: الخطة الاحترافية">
                </div>

                <div class="form-group">
                    <label class="form-label">السعر (ج.م) <span class="req">*</span></label>
                    <input type="number" name="price" class="form-control" value="{{ old('price') }}" required step="0.01">
                </div>

                <div class="form-group">
                    <label class="form-label">المدة (بالشهور) <span class="req">*</span></label>
                    <input type="number" name="duration" class="form-control" value="{{ old('duration', 1) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">أقصى عدد للمرضى <span class="req">*</span></label>
                    <input type="number" name="max_patients" class="form-control" value="{{ old('max_patients', 1) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">أقصى عدد للمواعيد شهرياً <span class="req">*</span></label>
                    <input type="number" name="max_appointments" class="form-control" value="{{ old('max_appointments', 100) }}" required>
                </div>

                <div class="form-group span-2">
                    <label class="form-label">المميزات (اختياري)</label>
                    <textarea name="features" class="form-control" rows="4" placeholder="اكتب المميزات هنا، ميزة في كل سطر..."></textarea>
                    <p class="text-xs text-muted mt-4">سيتم عرض هذه المميزات للعيادات عند اختيار الخطة.</p>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-24 gap-12">
                <a href="{{ route('admin.plans.index') }}" class="btn btn-ghost">إلغاء</a>
                <button type="submit" class="btn btn-primary btn-lg px-40">
                    <i class="ph-bold ph-check"></i>
                    <span>إنشاء الخطة</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
