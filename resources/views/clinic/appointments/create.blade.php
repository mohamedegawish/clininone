@extends('layouts.app')

@section('title', __('clinic.appointments.add_new'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('clinic.appointments.add_new') }}</h1>
        <p class="page-subtitle">{{ __('clinic.appointments.add_subtitle') }}</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('clinic.appointments.index') }}" class="btn btn-secondary">
            <i class="ph-bold ph-arrow-left"></i>
            <span>{{ __('clinic.common.back') }}</span>
        </a>
    </div>
</div>

<div class="dashboard-grid-100">
    <div class="card">
        <div class="card-header border-bottom mb-24" style="padding-bottom: 16px;">
            <h3 class="card-title">
                <i class="ph-fill ph-calendar-plus" style="margin-inline-end: 8px; color: var(--clr-primary-500);"></i>
                {{ __('clinic.appointments.add_new') }}
            </h3>
        </div>
        
        <div class="card-body">
            <form action="{{ route('clinic.appointments.store') }}" method="POST">
                @csrf
                
                {{-- Section 1: Patient Information --}}
                <div class="form-section mb-32">
                    <h4 class="form-section-title mb-16" style="color: var(--clr-n-600); font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                        1. {{ __('clinic.appointments.patient_info') }}
                    </h4>
                    
                    <div class="dashboard-grid" style="grid-template-columns: 1fr 1fr; gap: 24px;">
                        <div class="form-group mb-0">
                            <label class="form-label" for="patient_search">
                                <i class="ph-bold ph-magnifying-glass"></i>
                                {{ __('clinic.appointments.search_patient') }}
                            </label>
                            <input type="text" id="patient_search" class="form-control" placeholder="{{ __('clinic.common.search') }}">
                        </div>
                        
                        <div class="form-group mb-0">
                            <label class="form-label" for="patient_id">
                                <i class="ph-bold ph-user"></i>
                                {{ __('clinic.appointments.patient') }} <span class="text-danger">*</span>
                            </label>
                            <select name="patient_id" id="patient_id" class="form-control" required style="padding: 10px;">
                                <option value="" disabled selected>{{ __('clinic.appointments.select_patient') }}</option>
                                @foreach($patients as $patient)
                                    <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                        {{ $patient->full_name }} — {{ $patient->phone }}
                                    </option>
                                @endforeach
                            </select>
                            @error('patient_id') <span class="form-error">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <hr style="border: 0; border-top: 1px dashed var(--clr-n-200); margin: 32px 0;">

                {{-- Section 2: Appointment Settings --}}
                <div class="form-section mb-32">
                    <h4 class="form-section-title mb-16" style="color: var(--clr-n-600); font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                        2. {{ __('clinic.appointments.schedule_doctor') }}
                    </h4>
                    
                    <div class="dashboard-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 24px;">
                        <div class="form-group mb-0">
                            <label class="form-label" for="doctor_id">
                                <i class="ph-bold ph-stethoscope"></i>
                                {{ __('clinic.appointments.doctor') }} <span class="text-danger">*</span>
                            </label>
                            <select name="doctor_id" id="doctor_id" class="form-control" required>
                                @foreach($doctors as $doctor)
                                    <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                        Dr. {{ $doctor->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('doctor_id') <span class="form-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group mb-0">
                            <label class="form-label" for="appointment_date">
                                <i class="ph-bold ph-calendar"></i>
                                {{ __('clinic.appointments.date') }} <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="appointment_date" id="appointment_date" class="form-control" value="{{ old('appointment_date', date('Y-m-d')) }}" required>
                            @error('appointment_date') <span class="form-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group mb-0">
                            <label class="form-label" for="start_time">
                                <i class="ph-bold ph-clock"></i>
                                {{ __('clinic.appointments.time') }} <span class="text-danger">*</span>
                            </label>
                            <input type="time" name="start_time" id="start_time" class="form-control" value="{{ old('start_time') }}" required>
                            @error('start_time') <span class="form-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group mb-0">
                            <label class="form-label" for="type">
                                <i class="ph-bold ph-clipboard-text"></i>
                                {{ __('clinic.appointments.type') }} <span class="text-danger">*</span>
                            </label>
                            <select name="type" id="type" class="form-control" required>
                                <option value="consultation" {{ old('type') == 'consultation' ? 'selected' : '' }}>{{ __('clinic.appointments.type_consultation') }}</option>
                                <option value="followup" {{ old('type') == 'followup' ? 'selected' : '' }}>{{ __('clinic.appointments.type_followup') }}</option>
                            </select>
                            @error('type') <span class="form-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group mb-0">
                            <label class="form-label" for="source">
                                <i class="ph-bold ph-laptop"></i>
                                {{ __('clinic.appointments.source') }} <span class="text-danger">*</span>
                            </label>
                            <select name="source" id="source" class="form-control" required>
                                <option value="clinic" {{ old('source') == 'clinic' ? 'selected' : '' }}>{{ __('clinic.appointments.clinic') }}</option>
                                <option value="online" {{ old('source') == 'online' ? 'selected' : '' }}>{{ __('clinic.appointments.online') }}</option>
                            </select>
                            @error('source') <span class="form-error">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <hr style="border: 0; border-top: 1px dashed var(--clr-n-200); margin: 32px 0;">

                {{-- Section 3: Services & Payment --}}
                <div class="form-section mb-32">
                    <h4 class="form-section-title mb-16" style="color: var(--clr-n-600); font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                        3. {{ __('clinic.appointments.services_payment') }}
                    </h4>
                    
                    <div class="form-group mb-24">
                        <label class="form-label mb-12">
                            <i class="ph-bold ph-list-plus"></i>
                            {{ __('clinic.appointments.services_payment') }}
                        </label>
                        <div class="dashboard-grid" style="grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 12px;" id="doctor_services_container">
                            @if(isset($doctorServices) && $doctorServices->count() > 0)
                                @foreach($doctorServices as $service)
                                    <label class="d-flex align-center gap-12 cursor-pointer p-16 card mb-0" style="border: 1px solid var(--clr-n-200); transition: all 0.2s ease; border-radius: var(--radius-md);">
                                        <input type="checkbox" name="services[]" value="{{ $service->id }}" style="width: 20px; height: 20px; accent-color: var(--clr-primary-500);">
                                        <div>
                                            <div class="fw-600 text-n-800" style="font-size: 14px;">{{ $service->name }}</div>
                                            <div class="text-sm" style="color: var(--clr-primary-600); font-weight: 700;">+ {{ $service->price }} EGP</div>
                                        </div>
                                    </label>
                                @endforeach
                            @else
                                <div class="p-16 card mb-0 d-flex align-center justify-center" style="border: 1px dashed var(--clr-n-300); background: var(--clr-n-50); grid-column: 1 / -1;">
                                    <p class="text-muted text-sm m-0">
                                        <i class="ph-bold ph-info" style="margin-inline-end: 4px;"></i>
                                        {{ __('clinic.appointments.no_services') }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="dashboard-grid" style="grid-template-columns: 1fr 1fr; gap: 24px; align-items: start;">
                        <div class="form-group mb-0">
                            <label class="form-label" for="notes">
                                <i class="ph-bold ph-note"></i>
                                {{ __('clinic.appointments.notes') }}
                            </label>
                            <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="{{ __('clinic.appointments.notes_placeholder') }}">{{ old('notes') }}</textarea>
                        </div>
                        
                        <div class="form-group mb-0 p-24" style="background: var(--clr-n-50); border-radius: var(--radius-md); border: 1px solid var(--clr-n-200);">
                            <label class="d-flex align-center gap-12 cursor-pointer">
                                <input type="checkbox" name="is_paid" id="is_paid" value="1" {{ old('is_paid') ? 'checked' : '' }} style="width: 24px; height: 24px; accent-color: var(--clr-success-500);">
                                <div>
                                    <span class="fw-700 text-n-800 d-block" style="font-size: 16px;">{{ __('clinic.appointments.is_paid_label') }}</span>
                                    <span class="text-sm text-muted">{{ __('clinic.appointments.mark_paid_desc') }}</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-end gap-16 mt-40 pt-24" style="border-top: 1px solid var(--clr-n-200);">
                    <a href="{{ route('clinic.appointments.index') }}" class="btn btn-outline" style="padding: 12px 24px;">
                        {{ __('clinic.common.cancel') }}
                    </a>
                    <button type="submit" class="btn btn-primary" style="padding: 12px 32px; font-size: 16px;">
                        <i class="ph-bold ph-check-circle" style="font-size: 20px;"></i>
                        <span>{{ __('clinic.appointments.confirm_btn') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('patient_search');
        const selectBox = document.getElementById('patient_id');
        const options = Array.from(selectBox.options).slice(1); // skip first disabled option

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            let hasMatch = false;
            options.forEach(option => {
                const text = option.text.toLowerCase();
                if (text.includes(searchTerm)) {
                    option.style.display = '';
                    if (!hasMatch) {
                        selectBox.value = option.value;
                        hasMatch = true;
                    }
                } else {
                    option.style.display = 'none';
                }
            });
            
            if (!hasMatch) {
                selectBox.value = "";
            }
        });
    });
</script>
@endpush
