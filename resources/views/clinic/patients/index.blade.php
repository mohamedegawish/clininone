@extends('layouts.app')

@section('title', __('clinic.patients.title'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('clinic.patients.title') }}</h1>
        <p class="page-subtitle">{{ __('clinic.patients.subtitle') }}</p>
    </div>
    <div class="page-header-actions d-flex gap-12 align-center">
        <form action="{{ route('clinic.patients.index') }}" method="GET" class="d-flex gap-8">
            <input type="text" name="search" class="form-control" placeholder="{{ __('clinic.patients.search_placeholder') }}" value="{{ request('search') }}" style="min-width: 250px;">
            <button type="submit" class="btn btn-secondary"><i class="ph-bold ph-magnifying-glass"></i></button>
            @if(request('search'))
                <a href="{{ route('clinic.patients.index') }}" class="btn btn-ghost" title="Clear"><i class="ph-bold ph-x"></i></a>
            @endif
        </form>
        <a href="{{ route('clinic.patients.create') }}" class="btn btn-primary">
            <i class="ph-bold ph-plus"></i>
            <span>{{ __('clinic.patients.add_new') }}</span>
        </a>
    </div>
</div>

<div class="stats-grid mb-24">
    <div class="stat-card primary">
        <div class="stat-top">
            <span class="stat-label">{{ __('clinic.patients.total') }}</span>
            <div class="stat-icon"><i class="ph-fill ph-users"></i></div>
        </div>
        <div class="stat-value">{{ $summary['total'] }}</div>
    </div>
    <div class="stat-card success">
        <div class="stat-top">
            <span class="stat-label">{{ __('clinic.patients.active') }}</span>
            <div class="stat-icon"><i class="ph-fill ph-check-circle"></i></div>
        </div>
        <div class="stat-value">{{ $summary['active'] }}</div>
    </div>
</div>

<div class="card">
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>{{ __('clinic.patients.name') }}</th>
                    <th>{{ __('clinic.patients.phone') }}</th>
                    <th>{{ __('clinic.patients.dob') }}</th>
                    <th>{{ __('clinic.patients.nationality') }}</th>
                    <th>{{ __('clinic.patients.insurance_company') }}</th>
                    <th>{{ __('clinic.common.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($patients as $patient)
                <tr>
                    <td>
                        <div class="d-flex align-center gap-12">
                            <div class="user-avatar-initials" style="width: 36px; height: 36px; font-size: 14px; background: var(--clr-primary-400);">
                                {{ mb_substr($patient->full_name, 0, 2) }}
                            </div>
                            <div>
                                <div class="fw-600">{{ $patient->full_name }}</div>
                                <div class="text-muted text-sm">{{ $patient->english_name ?? '' }}</div>
                            </div>
                        </div>
                    </td>
                    <td dir="ltr">{{ $patient->phone }}</td>
                    <td>
                        @if($patient->birth_date)
                            {{ \Carbon\Carbon::parse($patient->birth_date)->format('Y-m-d') }}
                        @elseif($patient->age)
                            {{ $patient->age }} {{ __('clinic.patients.years') }}
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $patient->nationality ?? 'Egypt' }}</td>
                    <td>
                        @if($patient->company)
                            <span class="badge badge-primary">{{ $patient->company }}</span>
                        @else -
                        @endif
                    </td>
                    <td>
                        <div class="action-menu">
                            <a href="{{ route('clinic.patients.show', $patient->id) }}" class="btn-action view" title="{{ __('clinic.common.view') }}">
                                <i class="ph-bold ph-eye"></i>
                            </a>
                            <a href="{{ route('clinic.patients.edit', $patient->id) }}" class="btn-action edit" title="{{ __('clinic.common.edit') }}">
                                <i class="ph-bold ph-pencil-simple"></i>
                            </a>
                            <form action="{{ route('clinic.patients.destroy', $patient->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('clinic.common.confirm_delete') }}');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-action delete" title="{{ __('clinic.common.delete') }}">
                                    <i class="ph-bold ph-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-24">{{ __('clinic.common.no_data') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($patients->hasPages())
    <div class="card-footer">{{ $patients->links() }}</div>
    @endif
</div>
@endsection
