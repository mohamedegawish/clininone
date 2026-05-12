@extends('layouts.app')

@section('title', __('admin.doctors.title'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('admin.doctors.title') }}</h1>
        <p class="page-subtitle">{{ __('admin.doctors.subtitle', ['count' => $doctors->total()]) }}</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.doctors.create') }}" class="btn btn-primary">
            <i class="ph-bold ph-user-plus"></i>
            <span>{{ __('admin.doctors.add_new') }}</span>
        </a>
    </div>
</div>

<div class="row g-48">
    @forelse($doctors as $doctor)
    <div class="col-xl-4 col-lg-6 mb-5">
        <div class="doctor-card-square">
            <div class="card-glass-body">
                <!-- Status Badge -->
                <div class="status-badge-wrapper">
                    <span class="badge-status-pill {{ $doctor->status === 'active' ? 'active' : 'inactive' }}">
                        <span class="dot"></span>
                        {{ $doctor->status === 'active' ? __('admin.doctors.status_active') : __('admin.doctors.status_inactive') }}
                    </span>
                </div>

                <!-- Header Section -->
                <div class="card-header-main">
                    <div class="logo-box-wrapper">
                        @if($doctor->photo_path)
                            <img src="{{ asset('storage/' . $doctor->photo_path) }}" class="logo-box object-cover" alt="{{ $doctor->name }}">
                        @else
                            <div class="logo-box">
                                {{ mb_substr($doctor->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <div class="doctor-title-area">
                        <h5 class="doctor-name">
                            <span>{{ $doctor->name }}</span>
                        </h5>
                        <p class="doctor-specialty">
                            <i class="ph-bold ph-stethoscope"></i>
                            {{ $doctor->specialty }}
                        </p>
                    </div>
                </div>

                <!-- Info Layer -->
                <div class="info-grid">
                    <div class="info-box">
                        <span class="label">{{ __('admin.doctors.clinic') }}</span>
                        <span class="value">{{ $doctor->clinics->first()?->name ?? __('admin.doctors.no_clinic') }}</span>
                    </div>
                    <div class="info-box">
                        <span class="label">{{ __('admin.doctors.price') }}</span>
                        <span class="value">{{ number_format($doctor->price) }} {{ __('admin.common.currency') }}</span>
                    </div>
                    <div class="info-box">
                        <span class="label">{{ __('admin.doctors.experience') }}</span>
                        <span class="value">{{ $doctor->experience_years ?? 0 }} {{ __('admin.doctors.years') }}</span>
                    </div>
                </div>

                <!-- Footer Section -->
                <div class="card-footer-main">
                    <div class="contact-info">
                        <div class="contact-item">
                            <i class="ph ph-envelope"></i>
                            <span>{{ $doctor->email }}</span>
                        </div>
                        <div class="contact-item">
                            <i class="ph ph-phone"></i>
                            <span>{{ $doctor->phone }}</span>
                        </div>
                    </div>
                    
                    <div class="action-group">
                        <a href="{{ route('admin.doctors.schedule.edit', $doctor) }}" class="btn-icon-action" title="{{ __('admin.doctors.schedule') }}">
                            <i class="ph-bold ph-calendar"></i>
                        </a>
                        <div class="dropdown-container">
                            <button class="btn-icon-action dropdown-trigger" onclick="toggleDropdown('drop-doc-{{ $doctor->id }}', event)">
                                <i class="ph-bold ph-dots-three-vertical"></i>
                            </button>
                            <div class="dropdown-menu-custom" id="drop-doc-{{ $doctor->id }}">
                                <form action="{{ route('admin.doctors.destroy', $doctor) }}" method="POST" onsubmit="return confirm('{{ __('admin.doctors.confirm_delete') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="drop-link text-danger">
                                        <i class="ph-bold ph-trash"></i>
                                        <span>{{ __('admin.doctors.delete') }}</span>
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
            <i class="ph ph-users"></i>
            <h5>{{ __('admin.doctors.no_doctors_found') }}</h5>
        </div>
    </div>
    @endforelse
</div>

<div class="mt-4">
    {{ $doctors->links() }}
</div>

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
@endsection
