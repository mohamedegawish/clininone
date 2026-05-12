@extends('layouts.app')

@section('title', __('admin.clinics.change_plan') . ' - ' . $clinic->name)

@section('content')
<div class="page-header mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('admin.clinics.index') }}" class="btn btn-icon btn-light rounded-circle shadow-sm">
            <i class="ph-bold ph-arrow-left"></i>
        </a>
        <div>
            <h1 class="page-title fw-bold mb-1" style="color: var(--clr-primary-800);">{{ __('admin.clinics.change_plan') }}</h1>
            <p class="page-subtitle text-muted mb-0">{{ $clinic->name }}</p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card shadow-sm border-0" style="border-radius: 20px; overflow: hidden;">
            <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                <h4 class="card-title fw-bold">
                    <i class="ph-bold ph-credit-card text-primary me-2"></i>
                    {{ __('admin.clinics.select_plan') }}
                </h4>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.clinics.assign-plan', $clinic) }}" method="POST">
                    @csrf
                    
                    <div class="form-group mb-4">
                        <label class="form-label fw-bold mb-2">{{ __('admin.clinics.plan') }}</label>
                        <select name="plan_id" class="form-select form-control form-control-lg @error('plan_id') is-invalid @enderror" required style="border-radius: 12px; cursor: pointer;">
                            <option value="">-- {{ __('admin.clinics.select_plan') }} --</option>
                            @foreach($plans as $plan)
                                <option value="{{ $plan->id }}">{{ $plan->name }} ({{ number_format($plan->price) }} {{ __('admin.common.currency') }} / {{ $plan->duration }} {{ __('admin.plans.duration') }})</option>
                            @endforeach
                        </select>
                        @error('plan_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-4">
                                <label class="form-label fw-bold mb-2">{{ __('admin.clinics.start_date') }}</label>
                                <input type="date" name="start_at" class="form-control form-control-lg @error('start_at') is-invalid @enderror" required style="border-radius: 12px;" value="{{ old('start_at', date('Y-m-d')) }}">
                                @error('start_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-4">
                                <label class="form-label fw-bold mb-2">{{ __('admin.clinics.end_date') }}</label>
                                <input type="date" name="end_at" class="form-control form-control-lg @error('end_at') is-invalid @enderror" required style="border-radius: 12px;" value="{{ old('end_at', date('Y-m-d', strtotime('+1 year'))) }}">
                                @error('end_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-2">
                        <a href="{{ route('admin.clinics.index') }}" class="btn btn-light px-4 py-2" style="border-radius: 12px; font-weight: 600;">
                            {{ __('admin.common.cancel') ?? 'Cancel' }}
                        </a>
                        <button type="submit" class="btn btn-primary px-4 py-2 shadow-sm" style="border-radius: 12px; font-weight: 600; background: linear-gradient(135deg, #2563eb, #1f5d96); border: none;">
                            <i class="ph-bold ph-check-circle me-1"></i>
                            {{ __('admin.common.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
