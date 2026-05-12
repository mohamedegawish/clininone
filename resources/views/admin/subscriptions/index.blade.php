@extends('layouts.app')

@section('title', __('admin.subscriptions.title'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('admin.subscriptions.title') }}</h1>
        <p class="page-subtitle">{{ __('admin.subscriptions.subtitle') }}</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.subscriptions.create') }}" class="btn btn-primary">
            <i class="ph-bold ph-plus"></i>
            <span>{{ __('admin.subscriptions.add_new') }}</span>
        </a>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card primary">
        <div class="stat-top">
            <span class="stat-label">{{ __('admin.subscriptions.total') }}</span>
            <div class="stat-icon"><i class="ph-fill ph-credit-card"></i></div>
        </div>
        <div class="stat-value">{{ $subscriptions->total() }}</div>
    </div>
    <div class="stat-card warning">
        <div class="stat-top">
            <span class="stat-label">{{ __('admin.subscriptions.expiring_soon') }}</span>
            <div class="stat-icon"><i class="ph-fill ph-clock-counter-clockwise"></i></div>
        </div>
        <div class="stat-value">{{ $expiringSoonCount }}</div>
    </div>
    <div class="stat-card danger">
        <div class="stat-top">
            <span class="stat-label">{{ __('admin.subscriptions.expired') }}</span>
            <div class="stat-icon"><i class="ph-fill ph-x-circle"></i></div>
        </div>
        <div class="stat-value">{{ $expiredCount }}</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ __('admin.subscriptions.list') }}</h3>
    </div>
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>{{ __('admin.subscriptions.clinic') }}</th>
                    <th>{{ __('admin.subscriptions.plan') }}</th>
                    <th>{{ __('admin.subscriptions.start_date') }}</th>
                    <th>{{ __('admin.subscriptions.end_date') }}</th>
                    <th>{{ __('admin.subscriptions.price') }}</th>
                    <th>{{ __('admin.subscriptions.status') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($subscriptions as $sub)
                <tr>
                    <td>
                        <div class="fw-600 text-dark">{{ $sub->clinic->name }}</div>
                    </td>
                    <td>
                        <span class="badge badge-primary">{{ $sub->plan->name }}</span>
                    </td>
                    <td class="muted">{{ $sub->start_at->format('Y/m/d') }}</td>
                    <td class="muted">{{ $sub->end_at->format('Y/m/d') }}</td>
                    <td class="fw-600">{{ number_format($sub->plan->price) }} {{ __('admin.common.currency') }}</td>
                    <td>
                        <span class="badge dot {{ 
                            $sub->status === 'active' ? 'badge-success' : (
                            $sub->status === 'expired' ? 'badge-danger' : (
                            $sub->status === 'pending' ? 'badge-warning' : 'badge-neutral'))
                        }}">
                            {{ 
                                $sub->status === 'active' ? __('admin.subscriptions.status_active') : (
                                $sub->status === 'expired' ? __('admin.subscriptions.status_expired') : (
                                $sub->status === 'pending' ? __('admin.subscriptions.status_pending') : __('admin.subscriptions.status_cancelled')))
                            }}
                        </span>
                    </td>
                    <td>
                        {{-- Actions placeholder --}}
                        <button class="btn-action edit"><i class="ph-bold ph-pencil"></i></button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="mt-24">
    {{ $subscriptions->links() }}
</div>
@endsection
