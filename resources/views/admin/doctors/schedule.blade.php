@extends('layouts.app')

@section('title', 'جدول مواعيد الطبيب')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">إدارة جدول المواعيد</h1>
        <p class="page-subtitle">تحديد أوقات العمل للطبيب: <span class="fw-700 text-primary">{{ $doctor->name }}</span></p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.doctors.index') }}" class="btn btn-ghost">
            <i class="ph-bold ph-arrow-right"></i>
            <span>العودة للأطباء</span>
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">جدول العمل الأسبوعي</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.doctors.schedule.update', $doctor) }}" method="POST">
            @csrf
            @method('PUT')
            
            <input type="hidden" name="clinic_id" value="{{ $clinicId }}">

            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 150px;">اليوم</th>
                            <th>وقت البدء</th>
                            <th>وقت الانتهاء</th>
                            <th>مدة الكشف (دقيقة)</th>
                            <th>الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $days = [
                                0 => 'الأحد',
                                1 => 'الاثنين',
                                2 => 'الثلاثاء',
                                3 => 'الأربعاء',
                                4 => 'الخميس',
                                5 => 'الجمعة',
                                6 => 'السبت'
                            ];
                        @endphp

                        @foreach($days as $dayNum => $dayName)
                            @php
                                $schedule = $schedules->firstWhere('day_of_week', $dayNum);
                            @endphp
                            <tr>
                                <td class="fw-600">{{ $dayName }}</td>
                                <td>
                                    <input type="time" name="schedules[{{ $dayNum }}][start_time]" 
                                           value="{{ $schedule ? \Carbon\Carbon::parse($schedule->start_time)->format('H:i') : '' }}" 
                                           class="form-control">
                                </td>
                                <td>
                                    <input type="time" name="schedules[{{ $dayNum }}][end_time]" 
                                           value="{{ $schedule ? \Carbon\Carbon::parse($schedule->end_time)->format('H:i') : '' }}" 
                                           class="form-control">
                                </td>
                                <td>
                                    <input type="number" name="schedules[{{ $dayNum }}][slot_duration]" 
                                           value="{{ $schedule->slot_duration ?? 30 }}" 
                                           class="form-control" min="5" max="120">
                                </td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="schedules[{{ $dayNum }}][is_active]" 
                                               value="1" {{ ($schedule->is_active ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label">{{ ($schedule->is_active ?? false) ? 'نشط' : 'غير نشط' }}</label>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-24">
                <button type="submit" class="btn btn-primary btn-lg px-40">
                    <i class="ph-bold ph-floppy-disk"></i>
                    <span>حفظ التعديلات</span>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .form-check-input:checked {
        background-color: var(--clr-primary-600);
        border-color: var(--clr-primary-600);
    }
    .form-switch .form-check-input {
        width: 40px; height: 20px; cursor: pointer;
    }
</style>
@endsection
