@extends('layouts.app')

@section('title', 'تعديل خطة اشتراك')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">تعديل الخطة: {{ $plan->name }}</h1>
        <p class="page-subtitle">تحديث مميزات وأسعار الخطة في النظام</p>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">بيانات الخطة</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.plans.update', $plan) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-grid">
                <div class="form-group span-2">
                    <label class="form-label">اسم الخطة <span class="req">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $plan->name) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">السعر (ج.م) <span class="req">*</span></label>
                    <input type="number" name="price" class="form-control" value="{{ old('price', $plan->price) }}" required step="0.01">
                </div>

                <div class="form-group">
                    <label class="form-label">المدة (بالشهور) <span class="req">*</span></label>
                    <input type="number" name="duration" class="form-control" value="{{ old('duration', $plan->duration) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">أقصى عدد للمرضى <span class="req">*</span></label>
                    <input type="number" name="max_patients" class="form-control" value="{{ old('max_patients', $plan->max_patients) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">أقصى عدد للمواعيد شهرياً <span class="req">*</span></label>
                    <input type="number" name="max_appointments" class="form-control" value="{{ old('max_appointments', $plan->max_appointments) }}" required>
                </div>

                <div class="form-group span-2">
                    <label class="form-label">المميزات (اختياري)</label>
                    <textarea name="features" class="form-control" rows="4">{{ old('features', $plan->features) }}</textarea>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-24 gap-12">
                <a href="{{ route('admin.plans.index') }}" class="btn btn-ghost">إلغاء</a>
                <button type="submit" class="btn btn-primary btn-lg px-40">
                    <i class="ph-bold ph-check"></i>
                    <span>حفظ التعديلات</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
