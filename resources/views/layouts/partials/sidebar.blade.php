<aside class="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            @if($logo = App\Models\Setting::get('system_logo'))
                <img src="{{ asset('uploads/settings/' . $logo) }}" style="width: 24px; height: 24px; object-fit: contain;">
            @else
                <i class="ph-fill ph-first-aid-kit" style="font-size: 22px;"></i>
            @endif
        </div>
        <div class="sidebar-brand">
            <h2>{{ App\Models\Setting::get('system_name', 'كلينيك وان') }}</h2>
            <p>{{ auth()->user()->role === 'admin' ? __('admin.sidebar.admin_panel') : __('admin.sidebar.clinic_panel') }}</p>
        </div>
    </div>

    <nav class="sidebar-nav">
        @include('layouts.partials.sidebar-links')
    </nav>

    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn">
                <i class="ph-bold ph-sign-out"></i>
                <span>{{ auth()->user()->role === 'admin' ? __('admin.sidebar.logout') : __('clinic.sidebar.logout') }}</span>
            </button>
        </form>
    </div>
</aside>
