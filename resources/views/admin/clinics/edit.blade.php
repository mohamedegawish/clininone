@extends('layouts.app')

@section('title', 'تعديل بيانات العيادة')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">تعديل العيادة: {{ $clinic->name }}</h1>
        <p class="page-subtitle">تحديث بيانات العيادة وحالتها في النظام</p>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">تعديل البيانات</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.clinics.update', $clinic) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-grid">
                <div class="form-group span-2">
                    <label class="form-label">اسم العيادة <span class="req">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $clinic->name) }}" required>
                </div>

                <div class="form-group span-2">
                    <label class="form-label">العنوان <span class="req">*</span></label>
                    <input type="text" name="address" class="form-control" value="{{ old('address', $clinic->address) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">حالة العيادة <span class="req">*</span></label>
                    <select name="status" class="form-control" required>
                        <option value="active" {{ old('status', $clinic->status) == 'active' ? 'selected' : '' }}>نشطة</option>
                        <option value="inactive" {{ old('status', $clinic->status) == 'inactive' ? 'selected' : '' }}>غير نشطة</option>
                    </select>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-24 gap-12">
                <a href="{{ route('admin.clinics.index') }}" class="btn btn-ghost">إلغاء</a>
                <button type="submit" class="btn btn-primary btn-lg px-40">
                    <i class="ph-bold ph-check"></i>
                    <span>حفظ التعديلات</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
