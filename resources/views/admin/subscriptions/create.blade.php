@extends('layouts.app')

@section('title', 'إضافة اشتراك جديد')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">إنشاء اشتراك لعيادة</h1>
        <p class="page-subtitle">تفعيل خطة خدمة جديدة لعيادة معينة</p>
    </div>
</div>

<div class="card" style="max-width: 800px;">
    <div class="card-header">
        <h3 class="card-title">بيانات الاشتراك</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.subscriptions.store') }}" method="POST">
            @csrf
            
            <div class="form-grid">
                <div class="form-group span-2">
                    <label class="form-label">اختر العيادة <span class="req">*</span></label>
                    <select name="clinic_id" class="form-control" required>
                        <option value="">اختر العيادة...</option>
                        @foreach($clinics as $clinic)
                            <option value="{{ $clinic->id }}">{{ $clinic->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group span-2">
                    <label class="form-label">اختر الخطة <span class="req">*</span></label>
                    <select name="plan_id" class="form-control" required id="planSelect">
                        <option value="">اختر الخطة...</option>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" data-duration="{{ $plan->duration }}">{{ $plan->name }} ({{ number_format($plan->price) }} ج.م)</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">تاريخ البدء <span class="req">*</span></label>
                    <input type="date" name="start_at" class="form-control" id="startDate" value="{{ date('Y-m-d') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">تاريخ الانتهاء <span class="req">*</span></label>
                    <input type="date" name="end_at" class="form-control" id="endDate" required>
                </div>

                <div class="form-group">
                    <label class="form-label">الحالة <span class="req">*</span></label>
                    <select name="status" class="form-control" required>
                        <option value="active">نشط</option>
                        <option value="pending">معلق</option>
                        <option value="expired">منتهي</option>
                        <option value="cancelled">ملغي</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">تجديد تلقائي</label>
                    <select name="auto_renew" class="form-control">
                        <option value="0">لا</option>
                        <option value="1">نعم</option>
                    </select>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-24">
                <button type="submit" class="btn btn-primary btn-lg px-40">
                    <i class="ph-bold ph-check"></i>
                    <span>تفعيل الاشتراك</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // تلقائياً حساب تاريخ الانتهاء بناءً على الخطة المختارة
    document.getElementById('planSelect').addEventListener('change', updateEndDate);
    document.getElementById('startDate').addEventListener('change', updateEndDate);

    function updateEndDate() {
        const planSelect = document.getElementById('planSelect');
        const startDateInput = document.getElementById('startDate');
        const endDateInput = document.getElementById('endDate');

        const selectedOption = planSelect.options[planSelect.selectedIndex];
        const duration = selectedOption.getAttribute('data-duration');

        if (duration && startDateInput.value) {
            const startDate = new Date(startDateInput.value);
            startDate.setDate(startDate.getDate() + parseInt(duration));
            endDateInput.value = startDate.toISOString().split('T')[0];
        }
    }
</script>
@endsection
