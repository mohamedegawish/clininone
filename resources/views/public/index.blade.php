<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ App\Models\Setting::get('system_name', 'ClinicOne') }} — {{ __('public.home') }}</title>
  <meta name="description" content="{{ __('public.all_clinics') }}">

  <!-- PWA & Icons -->
  <link rel="manifest" href="{{ asset('manifest.json') }}">
  <meta name="theme-color" content="#1f5d96">
  <link rel="apple-touch-icon" href="{{ asset('uploads/settings/' . App\Models\Setting::get('public_logo', 'favicon.ico')) }}">
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('uploads/settings/' . App\Models\Setting::get('public_logo', 'favicon.ico')) }}">

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter+Tight:wght@700;800;900&display=swap" rel="stylesheet">

  <!-- CSS -->
  <link rel="stylesheet" href="{{ asset('landing/css/main.css') }}">
  <link rel="stylesheet" href="{{ asset('landing/css/home.css') }}">

  <style>
    .animate-fade-in-up { animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }

    .mobile-drawer { display: none; }
    .mobile-drawer.open { display: flex !important; }
    .mobile-overlay { display: none; }
    .mobile-overlay.open { display: block !important; }

    @if($bg = App\Models\Setting::get('landing_bg'))
    .hero {
      min-height: 100vh;
      background-image: linear-gradient(rgba(10, 30, 60, 0.45), rgba(10, 30, 60, 0.6)), url('{{ asset("uploads/settings/" . $bg) }}') !important;
      background-size: cover;
      background-position: center;
    }
    @endif
  </style>
</head>
<body>

<div class="gradient-bar" style="border-radius: 0; height: 5px"></div>
<!-- NAVBAR -->
<nav class="navbar home-navbar">
  <a class="navbar-brand" href="{{ route('public.index') }}">
    <div class="navbar-logo" style="overflow: hidden; padding: 0; background: none">
      @if($logo = App\Models\Setting::get('public_logo'))
        <img src="{{ asset('uploads/settings/' . $logo) }}" alt="Logo" style="width: 100%; height: 100%; object-fit: contain;">
      @else
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
          <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
        </svg>
      @endif
    </div>
    <div>
      <div class="navbar-title">{{ __('public.system_name') }}</div>
      <div class="navbar-subtitle">SMART MEDICAL</div>
    </div>
  </a>

  <!-- Desktop nav -->
  <div class="navbar-nav desktop-nav">
    <a class="nav-link active" href="{{ route('public.index') }}">{{ __('public.home') }}</a>
    <a class="nav-link" href="{{ route('public.blood-bank') }}" style="color: #dc2626; font-weight: 800;">🩸 {{ __('public.blood_bank.title') }}</a>
    <a class="nav-link portal-link" href="{{ route('login') }}">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
        <circle cx="12" cy="7" r="4" />
      </svg>
      {{ __('public.portal') }}
    </a>
    
    <!-- Language Switcher -->
    <a class="nav-link lang-switch" href="{{ route('locale.switch', app()->getLocale() === 'ar' ? 'en' : 'ar') }}" style="background: rgba(255,255,255,0.1); padding: 6px 12px; border-radius: 50px; font-weight: 800; font-size: 13px;">
      {{ app()->getLocale() === 'ar' ? 'English' : 'العربية' }}
    </a>
  </div>

  <!-- Mobile right actions -->
  <div class="mobile-actions">
    <button class="hamburger-btn" id="mobile-menu-btn" aria-label="القائمة">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <line x1="3" y1="6" x2="21" y2="6" />
        <line x1="3" y1="12" x2="21" y2="12" />
        <line x1="3" y1="18" x2="21" y2="18" />
      </svg>
    </button>
  </div>
</nav>

<!-- Mobile Drawer Overlay -->
<div class="mobile-overlay" id="mobile-overlay"></div>
<div class="mobile-drawer" id="mobile-drawer">
  <!-- Drawer Header -->
  <div class="drawer-header">
    <div class="navbar-brand">
      <div class="navbar-logo" style="overflow: hidden; padding: 0; background: none">
        @if($logo = App\Models\Setting::get('public_logo'))
          <img src="{{ asset('uploads/settings/' . $logo) }}" alt="Logo" style="width: 100%; height: 100%; object-fit: contain;">
        @else
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
          </svg>
        @endif
      </div>
      <div>
        <div class="navbar-title" style="color:white">{{ __('public.system_name') }}</div>
        <div class="navbar-subtitle" style="color:white">SMART MEDICAL</div>
      </div>
    </div>
    <button class="hamburger-btn" id="mobile-menu-close">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <line x1="18" y1="6" x2="6" y2="18" />
        <line x1="6" y1="6" x2="18" y2="18" />
      </svg>
    </button>
  </div>

  <!-- Drawer Links -->
  <div class="drawer-nav">
    <a class="drawer-link active" href="{{ route('public.index') }}">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
        <polyline points="9 22 9 12 15 12 15 22" />
      </svg>
      {{ __('public.home') }}
    </a>
    <a class="drawer-nav-link" href="{{ route('public.blood-bank') }}" style="color: #dc2626;">
      <i class="ph-bold ph-drop"></i>
      <span>{{ __('public.blood_bank.title') }}</span>
    </a>
    <a class="drawer-link" href="{{ route('login') }}">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
        <circle cx="12" cy="7" r="4" />
      </svg>
      {{ __('public.portal') }}
    </a>
    <a class="drawer-link" href="{{ route('locale.switch', app()->getLocale() === 'ar' ? 'en' : 'ar') }}" style="border-top: 1px solid rgba(255,255,255,0.1); margin-top: 10px; padding-top: 15px;">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="10" />
        <line x1="2" y1="12" x2="22" y2="12" />
        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
      </svg>
      {{ app()->getLocale() === 'ar' ? 'English' : 'العربية' }}
    </a>
  </div>
</div>

<!-- HERO -->
<section class="hero">
  <div class="hero-bg"></div>

  <div class="container hero-content animate-fade-in-up" style="position: relative; z-index: 1">
    <div class="hero-eyebrow">
      {{ __('public.trusted_by') }}
    </div>

    <h1 class="hero-headline">
      <span class="hero-line" style="animation-delay: 0.1s">{{ __('public.system_name') }}</span><br />
      <span class="hero-line" style="animation-delay: 0.4s">
        <span class="hero-gradient">{{ __('public.elite_doctors') }}</span>
      </span>
    </h1>

    <p class="hero-subtitle" style="animation-delay: 0.7s">
      {{ __('public.all_clinics') }}
    </p>

    <!-- Search -->
    <div class="search-bar animate-fade-in-up" style="animation-delay: 0.9s">
      <svg class="search-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="11" cy="11" r="8" />
        <path d="m21 21-4.35-4.35" />
      </svg>
      <input type="text" class="search-input" id="search-input" placeholder="{{ __('public.search_placeholder') }}" />
    </div>

    <!-- Location Filters -->
    <div class="filters-row animate-fade-in-up" style="display: flex; gap: 12px; margin: 0 auto 44px; max-width: 580px; flex-wrap: wrap; animation-delay: 1.1s;">
      <div class="search-bar" style="margin: 0; flex: 1; min-width: 140px; padding: 8px 12px; padding-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}: 55px; position: relative;">
          <svg style="position: absolute; {{ app()->getLocale() === 'ar' ? 'right' : 'left' }}: 20px; top: 50%; transform: translateY(-50%); color: rgba(255, 255, 255, 0.5); pointer-events: none" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" /><circle cx="12" cy="10" r="3" /></svg>
          <select id="gov-filter" class="search-input" style="cursor: pointer; appearance: none; -webkit-appearance: none; width: 100%; color: white !important;">
            <option value="all" style="color: black;">{{ __('public.all_governorates') }}</option>
          </select>
      </div>
      <div class="search-bar" style="margin: 0; flex: 1; min-width: 140px; padding: 8px 12px; padding-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}: 55px; position: relative;">
          <svg style="position: absolute; {{ app()->getLocale() === 'ar' ? 'right' : 'left' }}: 20px; top: 50%; transform: translateY(-50%); color: rgba(255, 255, 255, 0.5); pointer-events: none" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" /><polyline points="9 22 9 12 15 12 15 22" /></svg>
          <select id="city-filter" class="search-input" style="cursor: pointer; appearance: none; -webkit-appearance: none; width: 100%; color: white !important;">
            <option value="all" style="color: black;">{{ __('public.all_areas') }}</option>
          </select>
      </div>
    </div>

    <!-- Stats -->
    <div class="hero-stats">
      <div class="hero-stat">
        <span class="hero-stat-num" id="stats-doctors-count">0+</span>
        <span class="hero-stat-label">{{ __('public.specialists') }}</span>
      </div>
      <div class="hero-stat-divider"></div>
      <div class="hero-stat">
        <span class="hero-stat-num">10K+</span>
        <span class="hero-stat-label">{{ __('public.happy_patients') }}</span>
      </div>
      <div class="hero-stat-divider"></div>
      <div class="hero-stat">
        <span class="hero-stat-num">4.9</span>
        <span class="hero-stat-label">{{ __('public.avg_rating') }}</span>
      </div>
    </div>
  </div>
</section>

<!-- SPECIALTIES -->
<section class="section">
  <div class="container">
    <div class="filter-scroll" id="filter-scroll">
      <button class="specialty-btn active" data-spec="all">{{ __('public.all') }}</button>
    </div>

    <div class="results-info">
      <span class="results-count" id="results-count" 
            data-doctor-text="{{ app()->getLocale() === 'en' ? 'Doctors' : 'طبيب' }}"
            data-in-text="{{ app()->getLocale() === 'en' ? 'in' : 'في' }}">
        {{ __('public.loading') }}
      </span>
      <span class="results-filter" id="results-filter"></span>
    </div>

    <div class="doctors-grid" id="doctors-grid"
         data-all-text="{{ __('public.all') }}"
         data-no-doctors="{{ app()->getLocale() === 'en' ? 'No Doctors Found' : 'لا يوجد أطباء' }}"
         data-try-adjust="{{ app()->getLocale() === 'en' ? 'Try adjusting your search or specialty filter' : 'جرب تعديل البحث أو فلتر التخصص' }}"
         data-clear-text="{{ app()->getLocale() === 'en' ? 'Clear Filters' : 'مسح الفلاتر' }}"
         data-years-exp="{{ app()->getLocale() === 'en' ? 'Years Exp.' : 'سنوات خبرة' }}"
         data-consultation="{{ app()->getLocale() === 'en' ? 'Consultation' : 'الاستشارة' }}"
         data-book-now="{{ app()->getLocale() === 'en' ? 'Book Now' : 'احجز الآن' }}"
         data-currency="{{ app()->getLocale() === 'en' ? 'EGP' : 'ج.م' }}">
      @for($i=0; $i<6; $i++)
      <div class="doctor-card" style="padding:22px;border:1px solid rgba(255,255,255,0.25);border-radius:20px;height:240px">
        <div style="width:100%;height:100%;background:rgba(255,255,255,0.05);border-radius:10px;animation:pulse 1.5s infinite"></div>
      </div>
      @endfor
    </div>

    <!-- Blood Bank Promo -->
    <div class="animate-fade-in-up" style="margin-top: 120px !important; animation-delay: 1.5s">
        <div style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); border-radius: 24px; padding: 40px; position: relative; overflow: hidden; border: 1px solid rgba(220, 38, 38, 0.2); box-shadow: 0 24px 64px rgba(0,0,0,0.15);">
           <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: radial-gradient(circle, rgba(220, 38, 38, 0.15) 0%, transparent 70%); border-radius: 50%;"></div>
           <div class="row align-items-center position-relative" style="z-index: 1;">
              <div class="col-lg-8">
                 <div class="d-flex align-items-center gap-3 mb-3">
                    <span style="background: #dc2626; color: white; padding: 4px 12px; border-radius: 50px; font-size: 11px; font-weight: 800;">NEW FEATURE</span>
                    <h2 style="color: white; font-weight: 800; margin: 0; font-size: 28px;">🩸 {{ __('public.blood_bank.title') }}</h2>
                 </div>
                 <p style="color: #94a3b8; font-size: 16px; margin-bottom: 0;">{{ __('public.blood_bank.subtitle') }} — منصة ذكية لربط المتبرعين بالحالات الطارئة في جميع أنحاء مصر.</p>
              </div>
              <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                 <a href="{{ route('public.blood-bank') }}" class="btn btn-danger rounded-pill px-5 py-3 fw-800 shadow-lg" style="background: #dc2626; border: none; font-size: 16px;">
                    {{ __('public.blood_bank.donate_now') }}
                 </a>
              </div>
           </div>
        </div>
    </div>
  </div>
</section>

<footer class="footer">
  <div class="container footer-inner">
    <div class="navbar-brand">
      <div class="navbar-logo" style="overflow: hidden; padding: 0; background: none">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" style="color:var(--text-muted)">
          <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
        </svg>
      </div>
      <div>
        <div class="navbar-title">{{ __('public.system_name') }}</div>
        <div class="navbar-subtitle">All Clinics. One System.</div>
      </div>
    </div>
    <p style="color: var(--text-muted); font-size: 12px">
      © {{ date('Y') }} {{ App\Models\Setting::get('system_name', 'ClinicOne') }}. {{ __('public.rights_reserved') }}
    </p>
  </div>
</footer>

<!-- FLOATING ACTIONS -->
<div class="floating-actions">
    <a href="https://wa.me/201021021036" target="_blank" class="fab-btn fab-whatsapp" title="تواصل عبر واتساب">
        <svg width="32" height="32" viewBox="0 0 448 512" fill="currentColor">
            <path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.1 0-65.6-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-5.5-2.8-23.2-8.5-44.2-27.1-16.4-14.6-27.4-32.7-30.6-38.2-3.2-5.6-.3-8.6 2.5-11.3 2.5-2.5 5.6-6.5 8.3-9.8 2.8-3.3 3.7-5.6 5.6-9.3 1.9-3.7.9-6.9-.5-9.8-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 13.2 5.8 23.5 9.2 31.5 11.8 13.3 4.2 25.4 3.6 35 2.2 10.7-1.5 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/>
        </svg>
    </a>
    <a href="tel:01021021036" class="fab-btn fab-call" title="اتصل بنا">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.81 12.81 0 0 0 .63 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.63A2 2 0 0 1 22 16.92z"/>
        </svg>
    </a>
</div>

<style>
    .floating-actions {
        position: fixed;
        bottom: 30px;
        inset-inline-start: 30px;
        display: flex;
        flex-direction: column;
        gap: 15px;
        z-index: 9999;
    }
    .fab-btn {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        text-decoration: none !important;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .fab-btn:hover {
        transform: scale(1.1) translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.3);
        color: white;
    }
    .fab-whatsapp {
        background: linear-gradient(135deg, #25d366, #128c7e);
    }
    .fab-call {
        background: linear-gradient(135deg, #1f5d96, #16426b);
    }
    @media (max-width: 768px) {
        .floating-actions {
            bottom: 20px;
            inset-inline-start: 20px;
        }
        .fab-btn {
            width: 52px;
            height: 52px;
        }
    }
</style>

<script type="module" src="{{ asset('landing/js/home.js') }}"></script>
<script
  src="{{ asset('chatbot/frontend/chat-widget.js') }}"
  data-api-endpoint="/chatbot/backend/chat.php"
  defer
></script>

</body>
</html>
