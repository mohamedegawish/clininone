@if(auth()->user()->role === 'admin')
    <div class="nav-title">{{ __('admin.sidebar.main') }}</div>
    <div class="nav-item">
        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="ph-bold ph-chart-pie-slice"></i>
            <span>{{ __('admin.sidebar.dashboard') }}</span>
        </a>
    </div>

    <div class="nav-title">{{ __('admin.sidebar.management') }}</div>
    <div class="nav-item">
        <a href="{{ route('admin.clinics.index') }}" class="nav-link {{ request()->routeIs('admin.clinics.*') ? 'active' : '' }}">
            <i class="ph-bold ph-hospital"></i>
            <span>{{ __('admin.sidebar.clinics') }}</span>
        </a>
    </div>
    <div class="nav-item">
        <a href="{{ route('admin.doctors.index') }}" class="nav-link {{ request()->routeIs('admin.doctors.*') ? 'active' : '' }}">
            <i class="ph-bold ph-stethoscope"></i>
            <span>{{ __('admin.sidebar.doctors') }}</span>
        </a>
    </div>
    <div class="nav-item">
        <a href="{{ route('admin.patients.index') }}" class="nav-link {{ request()->routeIs('admin.patients.*') ? 'active' : '' }}">
            <i class="ph-bold ph-users"></i>
            <span>{{ __('admin.sidebar.patients') }}</span>
        </a>
    </div>
    <div class="nav-item">
        <a href="{{ route('admin.appointments.index') }}" class="nav-link {{ request()->routeIs('admin.appointments.index') ? 'active' : '' }}">
            <i class="ph-bold ph-calendar-check"></i>
            <span>{{ __('admin.sidebar.appointments') }}</span>
        </a>
    </div>
    <div class="nav-item">
        <a href="{{ route('admin.blood-bank.index') }}" class="nav-link {{ request()->routeIs('admin.blood-bank.*') ? 'active' : '' }}">
            <i class="ph-bold ph-drop" style="color: #dc2626;"></i>
            <span>{{ __('admin.sidebar.blood_bank') }}</span>
        </a>
    </div>

    <div class="nav-title">{{ __('admin.sidebar.system') }}</div>
    <div class="nav-item">
        <a href="{{ route('admin.plans.index') }}" class="nav-link {{ request()->routeIs('admin.plans.*') ? 'active' : '' }}">
            <i class="ph-bold ph-package"></i>
            <span>{{ __('admin.sidebar.plans') }}</span>
        </a>
    </div>
    <div class="nav-item">
        <a href="{{ route('admin.subscriptions.index') }}" class="nav-link {{ request()->routeIs('admin.subscriptions.*') ? 'active' : '' }}">
            <i class="ph-bold ph-credit-card"></i>
            <span>{{ __('admin.sidebar.subscriptions') }}</span>
        </a>
    </div>
    <div class="nav-item">
        <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.index') ? 'active' : '' }}">
            <i class="ph-bold ph-file-text"></i>
            <span>{{ __('admin.sidebar.reports') }}</span>
        </a>
    </div>
    <div class="nav-item">
        <a href="{{ route('settings') }}" class="nav-link {{ request()->routeIs('settings') ? 'active' : '' }}">
            <i class="ph-bold ph-gear"></i>
            <span>{{ __('admin.sidebar.settings') }}</span>
        </a>
    </div>

@else
    <div class="nav-title">{{ __('clinic.sidebar.dashboard') }}</div>
    <div class="nav-item">
        <a href="{{ route('clinic.dashboard') }}" class="nav-link {{ request()->routeIs('clinic.dashboard') ? 'active' : '' }}">
            <i class="ph-bold ph-house"></i>
            <span>{{ __('clinic.sidebar.dashboard') }}</span>
        </a>
    </div>
    <div class="nav-item">
        <a href="{{ route('clinic.patients.index') }}" class="nav-link {{ request()->routeIs('clinic.patients.*') ? 'active' : '' }}">
            <i class="ph-bold ph-users-three"></i>
            <span>{{ __('clinic.sidebar.patients') }}</span>
        </a>
    </div>
    <div class="nav-item">
        <a href="{{ route('clinic.appointments.index') }}" class="nav-link {{ request()->routeIs('clinic.appointments.index') ? 'active' : '' }}">
            <i class="ph-bold ph-calendar-blank"></i>
            <span>{{ __('clinic.sidebar.appointments') }}</span>
        </a>
    </div>
    <div class="nav-item">
        <a href="{{ route('clinic.queue.show') }}" class="nav-link {{ request()->routeIs('clinic.queue.show') ? 'active' : '' }}">
            <i class="ph-bold ph-users-four"></i>
            <span>{{ __('clinic.sidebar.queue') }}</span>
            <span class="nav-badge">Live</span>
        </a>
    </div>

    <div class="nav-title">{{ __('clinic.sidebar.expenses') }} & {{ __('clinic.sidebar.reports') }}</div>
    <div class="nav-item">
        <a href="{{ route('clinic.expenses.index') }}" class="nav-link {{ request()->routeIs('clinic.expenses.*') ? 'active' : '' }}">
            <i class="ph-bold ph-wallet"></i>
            <span>{{ __('clinic.sidebar.expenses') }}</span>
        </a>
    </div>
    <div class="nav-item">
        <a href="{{ route('clinic.reports.index') }}" class="nav-link {{ request()->routeIs('clinic.reports.*') ? 'active' : '' }}">
            <i class="ph-bold ph-chart-bar"></i>
            <span>{{ __('clinic.sidebar.reports') }}</span>
        </a>
    </div>

    <div class="nav-title">{{ __('clinic.sidebar.settings') }}</div>
    <div class="nav-item">
        <a href="{{ route('clinic.schedule.index') }}" class="nav-link {{ request()->routeIs('clinic.schedule.*') ? 'active' : '' }}">
            <i class="ph-bold ph-clock"></i>
            <span>{{ __('clinic.sidebar.schedule') }}</span>
        </a>
    </div>
    <div class="nav-item">
        <a href="{{ route('clinic.settings.index') }}" class="nav-link {{ request()->routeIs('clinic.settings.*') ? 'active' : '' }}">
            <i class="ph-bold ph-gear"></i>
            <span>{{ __('clinic.sidebar.settings') }}</span>
        </a>
    </div>
@endif
