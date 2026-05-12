@extends('layouts.app')

@section('title', 'إضافة مريض جديد')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">إضافة مريض للنظام</h1>
        <p class="page-subtitle">تسجيل بيانات مريض جديد وربطه بالعيادة</p>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">بيانات المريض</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.patients.store') }}" method="POST">
            @csrf
            
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">الاسم الكامل <span class="req">*</span></label>
                    <input type="text" name="full_name" class="form-control" value="{{ old('full_name') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">رقم الهاتف <span class="req">*</span></label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">البريد الإلكتروني</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">تاريخ الميلاد</label>
                    <input type="date" name="birth_date" class="form-control" value="{{ old('birth_date') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">النوع <span class="req">*</span></label>
                    <select name="gender" class="form-control" required>
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>ذكر</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>أنثى</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">العيادة <span class="req">*</span></label>
                    <select name="clinic_id" class="form-control" required>
                        <option value="">اختر العيادة...</option>
                        @foreach($clinics as $clinic)
                            <option value="{{ $clinic->id }}" {{ old('clinic_id') == $clinic->id ? 'selected' : '' }}>{{ $clinic->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group span-2">
                    <label class="form-label">العنوان</label>
                    <input type="text" name="address" class="form-control" value="{{ old('address') }}">
                </div>
            </div>

            <div class="d-flex justify-content-end mt-24 gap-12">
                <a href="{{ route('admin.patients.index') }}" class="btn btn-ghost">إلغاء</a>
                <button type="submit" class="btn btn-primary btn-lg px-40">
                    <i class="ph-bold ph-check"></i>
                    <span>إنشاء حساب المريض</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
