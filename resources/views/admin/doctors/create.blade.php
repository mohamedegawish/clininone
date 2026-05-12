@extends('layouts.app')

@section('title', 'إضافة طبيب جديد')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">إضافة طبيب للنظام</h1>
        <p class="page-subtitle">إنشاء حساب جديد للطبيب وربطه بالعيادة</p>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">بيانات الطبيب</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.doctors.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">الاسم الكامل <span class="req">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">البريد الإلكتروني <span class="req">*</span></label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">كلمة المرور <span class="req">*</span></label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">رقم الهاتف <span class="req">*</span></label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" required>
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

                <div class="form-group">
                    <label class="form-label">التخصص <span class="req">*</span></label>
                    <select name="specialty" class="form-control" required>
                        <option value="">اختر التخصص...</option>
                        @foreach($specialties as $specialty)
                            <option value="{{ $specialty }}" {{ old('specialty') == $specialty ? 'selected' : '' }}>{{ $specialty }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">النوع <span class="req">*</span></label>
                    <select name="gender" class="form-control" required>
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>ذكر</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>أنثى</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">سعر الكشف <span class="req">*</span></label>
                    <input type="number" name="price" class="form-control" value="{{ old('price', 0) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">سنوات الخبرة</label>
                    <input type="number" name="experience_years" class="form-control" value="{{ old('experience_years', 0) }}">
                </div>

                <div class="form-group">
                    <label class="form-label">الصورة الشخصية</label>
                    <input type="file" name="photo" class="form-control" accept="image/*">
                </div>
            </div>

            <div class="d-flex justify-content-end mt-24">
                <button type="submit" class="btn btn-primary btn-lg px-40">
                    <i class="ph-bold ph-check"></i>
                    <span>إنشاء حساب الطبيب</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
