@extends('layouts.app')

@section('title', __('admin.plans.title'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('admin.plans.title') }}</h1>
        <p class="page-subtitle">{{ __('admin.plans.subtitle') }}</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.plans.create') }}" class="btn btn-primary">
            <i class="ph-bold ph-plus"></i>
            <span>{{ __('admin.plans.add_new') }}</span>
        </a>
    </div>
</div>

<div class="row g-32">
    @forelse($plans as $index => $plan)
    <div class="col-xl-3 col-lg-4 col-md-6">
        <div class="plan-card-premium {{ $index === 1 ? 'popular-plan' : '' }}">
            @if($index === 1)
            <div class="popular-badge"><i class="ph-fill ph-star me-1"></i> الأكثر طلباً</div>
            @endif
            
            <div class="card-color-body">
                <div class="plan-header">
                    <h3 class="plan-name">{{ $plan->name }}</h3>
                    <div class="plan-price-wrapper">
                        <span class="price-value">{{ number_format($plan->price) }}</span>
                        <div class="price-meta">
                            <span class="currency">{{ __('admin.common.currency') }}</span>
                            <span class="duration">/ {{ $plan->duration }} {{ __('admin.plans.duration') }}</span>
                        </div>
                    </div>
                </div>

                <div class="plan-features">
                    <ul class="feature-list">
                        <li>
                            <i class="ph-bold ph-check"></i>
                            <span>{{ __('admin.plans.limit_patients') }}: <strong>{{ $plan->max_patients ?: __('admin.plans.unlimited') }}</strong></span>
                        </li>
                        <li>
                            <i class="ph-bold ph-check"></i>
                            <span>{{ __('admin.plans.limit_appointments') }}: <strong>{{ $plan->max_appointments ?: __('admin.plans.unlimited') }}</strong></span>
                        </li>
                        @if($plan->features)
                            @foreach(explode("\n", $plan->features) as $feature)
                                @if(trim($feature))
                                <li>
                                    <i class="ph-bold ph-check"></i>
                                    <span>{{ trim($feature) }}</span>
                                </li>
                                @endif
                            @endforeach
                        @endif
                    </ul>
                </div>
                
                <div class="plan-actions">
                    <a href="{{ route('admin.plans.edit', $plan) }}" class="btn-edit-plan">
                        <i class="ph-bold ph-pencil-simple"></i>
                        {{ __('admin.plans.edit') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="empty-state-glass">
            <i class="ph ph-package"></i>
            <h5>لا توجد باقات متاحة حالياً</h5>
        </div>
    </div>
    @endforelse
</div>

<div class="mt-5 d-flex justify-content-center">
    {{ $plans->links() }}
</div>

<style>
    /* Premium Colorful Plan Cards */
    .plan-card-premium {
        height: 100%;
        position: relative;
        z-index: 1;
        border-radius: 20px;
        perspective: 1000px;
        margin-top: 12px;
    }

    .popular-badge {
        position: absolute;
        top: -12px;
        left: 50%;
        transform: translateX(-50%);
        background: #fff;
        color: #0f172a;
        padding: 4px 14px;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        z-index: 10;
        white-space: nowrap;
        display: flex;
        align-items: center;
    }
    
    .popular-badge i {
        color: #f59e0b;
    }

    /* Base Body Styling - System Colors (Indigo/Blue) */
    .card-color-body {
        border-radius: 18px;
        padding: 20px 16px;
        color: white;
        background: linear-gradient(135deg, #2563eb, #1f5d96); /* System Blue */
        box-shadow: 0 8px 16px -4px rgba(31, 93, 150, 0.3), inset 0 2px 4px rgba(255, 255, 255, 0.2);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        display: flex;
        flex-direction: column;
        height: 100%;
        position: relative;
        overflow: hidden;
        backface-visibility: hidden;
        -webkit-font-smoothing: antialiased;
        transform: translateZ(0);
    }

    /* Popular plan gets a slightly more vibrant system gradient */
    .popular-plan .card-color-body {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        box-shadow: 0 12px 24px -6px rgba(29, 78, 216, 0.4), inset 0 2px 4px rgba(255, 255, 255, 0.3);
    }
    
    /* Variant for alternating */
    .row > div:nth-child(even) .card-color-body {
        background: linear-gradient(135deg, #1f5d96, #174a7a);
    }

    /* Hover effects */
    .plan-card-premium:hover .card-color-body {
        transform: translateY(-6px);
        box-shadow: 0 15px 30px -10px rgba(0, 0, 0, 0.25), inset 0 2px 4px rgba(255, 255, 255, 0.3);
    }

    .popular-plan .card-color-body {
        transform: scale(1.02);
    }
    .popular-plan:hover .card-color-body {
        transform: translateY(-6px) scale(1.02);
    }

    /* Decorative Shapes inside cards */
    .card-color-body::before {
        content: ''; position: absolute; top: -50%; right: -50%; width: 100%; height: 100%;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
        border-radius: 50%; pointer-events: none;
    }

    .plan-header {
        text-align: center;
        margin-bottom: 16px;
        padding-bottom: 16px;
        border-bottom: 1px dashed rgba(255,255,255,0.2);
    }

    .plan-name {
        font-size: 13px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 10px;
        color: rgba(255,255,255,0.9);
    }

    .plan-price-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
    }

    .price-value {
        font-size: 32px;
        font-weight: 900;
        line-height: 1;
        letter-spacing: -1.5px;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .price-meta {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        text-align: left;
    }
    html[dir="rtl"] .price-meta { text-align: right; align-items: flex-start; }

    .currency { font-size: 12px; font-weight: 700; opacity: 0.9; line-height: 1; }
    .duration { font-size: 10px; font-weight: 600; opacity: 0.7; }

    .plan-features {
        flex: 1;
        margin-bottom: 20px;
    }

    .feature-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .feature-list li {
        display: flex;
        align-items: flex-start;
        gap: 6px;
        font-size: 12px;
        color: rgba(255,255,255,0.9);
        line-height: 1.4;
    }
    
    .feature-list li i {
        font-size: 12px;
        margin-top: 2px;
        color: rgba(255,255,255,0.8);
        background: rgba(255,255,255,0.15);
        padding: 2px;
        border-radius: 50%;
    }

    .plan-actions { margin-top: auto; }

    .btn-edit-plan {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        width: 100%;
        padding: 10px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.15);
        color: white;
        font-size: 14px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        border: 1px solid rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(4px);
    }

    .btn-edit-plan:hover {
        background: white;
        color: #0f172a;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    
    .popular-plan .btn-edit-plan {
        background: white;
        color: #c026d3; /* Will adapt depending on if it's pink theme, but usually pink is middle */
    }
    
    .theme-blue.popular-plan .btn-edit-plan { color: #1f5d96; }
    .theme-pink.popular-plan .btn-edit-plan { color: #f43f5e; }
    .theme-green.popular-plan .btn-edit-plan { color: #10b981; }
    
    .popular-plan .btn-edit-plan:hover {
        background: rgba(255,255,255,0.9);
    }

    .empty-state-glass {
        text-align: center; padding: 60px 30px;
        background: rgba(255, 255, 255, 0.8); border-radius: 20px; border: 2px dashed #cbd5e1;
    }
</style>
@endsection
