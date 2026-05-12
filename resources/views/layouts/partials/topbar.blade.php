<header class="topbar">
    <div class="topbar-start">
        <button class="mobile-toggle" onclick="toggleSidebar()">
            <i class="ph-bold ph-list"></i>
        </button>
        <div class="breadcrumb">
            <a href="{{ route('dashboard') }}">{{ __('admin.topbar.home') }}</a>
            <span class="sep">/</span>
            <span class="current">@yield('title', __('admin.sidebar.dashboard'))</span>
        </div>
    </div>

    <div class="topbar-end">
        <!-- الإشعارات -->
        <div class="dropdown-trigger" id="notificationTrigger" onclick="toggleDropdown('notificationDropdown', event)">
            <button class="icon-btn" style="position:relative;">
                <i class="ph-bold ph-bell"></i>
                <span class="badge"
                      id="notificationBadge"
                      style="display:none;position:absolute;top:-4px;inset-inline-end:-4px;min-width:18px;height:18px;font-size:10px;border-radius:9px;background:var(--danger,#ef4444);color:#fff;display:none;align-items:center;justify-content:center;padding:0 4px;font-weight:700;">
                    0
                </span>
            </button>

            <div class="dropdown-menu" id="notificationDropdown"
                 style="width:320px;padding:0;inset-inline-end:0;inset-inline-start:auto;max-height:420px;display:flex;flex-direction:column;">

                {{-- Header --}}
                <div style="padding:12px 16px;border-bottom:1px solid var(--border-color);display:flex;justify-content:space-between;align-items:center;flex-shrink:0;">
                    <span style="font-weight:700;font-size:14px;">
                        {{ app()->getLocale() === 'ar' ? 'الإشعارات' : 'Notifications' }}
                        <span class="badge badge-primary" id="notificationCountBadge" style="margin-inline-start:6px;">0</span>
                    </span>
                    <button onclick="markAllNotifRead()" style="font-size:11px;color:var(--primary);font-weight:600;background:none;border:none;cursor:pointer;padding:0;">
                        {{ app()->getLocale() === 'ar' ? 'قراءة الكل' : 'Mark all read' }}
                    </button>
                </div>

                {{-- Scrollable list — populated by checkNotifications() --}}
                <div id="notificationList" style="flex:1;overflow-y:auto;max-height:320px;">
                    <div style="padding:30px;text-align:center;color:var(--text-muted);font-size:13px;">
                        <i class="ph ph-bell-slash" style="font-size:28px;display:block;margin-bottom:8px;opacity:.4;"></i>
                        {{ app()->getLocale() === 'ar' ? 'لا توجد إشعارات' : 'No notifications yet' }}
                    </div>
                </div>

                {{-- Footer --}}
                <div style="padding:10px 16px;text-align:center;border-top:1px solid var(--border-color);flex-shrink:0;">
                    <a href="{{ route('clinic.notifications.index') }}"
                       style="font-size:12px;color:var(--primary);font-weight:600;text-decoration:none;">
                        {{ __('admin.common.view_all') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- مبدل اللغة -->
        <div class="dropdown-trigger" id="langTrigger" onclick="toggleDropdown('langDropdown', event)">
            <button class="icon-btn" title="{{ __('admin.topbar.language') }}">
                <i class="ph-bold ph-translate"></i>
                <span style="font-size: 10px; font-weight: 800; position: absolute; bottom: -2px; background: var(--clr-primary-600); color: white; padding: 1px 4px; border-radius: 4px; line-height: 1;">
                    {{ app()->getLocale() === 'ar' ? 'AR' : 'EN' }}
                </span>
            </button>
            
            <div class="dropdown-menu" id="langDropdown" style="width: 140px; inset-inline-end: 0; inset-inline-start: auto;">
                <a href="{{ route('locale.switch', 'ar') }}" class="dropdown-item {{ app()->getLocale() === 'ar' ? 'active' : '' }}" style="{{ app()->getLocale() === 'ar' ? 'background: var(--clr-primary-50); color: var(--clr-primary-600); font-weight: 700;' : '' }}">
                    <span style="flex: 1;">العربية</span>
                    @if(app()->getLocale() === 'ar') <i class="ph-bold ph-check" style="font-size: 14px;"></i> @endif
                </a>
                <a href="{{ route('locale.switch', 'en') }}" class="dropdown-item {{ app()->getLocale() === 'en' ? 'active' : '' }}" style="{{ app()->getLocale() === 'en' ? 'background: var(--clr-primary-50); color: var(--clr-primary-600); font-weight: 700;' : '' }}">
                    <span style="flex: 1;">English</span>
                    @if(app()->getLocale() === 'en') <i class="ph-bold ph-check" style="font-size: 14px;"></i> @endif
                </a>
            </div>
        </div>

        <div class="topbar-sep"></div>

        <!-- حساب المستخدم -->
        <div class="user-profile dropdown-trigger" id="profileTrigger" onclick="toggleUserDropdown(event)">
            <div class="user-avatar-initials">
                {{ mb_substr(auth()->user()->name ?? 'U', 0, 1) }}
            </div>
            <div class="user-info d-none d-md-flex">
                <span class="user-name">{{ auth()->user()->name ?? 'User' }}</span>
                <span class="user-role">{{ (auth()->user() && auth()->user()->role === 'admin') ? __('admin.sidebar.admin_panel') : __('admin.sidebar.clinic_panel') }}</span>
            </div>
            <i class="ph-bold ph-caret-down user-chevron"></i>
            
            <div class="dropdown-menu" id="userDropdown" onclick="event.stopPropagation()">
                <a href="{{ route('profile.edit') }}" class="dropdown-item">
                    <i class="ph-bold ph-user-circle"></i>
                    {{ __('admin.topbar.profile') }}
                </a>
                <a href="{{ route('settings') }}" class="dropdown-item">
                    <i class="ph-bold ph-gear-six"></i>
                    {{ __('admin.topbar.settings') }}
                </a>
                <div class="dropdown-divider"></div>
                <form action="{{ route('logout') }}" method="POST" id="logout-form">
                    @csrf
                    <div class="dropdown-item danger" onclick="document.getElementById('logout-form').submit()">
                        <i class="ph-bold ph-sign-out"></i>
                        {{ __('admin.topbar.logout') }}
                    </div>
                </form>
            </div>
        </div>
    </div>
</header>
