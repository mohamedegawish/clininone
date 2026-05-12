@extends('layouts.app')

@section('title', __('admin.clinics.title'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--clr-primary-800);">{{ __('admin.clinics.registered_clinics') }}</h4>
        <p class="text-muted mb-0">{{ __('admin.clinics.subtitle', ['count' => $clinics->total()]) }}</p>
    </div>
    <a href="{{ route('admin.clinics.create') }}" class="btn btn-primary px-4 py-2 d-flex align-items-center gap-2 shadow-sm transition-all hover-lift">
        <i class="ph-bold ph-plus-circle fs-5"></i>
        <span>{{ __('admin.clinics.add_new') }}</span>
    </a>
</div>

<div class="row g-48">
    @forelse($clinics as $clinic)
    <div class="col-xl-4 col-lg-6 mb-5">
        <div class="clinic-card-premium">
            <div class="card-glass-body">
                <!-- Status Badge -->
                <div class="status-badge-wrapper">
                    <span class="badge-status-pill {{ $clinic->status === 'active' ? 'active' : 'inactive' }}">
                        <span class="dot"></span>
                        {{ $clinic->status === 'active' ? __('admin.clinics.status_active') : __('admin.clinics.status_inactive') }}
                    </span>
                </div>

                <!-- Header Section -->
                <div class="card-header-main">
                    <div class="logo-box-wrapper">
                        <div class="logo-box">
                            {{ mb_substr($clinic->name, 0, 1) }}
                        </div>
                    </div>
                    <div class="clinic-title-area">
                        <h5 class="clinic-name">
                            <a href="{{ route('admin.clinics.show', $clinic) }}">{{ $clinic->name }}</a>
                        </h5>
                        <p class="clinic-loc">
                            <i class="ph-bold ph-map-pin"></i>
                            {{ $clinic->address ?: __('admin.clinics.address_not_set') }}
                        </p>
                    </div>
                </div>

                <!-- Stats Layer -->
                <div class="stats-layer">
                    <div class="stat-item">
                        <div class="v">{{ $clinic->doctors_count }}</div>
                        <div class="l">{{ __('admin.clinics.doctors') }}</div>
                    </div>
                    <div class="stat-item">
                        <div class="v">{{ $clinic->patients_count }}</div>
                        <div class="l">{{ __('admin.clinics.patients') }}</div>
                    </div>
                    <div class="stat-item">
                        <div class="v">{{ $clinic->appointments_count }}</div>
                        <div class="l">{{ __('admin.clinics.appointments') }}</div>
                    </div>
                </div>

                <!-- Footer Section -->
                <div class="card-footer-main">
                    <div class="sub-pill-glass">
                        <div class="icon"><i class="ph-fill ph-crown"></i></div>
                        <div class="info">
                            @if($clinic->activeSubscription)
                                <div class="p-name">{{ $clinic->activeSubscription->plan->name }}</div>
                                <div class="p-date">{{ __('admin.clinics.ends_at', ['date' => $clinic->activeSubscription->end_at->format('d/m/Y')]) }}</div>
                            @else
                                <div class="p-none">{{ __('admin.clinics.no_active_subscription') }}</div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="action-group">
                        <a href="{{ route('admin.clinics.show', $clinic) }}" class="btn-main-view" title="{{ __('admin.clinics.view_details') }}">
                            <i class="ph-bold ph-eye"></i>
                        </a>
                        <div class="dropdown-container">
                            <button class="btn-action-trigger dropdown-trigger" onclick="toggleDropdown('drop-{{ $clinic->id }}', event)">
                                <i class="ph-bold ph-dots-three-vertical"></i>
                            </button>
                            <div class="dropdown-menu-custom" id="drop-{{ $clinic->id }}">
                                <a href="{{ route('admin.clinics.edit', $clinic) }}" class="drop-link">
                                    <i class="ph-bold ph-pencil-simple"></i>
                                    <span>{{ __('admin.clinics.edit') }}</span>
                                </a>
                                <a href="{{ route('admin.clinics.assign-plan', $clinic) }}" class="drop-link">
                                    <i class="ph-bold ph-credit-card"></i>
                                    <span>{{ __('admin.clinics.change_plan') }}</span>
                                </a>
                                <form action="{{ route('admin.clinics.toggle-status', $clinic) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="drop-link w-100 text-start border-0 bg-transparent">
                                        <i class="ph-bold {{ $clinic->status === 'active' ? 'ph-pause-circle text-warning' : 'ph-play-circle text-success' }}"></i>
                                        <span>{{ $clinic->status === 'active' ? __('admin.clinics.stop') : __('admin.clinics.activate') }}</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="empty-state-glass">
            <i class="ph ph-hospital"></i>
            <h5>{{ __('admin.clinics.no_clinics_found') }}</h5>
        </div>
    </div>
    @endforelse
</div>

<div class="mt-5 d-flex justify-content-center">
    {{ $clinics->links() }}
</div>

@endsection

@push('scripts')
<script>
    function toggleDropdown(id, event) {
        event.stopPropagation();
        const menu = document.getElementById(id);
        if (!menu) return;
        
        document.querySelectorAll('.dropdown-menu-custom').forEach(m => {
            if (m.id !== id) m.classList.remove('active');
        });
        menu.classList.toggle('active');
    }
    document.addEventListener('click', () => {
        document.querySelectorAll('.dropdown-menu-custom').forEach(m => m.classList.remove('active'));
    });
</script>
@endpush
