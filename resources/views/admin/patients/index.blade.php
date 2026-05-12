@extends('layouts.app')

@section('title', __('admin.patients.title'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('admin.patients.title') }}</h1>
        <p class="page-subtitle">{{ __('admin.patients.subtitle', ['count' => $patients->total()]) }}</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.patients.create') }}" class="btn btn-primary">
            <i class="ph-bold ph-user-plus"></i>
            <span>{{ __('admin.patients.add_new') }}</span>
        </a>
    </div>
</div>

<form method="GET" class="card p-16 mb-24">
    <div class="row g-12 align-items-end">
        <div class="col-md-4">
            <label class="form-label mb-8">{{ __('admin.patients.clinic') }}</label>
            <select name="clinic_id" class="form-control">
                <option value="">{{ __('admin.dashboard.view_all') }}</option>
                @foreach($clinics as $clinic)
                    <option value="{{ $clinic->id }}" @selected(($clinicId ?? null) == $clinic->id)>{{ $clinic->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100">{{ __('admin.dashboard.top_specialties') }}</button> {{-- Using a generic filter word if needed --}}
        </div>
    </div>
</form>

<div class="card overflow-hidden">
    <div class="table-wrapper">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th class="ps-3">{{ __('admin.patients.name') }}</th>
                    <th>{{ __('admin.patients.phone') }}</th>
                    <th>{{ __('admin.patients.clinic') }}</th>
                    <th>{{ __('admin.patients.last_visit') }}</th>
                    <th class="text-end pe-3">{{ __('admin.common.actions') }}</th>
                </tr>
            </thead>
            <tbody>
            @forelse($patients as $patient)
                <tr>
                    <td class="ps-3 fw-semibold">
                        <a class="text-decoration-none" href="{{ route('admin.patients.show', $patient) }}">{{ $patient->full_name }}</a>
                    </td>
                    <td>{{ $patient->phone }}</td>
                    <td>{{ $patient->clinic?->name }}</td>
                    <td>{{ $patient->appointments_max_appointment_date ? \Illuminate\Support\Carbon::parse($patient->appointments_max_appointment_date)->format('Y-m-d') : __('admin.patients.no_data') }}</td>
                    <td class="text-end pe-3">
                        <div class="d-flex justify-content-end gap-8">
                            <a href="{{ route('admin.patients.show', $patient) }}" class="btn btn-sm btn-ghost">{{ __('admin.common.view_all') }}</a>
                            <a href="{{ route('admin.patients.edit', $patient) }}" class="btn btn-sm btn-ghost">{{ __('admin.common.edit') }}</a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td class="text-center py-4 text-muted" colspan="5">{{ __('admin.patients.no_data') }}</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-24">{{ $patients->links() }}</div>

<style>
    .p-16 { padding: 16px; }
    .mb-24 { margin-bottom: 24px; }
    .mb-8 { margin-bottom: 8px; }
    .mt-24 { margin-top: 24px; }
</style>
@endsection
