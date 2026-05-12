<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'en' ? 'ltr' : 'rtl' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- DNS prefetch for external origins resolved before the browser needs them --}}
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="dns-prefetch" href="//unpkg.com">

    {{-- Preconnect only to the origins we fetch first-paint fonts from --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    {{-- Non-blocking font load: media trick swaps to "all" after stylesheet arrives --}}
    <link rel="preload" as="style"
          href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap"
          onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap">
    </noscript>

    @php
        $systemName = App\Models\Setting::get('system_name', config('app.name', 'ClinicOne'));
        $logoFile   = App\Models\Setting::get('logo', 'favicon.ico');
    @endphp

    <title>{{ $systemName }} | @yield('title', __('clinic.dashboard.title'))</title>

    <!-- PWA & Icons -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#1f5d96">
    <link rel="apple-touch-icon" href="{{ asset('uploads/settings/' . $logoFile) }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('uploads/settings/' . $logoFile) }}">

    <!-- Custom SaaS CSS (No Vite) -->
    <link rel="stylesheet" href="{{ asset('css/saas.css') }}">

    {{-- Phosphor Icons deferred — icons are not needed for initial render --}}
    <script defer src="https://unpkg.com/@phosphor-icons/web"></script>

    @stack('styles')
</head>
<body>

    <div class="app-container">
        <!-- Overlay للموبايل -->
        <div class="sidebar-overlay" onclick="toggleSidebar()"></div>

        <!-- القائمة الجانبية -->
        @include('layouts.partials.sidebar')

        <!-- المحتوى الرئيسي -->
        <div class="main-wrapper">
            
            <!-- الشريط العلوي -->
            @include('layouts.partials.topbar')

            <!-- منطقة المحتوى الديناميكية -->
            <main class="content-area">
                @if(session('success'))
                    <div style="background-color: rgba(16, 185, 129, 0.1); color: var(--success); padding: 16px; border-radius: var(--radius-md); margin-bottom: 24px; display: flex; align-items: center; gap: 8px; border: 1px solid rgba(16, 185, 129, 0.2);">
                        <i class="ph-fill ph-check-circle" style="font-size: 20px;"></i>
                        <span style="font-size: 14px; font-weight: 500;">{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div style="background-color: rgba(239, 68, 68, 0.1); color: var(--danger); padding: 16px; border-radius: var(--radius-md); margin-bottom: 24px; display: flex; align-items: center; gap: 8px; border: 1px solid rgba(239, 68, 68, 0.2);">
                        <i class="ph-fill ph-x-circle" style="font-size: 20px;"></i>
                        <span style="font-size: 14px; font-weight: 500;">{{ session('error') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div style="background-color: rgba(239, 68, 68, 0.1); color: var(--danger); padding: 16px; border-radius: var(--radius-md); margin-bottom: 24px; border: 1px solid rgba(239, 68, 68, 0.2);">
                        <div class="dashboard-grid" style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                            <i class="ph-fill ph-warning-circle" style="font-size: 20px;"></i>
                            <span style="font-size: 14px; font-weight: 700;">{{ app()->getLocale() === 'en' ? 'Please fix the following errors:' : 'يوجد أخطاء في البيانات:' }}</span>
                        </div>
                        <ul style="margin: 0; padding-right: 28px; font-size: 13px;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                @yield('content')
            </main>
            
        </div>
    </div>

    <!-- Notification Sound -->
    <audio id="notificationSound" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" preload="auto"></audio>

    <!-- Vanilla JS Logic -->
    <script>
        // ── Notification polling ─────────────────────────────────────────────
        let _lastNotifCount = null;
        const _csrfToken      = '{{ csrf_token() }}';
        const _notifCheckUrl  = '{{ route('clinic.notifications.check') }}';
        const _notifAllUrl    = '{{ route('clinic.notifications.index') }}';
        const _notifReadAll   = '{{ route('clinic.notifications.read-all') }}';
        const _isLoggedIn     = {{ auth()->check() ? 'true' : 'false' }};
        const _locale         = '{{ app()->getLocale() }}';

        function esc(str) {
            const d = document.createElement('div');
            d.appendChild(document.createTextNode(str ?? ''));
            return d.innerHTML;
        }

        /** Return the icon character for a notification type */
        function notifIcon(type) {
            if (type === 'payment')     return '💰';
            if (type === 'appointment') return '📅';
            return '🔔';
        }

        /** Mark a single notification as read without reloading */
        async function markNotifRead(id, el) {
            await fetch(`/clinic/notifications/${id}/read`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': _csrfToken, 'Accept': 'application/json' },
            });
            if (el) el.style.background = '';
        }

        /** Mark all as read */
        async function markAllNotifRead() {
            await fetch(_notifReadAll, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': _csrfToken, 'Accept': 'application/json' },
            });
            // refresh UI
            checkNotifications();
        }

        async function checkNotifications() {
            try {
                const res  = await fetch(_notifCheckUrl);
                const data = await res.json();

                // ── Badge ────────────────────────────────────────────────────
                const badge      = document.getElementById('notificationBadge');
                const countBadge = document.getElementById('notificationCountBadge');

                if (badge) {
                    badge.textContent    = data.count > 9 ? '9+' : data.count;
                    badge.style.display  = data.count > 0 ? 'flex' : 'none';
                }
                if (countBadge) {
                    countBadge.textContent = data.count;
                }

                // ── Dropdown list ────────────────────────────────────────────
                const list = document.getElementById('notificationList');
                if (!list) return;

                // data.notifications is the array returned by NotificationController::check()
                const notifs = data.notifications ?? [];

                if (notifs.length > 0) {
                    list.innerHTML = notifs.map(n => `
                        <div id="notif-row-${esc(n.id)}"
                             onclick="markNotifRead(${n.id}, this)"
                             style="
                                display: block;
                                padding: 12px 16px;
                                border-bottom: 1px solid var(--border-color);
                                cursor: pointer;
                                background: ${n.is_read ? '' : 'rgba(59,130,246,.06)'};
                                transition: background .2s;
                             "
                        >
                            <div style="display:flex;align-items:flex-start;gap:10px;">
                                <span style="font-size:18px;flex-shrink:0;margin-top:1px;">${notifIcon(n.type)}</span>
                                <div style="flex:1;min-width:0;">
                                    <div style="display:flex;justify-content:space-between;align-items:center;gap:6px;">
                                        <span style="font-weight:${n.is_read ? '500' : '700'};font-size:12px;color:var(--text-primary);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                            ${esc(n.title)}
                                        </span>
                                        ${!n.is_read ? '<span style="width:7px;height:7px;border-radius:50%;background:var(--primary);flex-shrink:0;"></span>' : ''}
                                    </div>
                                    <div style="font-size:11px;color:var(--text-muted);margin-top:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                        ${esc(n.message ?? '')}
                                    </div>
                                    <div style="font-size:10px;color:var(--text-muted);margin-top:4px;">${esc(n.created_at)}</div>
                                </div>
                            </div>
                        </div>
                    `).join('');
                } else {
                    list.innerHTML = `
                        <div style="padding:30px;text-align:center;color:var(--text-muted);font-size:13px;">
                            <i class="ph ph-bell-slash" style="font-size:28px;display:block;margin-bottom:8px;opacity:.4;"></i>
                            ${_locale === 'ar' ? 'لا توجد إشعارات' : 'No notifications yet'}
                        </div>
                    `;
                }

                // ── Sound on new notification ────────────────────────────────
                if (_lastNotifCount !== null && data.count > _lastNotifCount) {
                    const sound = document.getElementById('notificationSound');
                    sound?.play().catch(() => {});
                }

                _lastNotifCount = data.count;

            } catch (e) {
                console.error('Notification check failed:', e);
            }
        }

        // Initial poll + interval
        if (_isLoggedIn) {
            checkNotifications();
            setInterval(checkNotifications, 30000);
        }

        // إظهار/إخفاء القوائم المنسدلة
        function toggleDropdown(id, event) {
            if (event) event.stopPropagation();
            const dropdown = document.getElementById(id);
            
            // إغلاق أي قائمة أخرى مفتوحة
            document.querySelectorAll('.dropdown-menu.active').forEach(menu => {
                if (menu.id !== id) menu.classList.remove('active');
            });

            if (dropdown) {
                dropdown.classList.toggle('active');
            }
        }

        // إظهار/إخفاء قائمة المستخدم
        function toggleUserDropdown(event) {
            if (event) event.stopPropagation();
            const profileTrigger = document.getElementById('profileTrigger');
            const userDropdown = document.getElementById('userDropdown');
            
            if (userDropdown) {
                userDropdown.classList.toggle('active');
                if (profileTrigger) {
                    profileTrigger.classList.toggle('open');
                }
            }
            
            // إغلاق أي قائمة أخرى مفتوحة
            document.querySelectorAll('.dropdown-menu.active').forEach(menu => {
                if (menu.id !== 'userDropdown') {
                    menu.classList.remove('active');
                }
            });
        }

        // إظهار/إخفاء القائمة الجانبية في الشاشات الصغيرة
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            
            if (sidebar) {
                sidebar.classList.toggle('active');
            }
            
            if (overlay) {
                overlay.classList.toggle('visible');
                setTimeout(() => overlay.classList.toggle('active'), 10);
            }
        }

        // إغلاق القوائم عند النقر خارجها
        window.onclick = function(event) {
            // إغلاق جميع الـ Dropdowns المفتوحة
            if (!event.target.closest('.dropdown-trigger') && !event.target.closest('.dropdown-menu')) {
                document.querySelectorAll('.dropdown-menu.active').forEach(menu => {
                    menu.classList.remove('active');
                });
                // إغلاق بروفايل المستخدم بشكل خاص إذا لزم الأمر
                const profileTrigger = document.getElementById('profileTrigger');
                const userDropdown = document.getElementById('userDropdown');
                if (userDropdown && userDropdown.classList.contains('active')) {
                    userDropdown.classList.remove('active');
                    profileTrigger.classList.remove('open');
                }
            }
            
            // إغلاق القائمة الجانبية في الموبايل عند النقر خارجها
            if (window.innerWidth <= 992) {
                if (!event.target.closest('.sidebar') && !event.target.closest('.mobile-toggle')) {
                    const sidebar = document.querySelector('.sidebar');
                    const overlay = document.querySelector('.sidebar-overlay');
                    if(sidebar && sidebar.classList.contains('active')) {
                        sidebar.classList.remove('active');
                        if (overlay) {
                            overlay.classList.remove('active');
                            setTimeout(() => overlay.classList.remove('visible'), 250);
                        }
                    }
                }
            }
        }
    </script>
    
    @stack('scripts')
</body>
</html>
