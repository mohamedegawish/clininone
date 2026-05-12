@extends('layouts.app')

@section('title', __('clinic.dashboard.title'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('clinic.dashboard.title') }}</h1>
        <p class="page-subtitle">{{ __('clinic.dashboard.subtitle') }}</p>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card primary">
        <div class="stat-top">
            <span class="stat-label">{{ __('clinic.dashboard.today_appointments') }}</span>
            <div class="stat-icon">
                <i class="ph-fill ph-calendar-check"></i>
            </div>
        </div>
        <div class="stat-value">{{ $stats['today_appointments'] }}</div>
    </div>

    <div class="stat-card warning">
        <div class="stat-top">
            <span class="stat-label">{{ __('clinic.dashboard.pending') }}</span>
            <div class="stat-icon">
                <i class="ph-fill ph-users-four"></i>
            </div>
        </div>
        <div class="stat-value">{{ $stats['pending_today'] }}</div>
    </div>

    <div class="stat-card success">
        <div class="stat-top">
            <span class="stat-label">{{ __('clinic.dashboard.completed') }}</span>
            <div class="stat-icon">
                <i class="ph-fill ph-check-circle"></i>
            </div>
        </div>
        <div class="stat-value">{{ $stats['completed_today'] }}</div>
    </div>

    <div class="stat-card accent">
        <div class="stat-top">
            <span class="stat-label">{{ __('clinic.dashboard.revenue') }}</span>
            <div class="stat-icon">
                <i class="ph-fill ph-money"></i>
            </div>
        </div>
        <div class="stat-value" dir="ltr">{{ number_format($stats['revenue_today']) }} EGP</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ __('clinic.dashboard.recent_appointments') }}</h3>
        <a href="{{ route('clinic.appointments.index') }}" class="btn btn-sm btn-outline">{{ __('clinic.dashboard.view_all') }}</a>
    </div>
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('clinic.dashboard.patient') }}</th>
                    <th>{{ __('clinic.dashboard.time') }}</th>
                    <th>{{ __('clinic.appointments.source') }}</th>
                    <th>{{ __('clinic.appointments.status') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentAppointments ?? [] as $appointment)
                <tr>
                    <td>{{ str_pad($appointment->queue_number, 3, '0', STR_PAD_LEFT) }}</td>
                    <td class="fw-600">{{ $appointment->patient->full_name ?? '-' }}</td>
                    <td dir="ltr">{{ substr($appointment->start_time, 0, 5) }}</td>
                    <td>
                        @if($appointment->source == 'online')
                            <span class="badge badge-primary">{{ __('clinic.appointments.online') }}</span>
                        @else
                            <span class="badge badge-neutral">{{ __('clinic.appointments.clinic') }}</span>
                        @endif
                    </td>
                    <td>
                        @if($appointment->status == 'completed')
                            <span class="badge badge-success dot">{{ __('clinic.dashboard.completed') }}</span>
                        @elseif($appointment->status == 'cancelled')
                            <span class="badge badge-danger dot">{{ __('clinic.appointments.cancelled') }}</span>
                        @else
                            <span class="badge badge-warning dot">{{ __('clinic.dashboard.pending') }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted" style="padding: 40px;">{{ __('clinic.common.no_data') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
