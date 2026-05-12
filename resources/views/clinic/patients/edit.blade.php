@extends('layouts.app')

@section('title', 'تعديل بيانات المريض')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">تعديل بيانات المريض</h1>
        <p class="page-subtitle">تحديث الملف الطبي والتأميني للمريض: {{ $patient->full_name }}</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('clinic.patients.index') }}" class="btn btn-secondary">
            <i class="ph-bold ph-arrow-right"></i>
            <span>العودة للقائمة</span>
        </a>
    </div>
</div>

<div class="card" style="max-width: 900px; margin: 0 auto;">
    <div class="card-body">
        <form action="{{ route('clinic.patients.update', $patient->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <h3 class="fw-700 mb-16 text-primary border-bottom pb-8">البيانات الأساسية</h3>
            <div class="dashboard-grid" style="grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label class="form-label" for="full_name">الاسم بالكامل (Full Name) <span class="text-danger">*</span></label>
                    <input type="text" name="full_name" id="full_name" class="form-control" value="{{ old('full_name', $patient->full_name) }}" required>
                    @error('full_name') <span class="text-danger text-sm mt-4">{{ $message }}</span> @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="english_name">الاسم بالإنجليزية (English Name) <span class="text-danger">*</span></label>
                    <input type="text" name="english_name" id="english_name" class="form-control" value="{{ old('english_name', $patient->english_name) }}" dir="ltr" required>
                    @error('english_name') <span class="text-danger text-sm mt-4">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="phone">الهاتف (Phone) <span class="text-danger">*</span></label>
                    <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $patient->phone) }}" dir="ltr" required>
                    @error('phone') <span class="text-danger text-sm mt-4">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="ssn">رقم الهوية (SSN)</label>
                    <input type="text" name="ssn" id="ssn" class="form-control" value="{{ old('ssn', $patient->ssn) }}" dir="ltr">
                    @error('ssn') <span class="text-danger text-sm mt-4">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="birth_date">تاريخ الميلاد (Birth Date)</label>
                    <input type="date" name="birth_date" id="birth_date" class="form-control" value="{{ old('birth_date', $patient->birth_date ? \Carbon\Carbon::parse($patient->birth_date)->format('Y-m-d') : '') }}">
                    <span class="text-muted text-sm mt-4 d-block">يوم (D) | شهر (M) | سنة (Y)</span>
                    @error('birth_date') <span class="text-danger text-sm mt-4">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="age">أو العمر (Age)</label>
                    <input type="number" name="age" id="age" class="form-control" value="{{ old('age', $patient->age) }}" min="0">
                    <span class="text-muted text-sm mt-4 d-block">بالسنوات (Y)</span>
                    @error('age') <span class="text-danger text-sm mt-4">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="gender">النوع (Gender) <span class="text-danger">*</span></label>
                    <div class="d-flex gap-16 mt-8">
                        <label class="d-flex align-center gap-8 cursor-pointer">
                            <input type="radio" name="gender" value="male" {{ old('gender', $patient->gender) == 'male' ? 'checked' : '' }} required>
                            <span>ذكر (Male)</span>
                        </label>
                        <label class="d-flex align-center gap-8 cursor-pointer">
                            <input type="radio" name="gender" value="female" {{ old('gender', $patient->gender) == 'female' ? 'checked' : '' }} required>
                            <span>أنثى (Female)</span>
                        </label>
                    </div>
                    @error('gender') <span class="text-danger text-sm mt-4">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="nationality">الجنسية (Nationality)</label>
                    <input type="text" name="nationality" id="nationality" class="form-control" value="{{ old('nationality', $patient->nationality) }}">
                    @error('nationality') <span class="text-danger text-sm mt-4">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">البريد الإلكتروني (E-mail)</label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $patient->email) }}" dir="ltr">
                    @error('email') <span class="text-danger text-sm mt-4">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="status">حالة الملف (Status) <span class="text-danger">*</span></label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="active" {{ old('status', $patient->status) == 'active' ? 'selected' : '' }}>نشط (Active)</option>
                        <option value="inactive" {{ old('status', $patient->status) == 'inactive' ? 'selected' : '' }}>غير نشط (Inactive)</option>
                    </select>
                    @error('status') <span class="text-danger text-sm mt-4">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-group mt-16 mb-24">
                <label class="form-label" for="address">العنوان (Address)</label>
                <textarea name="address" id="address" class="form-control" rows="2">{{ old('address', $patient->address) }}</textarea>
                @error('address') <span class="text-danger text-sm mt-4">{{ $message }}</span> @enderror
            </div>

            <h3 class="fw-700 mb-16 text-primary border-bottom pb-8">البيانات التأمينية (Insurance Details)</h3>
            <div class="dashboard-grid" style="grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label class="form-label" for="company">الشركة (Company)</label>
                    <input type="text" name="company" id="company" class="form-control" value="{{ old('company', $patient->company) }}">
                    @error('company') <span class="text-danger text-sm mt-4">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="policy_name">اسم أو رقم البوليصة (Policy Name, No)</label>
                    <input type="text" name="policy_name" id="policy_name" class="form-control" value="{{ old('policy_name', $patient->policy_name) }}">
                    @error('policy_name') <span class="text-danger text-sm mt-4">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="class">الفئة (Class)</label>
                    <input type="text" name="class" id="class" class="form-control" value="{{ old('class', $patient->class) }}">
                    @error('class') <span class="text-danger text-sm mt-4">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="card_no">رقم بطاقة التأمين (Card No)</label>
                    <input type="text" name="card_no" id="card_no" class="form-control" value="{{ old('card_no', $patient->card_no) }}">
                    @error('card_no') <span class="text-danger text-sm mt-4">{{ $message }}</span> @enderror
                </div>
            </div>
            
            <div class="d-flex justify-end gap-12 mt-32 border-top pt-16">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="ph-bold ph-floppy-disk"></i>
                    <span>تحديث البيانات</span>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .border-bottom { border-bottom: 1px solid var(--clr-surface-300); }
    .border-top { border-top: 1px solid var(--clr-surface-300); }
</style>
@endsection
