@extends('layouts.app')

@section('title', __('clinic.appointments.title'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('clinic.appointments.title') }}</h1>
        <p class="page-subtitle">{{ __('clinic.appointments.subtitle') }}</p>
    </div>
    <div class="page-header-actions" style="display: flex; gap: 12px; align-items: center;">
        
        {{-- Filter Dropdown --}}
        <form action="{{ route('clinic.appointments.index') }}" method="GET" id="filterForm">
            <select name="source" class="form-control" onchange="document.getElementById('filterForm').submit()" style="min-width: 180px;">
                <option value="">{{ __('clinic.appointments.filter_all') }}</option>
                <option value="clinic" {{ request('source') == 'clinic' ? 'selected' : '' }}>🏥 {{ __('clinic.appointments.clinic') }}</option>
                <option value="online" {{ request('source') == 'online' ? 'selected' : '' }}>🌐 {{ __('clinic.appointments.online') }}</option>
            </select>
        </form>

        <a href="{{ route('clinic.appointments.create') }}" class="btn btn-primary">
            <i class="ph-bold ph-calendar-plus"></i>
            <span>{{ __('clinic.appointments.add_new') }}</span>
        </a>
    </div>
</div>

<div class="card">
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 80px;">{{ __('clinic.appointments.queue_no') }}</th>
                    <th>{{ __('clinic.appointments.patient') }}</th>
                    <th>{{ __('clinic.appointments.doctor') }}</th>
                    <th>{{ __('clinic.appointments.date_time') }}</th>
                    <th>{{ __('clinic.appointments.source') }}</th>
                    <th>{{ __('clinic.appointments.payment') }}</th>
                    <th>{{ __('clinic.appointments.status') }}</th>
                    <th style="text-align: end;">{{ __('clinic.common.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($appointments as $appointment)
                <tr>
                    <td>
                        @if($appointment->status == 'confirmed' || $appointment->status == 'completed')
                            <span class="badge badge-primary" style="font-size: 14px; font-weight: 800;">{{ str_pad($appointment->queue_number, 3, '0', STR_PAD_LEFT) }}</span>
                        @else
                            <span class="badge badge-neutral" style="font-size: 14px; font-weight: 400; opacity: 0.5;">—</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('clinic.patients.show', $appointment->patient_id) }}" class="fw-700 color-primary">
                            {{ $appointment->patient?->full_name ?? '—' }}
                        </a>
                        <div class="text-xs text-muted" dir="ltr">{{ $appointment->patient?->phone ?? '' }}</div>
                    </td>
                    <td>
                        <div class="text-sm fw-600">Dr. {{ $appointment->doctor?->name ?? '—' }}</div>
                    </td>
                    <td dir="ltr">
                        <div class="fw-600">{{ $appointment->appointment_date?->format('Y-m-d') ?? '—' }}</div>
                        <div class="text-xs text-muted">{{ $appointment->start_time ? \Carbon\Carbon::parse($appointment->start_time)->format('h:i A') : '' }}</div>
                    </td>
                    <td>
                        @if($appointment->source == 'online')
                            <span class="badge" style="background: var(--clr-primary-50); color: var(--clr-primary-700); border: 1px solid var(--clr-primary-100);">
                                <i class="ph-bold ph-globe"></i> {{ __('clinic.appointments.online') }}
                            </span>
                        @else
                            <span class="badge badge-neutral">
                                <i class="ph-bold ph-buildings"></i> {{ __('clinic.appointments.clinic') }}
                            </span>
                        @endif
                    </td>
                    <td>
                        @if($appointment->is_paid)
                            <span class="badge badge-success dot">{{ __('clinic.appointments.paid') }}</span>
                        @else
                            <span class="badge badge-danger dot">{{ __('clinic.appointments.unpaid') }}</span>
                        @endif
                    </td>
                    <td>
                        @if($appointment->status == 'completed')
                            <span class="badge badge-success">{{ __('clinic.dashboard.completed') }}</span>
                        @elseif($appointment->status == 'cancelled')
                            <span class="badge badge-danger">{{ __('clinic.appointments.cancelled') }}</span>
                        @elseif($appointment->status == 'confirmed')
                            <span class="badge badge-primary">{{ __('clinic.appointments.confirmed') }}</span>
                        @else
                            <span class="badge badge-warning">{{ __('clinic.dashboard.pending') }}</span>
                        @endif
                    </td>
                    <td style="text-align: end;">
                        <div style="display: flex; gap: 8px; justify-content: flex-end;">
                            {{-- Confirm Button for Online/Pending --}}
                            @if($appointment->status == 'pending')
                                <form action="{{ route('clinic.appointments.confirm', $appointment->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="ph-bold ph-check"></i>
                                        <span>{{ __('clinic.appointments.confirm') }}</span>
                                    </button>
                                </form>
                            @endif

                            @if(!$appointment->is_paid)
                                <form action="{{ route('clinic.appointments.mark-paid', $appointment->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline">
                                        <i class="ph-bold ph-currency-dollar"></i>
                                        <span>{{ __('clinic.appointments.mark_paid') }}</span>
                                    </button>
                                </form>
                            @endif

                            @if($appointment->status == 'confirmed')
                                <a href="{{ route('clinic.consultations.create', $appointment->id) }}" class="btn btn-sm btn-primary">
                                    <i class="ph-bold ph-stethoscope"></i>
                                    <span>{{ __('clinic.appointments.start_consultation') }}</span>
                                </a>
                            @endif

                            @if($appointment->status == 'completed' && $appointment->consultation)
                                <a href="{{ route('clinic.consultations.show', $appointment->consultation->id) }}" class="btn btn-sm btn-ghost">
                                    <i class="ph-bold ph-file-text"></i>
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-40">
                        <i class="ph-bold ph-calendar-blank" style="font-size: 40px; display: block; margin-bottom: 12px; opacity: 0.3;"></i>
                        {{ __('clinic.common.no_data') }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($appointments->hasPages())
    <div class="card-footer" style="background: white;">{{ $appointments->links() }}</div>
    @endif
</div>
@endsection
