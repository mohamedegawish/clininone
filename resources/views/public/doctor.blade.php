<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ App\Models\Setting::get('system_name', 'ClinicOne') }} — {{ __('public.view_profile') }}</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter+Tight:wght@700;800;900&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="{{ asset('landing/css/main.css') }}">
  <link rel="stylesheet" href="{{ asset('landing/css/doctor.css') }}">

  <style>
    .animate-fade-in-up { animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
  </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
  <div class="navbar-brand" onclick="window.location.href='{{ route('public.index') }}'">
    <div class="navbar-logo">
      <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
    </div>
    <div><div class="navbar-title">{{ App\Models\Setting::get('system_name', 'ClinicOne') }}</div><div class="navbar-subtitle">SMART MEDICAL</div></div>
  </div>
  <div class="navbar-nav">
    <a class="nav-link" href="{{ route('public.index') }}">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5m7-7-7 7 7 7"/></svg>
      {{ __('public.back_to_doctors') }}
    </a>
    <a class="nav-link lang-switch" href="{{ route('locale.switch', app()->getLocale() === 'ar' ? 'en' : 'ar') }}" style="background: rgba(255,255,255,0.1); padding: 4px 10px; border-radius: 50px; font-weight: 800; font-size: 12px; margin-inline-start: 10px;">
      {{ app()->getLocale() === 'ar' ? 'English' : 'العربية' }}
    </a>
  </div>
</nav>

<div class="profile-page">
  <div class="container-sm animate-fade-in-up" id="profile-container">
    <div class="card" style="padding:40px;text-align:center;border:1px solid rgba(255,255,255,0.1)">
      <div class="avatar avatar-xl" style="margin:0 auto 20px;background:rgba(255,255,255,0.05);animation:pulse 1.5s infinite"></div>
      <div style="height:24px;width:200px;background:rgba(255,255,255,0.05);margin:0 auto 10px;border-radius:10px;animation:pulse 1.5s infinite"></div>
      <div style="height:16px;width:140px;background:rgba(255,255,255,0.05);margin:0 auto 30px;border-radius:10px;animation:pulse 1.5s infinite"></div>
      <div style="display:flex;gap:10px;justify-content:center">
        <div style="height:40px;width:120px;background:rgba(255,255,255,0.05);border-radius:10px;animation:pulse 1.5s infinite"></div>
        <div style="height:40px;width:120px;background:rgba(255,255,255,0.05);border-radius:10px;animation:pulse 1.5s infinite"></div>
      </div>
    </div>
  </div>
</div>

<footer class="footer">
  <div class="container footer-inner">
    <div class="navbar-brand">
      <div class="navbar-logo" style="overflow: hidden; padding: 0; background: none">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" style="color:var(--text-muted)">
          <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
        </svg>
      </div>
      <div>
        <div class="navbar-title">{{ App\Models\Setting::get('system_name', 'ClinicOne') }}</div>
        <div class="navbar-subtitle">All Clinics. One System.</div>
      </div>
    </div>
    <p style="color: var(--text-muted); font-size: 12px">
      © {{ date('Y') }} {{ App\Models\Setting::get('system_name', 'ClinicOne') }}. {{ __('public.rights_reserved') }}
    </p>
  </div>
</footer>

<script type="module" src="{{ asset('landing/js/doctor.js') }}"></script>
<script
  src="{{ asset('chatbot/frontend/chat-widget.js') }}"
  data-api-endpoint="/chatbot/backend/chat.php"
  defer
></script>

</body>
</html>
