@extends('layouts.app')

@section('title', __('clinic.patients.patient_profile'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('clinic.patients.patient_profile') }}</h1>
        <p class="page-subtitle">{{ __('clinic.patients.subtitle') }}</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('clinic.patients.edit', $patient->id) }}" class="btn btn-primary">
            <i class="ph-bold ph-pencil-simple"></i>
            <span>{{ __('clinic.common.edit') }}</span>
        </a>
        <a href="{{ route('clinic.patients.index') }}" class="btn btn-ghost">
            <i class="ph-bold ph-arrow-left"></i>
            <span>{{ __('clinic.common.back') }}</span>
        </a>
    </div>
</div>

<div class="dashboard-grid" style="grid-template-columns: 350px 1fr; gap: 24px; align-items: start;">
    
    {{-- Sidebar: Basic & Insurance Info --}}
    <div style="display: flex; flex-direction: column; gap: 24px;">
        
        {{-- Profile Summary Card --}}
        <div class="card">
            <div class="card-body text-center" style="padding-top: 32px;">
                <div class="user-avatar-initials" style="width: 80px; height: 80px; font-size: 32px; background: linear-gradient(135deg, var(--clr-primary-500), var(--clr-primary-700)); margin: 0 auto 16px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);">
                    {{ mb_substr($patient->full_name, 0, 2) }}
                </div>
                <h2 class="fw-800 mb-4" style="font-size: 20px;">{{ $patient->full_name }}</h2>
                <p class="text-muted text-sm mb-16">{{ $patient->english_name }}</p>
                <div style="display: flex; justify-content: center; gap: 8px;">
                    <span class="badge {{ $patient->status == 'active' ? 'badge-success' : 'badge-danger' }}">
                        {{ $patient->status == 'active' ? __('clinic.common.active') : __('clinic.common.inactive') }}
                    </span>
                    <span class="badge badge-primary">{{ $patient->blood_group ?? '—' }}</span>
                </div>
            </div>
            <div class="card-footer" style="padding: 0; background: var(--clr-n-50); border-top: 1px solid var(--clr-n-100);">
                <div style="display: grid; grid-template-columns: 1fr 1fr; border-top: 1px solid var(--clr-n-100);">
                    <div style="padding: 16px; text-align: center; border-inline-end: 1px solid var(--clr-n-100);">
                        <div class="text-xs text-muted uppercase fw-700 mb-4">{{ __('clinic.patients.total_visits') }}</div>
                        <div class="fw-800 text-lg color-primary">{{ $patient->appointments->where('status', 'completed')->count() }}</div>
                    </div>
                    <div style="padding: 16px; text-align: center;">
                        <div class="text-xs text-muted uppercase fw-700 mb-4">{{ __('clinic.patients.gender') }}</div>
                        <div class="fw-800">{{ $patient->gender == 'male' ? __('clinic.patients.gender_male') : __('clinic.patients.gender_female') }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Contact Details --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ __('clinic.patients.basic_info') }}</h3>
            </div>
            <div class="card-body" style="padding: 20px;">
                <div class="mb-16">
                    <div class="text-xs text-muted uppercase fw-700 mb-4">{{ __('clinic.patients.phone') }}</div>
                    <div class="fw-600" dir="ltr">{{ $patient->phone }}</div>
                </div>
                <div class="mb-16">
                    <div class="text-xs text-muted uppercase fw-700 mb-4">{{ __('clinic.patients.email') }}</div>
                    <div class="fw-600">{{ $patient->email ?? '—' }}</div>
                </div>
                <div class="mb-16">
                    <div class="text-xs text-muted uppercase fw-700 mb-4">{{ __('clinic.patients.dob') }}</div>
                    <div class="fw-600">
                        {{ $patient->birth_date ? \Carbon\Carbon::parse($patient->birth_date)->format('Y-m-d') : ($patient->age ? $patient->age . ' ' . __('clinic.patients.years') : '—') }}
                    </div>
                </div>
                <div>
                    <div class="text-xs text-muted uppercase fw-700 mb-4">{{ __('clinic.patients.address') }}</div>
                    <div class="fw-600">{{ $patient->address ?? '—' }}</div>
                </div>
            </div>
        </div>

        {{-- Insurance Card --}}
        <div class="card" style="background: linear-gradient(135deg, #1e293b, #0f172a); color: white; border: none;">
            <div class="card-header" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                <h3 class="card-title" style="color: white;">{{ __('clinic.patients.insurance_info') }}</h3>
            </div>
            <div class="card-body" style="padding: 20px;">
                @if($patient->company)
                    <div class="mb-16">
                        <div class="text-xs uppercase fw-700 mb-4" style="color: rgba(255,255,255,0.5);">{{ __('clinic.patients.insurance_company') }}</div>
                        <div class="fw-700 text-lg">{{ $patient->company }}</div>
                    </div>
                    <div class="mb-16">
                        <div class="text-xs uppercase fw-700 mb-4" style="color: rgba(255,255,255,0.5);">{{ __('clinic.patients.policy_name') }}</div>
                        <div class="fw-600">{{ $patient->policy_name }}</div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div>
                            <div class="text-xs uppercase fw-700 mb-4" style="color: rgba(255,255,255,0.5);">{{ __('clinic.patients.class') }}</div>
                            <div class="fw-600">{{ $patient->class ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs uppercase fw-700 mb-4" style="color: rgba(255,255,255,0.5);">{{ __('clinic.patients.card_no') }}</div>
                            <div class="fw-600">{{ $patient->card_no ?? '—' }}</div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-16">
                        <i class="ph-bold ph-shield-slash" style="font-size: 32px; color: rgba(255,255,255,0.2); display: block; margin-bottom: 8px;"></i>
                        <span style="color: rgba(255,255,255,0.5);">{{ __('clinic.patients.not_registered') }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Main Area: History & Timeline --}}
    <div style="display: flex; flex-direction: column; gap: 24px;">
        
        {{-- Medical History --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ __('clinic.patients.medical_history') }}</h3>
            </div>
            <div class="card-body">
                <div style="padding: 20px; background: var(--clr-primary-50); border-radius: 12px; border: 1px solid var(--clr-primary-100); line-height: 1.7; color: var(--clr-primary-900);">
                    <i class="ph-bold ph-info" style="margin-inline-end: 8px; color: var(--clr-primary-500);"></i>
                    {{ $patient->medical_history ?? __('clinic.common.no_data') }}
                </div>
            </div>
        </div>

        {{-- Timeline / Visits --}}
        <div class="card">
            <div class="card-header d-flex justify-between align-center">
                <h3 class="card-title">{{ __('clinic.patients.timeline') }}</h3>
                <span class="badge badge-neutral">{{ $patient->appointments->count() }} {{ __('clinic.patients.visits') }}</span>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-wrapper" style="border: none;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('clinic.appointments.date_time') }}</th>
                                <th>{{ __('clinic.appointments.doctor') }}</th>
                                <th>{{ __('clinic.appointments.status') }}</th>
                                <th style="text-align: end;">{{ __('clinic.common.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($patient->appointments->sortByDesc('appointment_date') as $appointment)
                            <tr>
                                <td dir="ltr">
                                    <div class="fw-700">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('Y-m-d') }}</div>
                                    <div class="text-xs text-muted">{{ $appointment->start_time ? \Carbon\Carbon::parse($appointment->start_time)->format('h:i A') : '—' }}</div>
                                </td>
                                <td>
                                    <div class="d-flex align-center gap-8">
                                        <div class="user-avatar-initials" style="width: 24px; height: 24px; font-size: 10px;">
                                            {{ mb_substr($appointment->doctor->name ?? 'D', 0, 1) }}
                                        </div>
                                        <span class="fw-600">{{ $appointment->doctor?->name ?? '—' }}</span>
                                    </div>
                                </td>
                                <td>
                                    @if($appointment->status == 'completed')
                                        <span class="badge badge-success">{{ __('clinic.dashboard.completed') }}</span>
                                    @elseif($appointment->status == 'pending')
                                        <span class="badge badge-primary">{{ __('clinic.dashboard.pending') }}</span>
                                    @elseif($appointment->status == 'cancelled')
                                        <span class="badge badge-danger">{{ __('clinic.appointments.cancelled') }}</span>
                                    @else
                                        <span class="badge badge-neutral">{{ $appointment->status }}</span>
                                    @endif
                                </td>
                                <td style="text-align: end;">
                                    @if($appointment->status == 'completed')
                                        @php
                                            $consultation = \App\Models\core\Consultation::where('appointment_id', $appointment->id)->first();
                                        @endphp
                                        @if($consultation)
                                        <a href="{{ route('clinic.consultations.show', $consultation->id) }}" class="btn btn-sm btn-accent">
                                            <i class="ph-bold ph-eye"></i>
                                            <span>{{ __('clinic.consultations.print') }}</span>
                                        </a>
                                        @endif
                                    @elseif($appointment->status == 'confirmed')
                                        <a href="{{ route('clinic.consultations.create', $appointment->id) }}" class="btn btn-sm btn-primary">
                                            {{ __('clinic.appointments.start_consultation') }}
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-40 text-muted">
                                    <i class="ph-bold ph-calendar-blank" style="font-size: 40px; display: block; margin-bottom: 12px; opacity: 0.3;"></i>
                                    {{ __('clinic.patients.no_visits') }}
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
