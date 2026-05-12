<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>🩸 {{ __('public.blood_bank.title') }} — ClinicOne</title>
  
  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  
  <!-- Icons -->
  <script src="https://unpkg.com/@phosphor-icons/web"></script>

  <!-- System CSS -->
  <link rel="stylesheet" href="{{ asset('css/saas.css') }}">
  <link rel="stylesheet" href="{{ asset('landing/css/main.css') }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <style>
    :root {
      --clr-blood: #dc2626;
      --clr-blood-light: #fef2f2;
      --clr-blood-glow: rgba(220, 38, 38, 0.15);
      --clr-urgent-high: #ef4444;
      --clr-urgent-med: #f59e0b;
      --clr-urgent-low: #10b981;
    }

    /* Fixed Scroll Issue */
    body { background: var(--bg); font-family: 'Cairo', sans-serif; margin: 0; padding: 0; overflow: hidden; height: 100vh; width: 100vw; }
    .app-container { height: 100%; display: flex; width: 100%; overflow: hidden; }
    
    /* Sidebar Overrides to match ClinicOne */
    .sidebar { z-index: 2000 !important; width: 280px; }
    .sidebar-blood-logo { background: var(--clr-blood) !important; box-shadow: 0 4px 14px var(--clr-blood-glow) !important; }

    /* Main Content Scrolling */
    .main-wrapper { flex: 1; display: flex; flex-direction: column; overflow: hidden; position: relative; width: 100%; background: var(--bg); height: 100%; }
    .topbar { z-index: 999; position: sticky; top: 0; background: #fff; border-bottom: 1px solid rgba(0,0,0,0.05); flex-shrink: 0; }
    .content-area { flex: 1; overflow-y: scroll !important; padding: 24px; -webkit-overflow-scrolling: touch; height: 100%; overscroll-behavior-y: contain; }

    /* Smaller, Organized Cards */
    .blood-card {
        background: #fff;
        border-radius: 16px;
        padding: 16px;
        border: 1px solid rgba(0,0,0,0.06);
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        transition: all 0.3s ease;
        position: relative;
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    .blood-card:hover { transform: translateY(-4px); box-shadow: 0 8px 20px rgba(0,0,0,0.06); }

    /* Grid Layout */
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        grid-auto-rows: 1fr;
        gap: 20px;
    }

    .stat-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    @media (max-width: 992px) {
        .sidebar {
            position: fixed;
            inset-inline-start: 0;
            width: 280px !important;
            transform: translateX({{ app()->getLocale() === 'ar' ? '100%' : '-100%' }});
            transition: transform 0.3s ease-in-out;
            height: 100vh;
            display: flex !important;
        }
        .sidebar.active { transform: translateX(0); }
        .stat-row { grid-template-columns: 1fr; }
        .dashboard-grid { grid-template-columns: 1fr; }
        .content-area { padding: 16px; }
        .main-wrapper { width: 100%; margin-inline-start: 0 !important; }
        .mobile-toggle { display: flex !important; }
        .sidebar-nav span { display: inline-block !important; }
        .mobile-brand { display: flex !important; }
    }

    .mobile-brand {
        display: none;
        align-items: center;
        gap: 10px;
        margin-inline-start: 10px;
    }
    .mobile-brand .logo-circle {
        width: 32px;
        height: 32px;
        background: var(--clr-blood);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 16px;
    }
    .mobile-brand h2 {
        font-size: 14px;
        font-weight: 800;
        margin: 0;
        color: #1e293b;
    }

    .mobile-toggle {
        display: none;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: #fff;
        border: 1px solid rgba(0,0,0,0.08);
        color: #000;
        cursor: pointer;
        font-size: 22px;
        position: relative;
        z-index: 1002 !important;
        margin-inline-end: 12px;
    }

    /* Urgency Badges */
    .urgency-badge {
        padding: 2px 10px;
        border-radius: 50px;
        font-size: 10px;
        font-weight: 800;
    }
    .urgency-high { background: #fef2f2; color: #ef4444; }
    .urgency-medium { background: #fffbeb; color: #d97706; }
    .urgency-low { background: #f0fdf4; color: #16a34a; }

    /* Timer */
    .timer-box { display: flex; align-items: center; gap: 4px; font-size: 11px; font-weight: 700; color: #64748b; }
    .timer-box i { color: var(--clr-blood); }

    /* Buttons */
    .btn-blood {
        background: var(--clr-blood); color: white; border: none;
        padding: 8px 16px; border-radius: 10px; font-weight: 700;
        cursor: pointer; transition: 0.2s; display: flex; align-items: center;
        gap: 8px; justify-content: center; font-size: 13px; text-decoration: none;
    }
    .btn-blood:hover { background: #991b1b; transform: scale(1.02); }
    
    .btn-blood-outline {
        border: 2px solid var(--clr-blood); color: var(--clr-blood);
        background: transparent; padding: 7px 16px; border-radius: 10px;
        font-weight: 700; cursor: pointer; transition: 0.2s; font-size: 13px; text-decoration: none;
    }
    .btn-blood-outline:hover { background: var(--clr-blood); color: white; }

    /* Stats Card (Smaller) */
    .stat-pill {
        padding: 12px 16px;
        border-radius: 14px;
        background: #fff;
        border: 1px solid rgba(0,0,0,0.04);
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .stat-pill i { font-size: 20px; }
    .stat-pill h4 { margin: 0; font-size: 18px; font-weight: 800; color: #000; }
    .stat-pill p { margin: 0; font-size: 11px; color: #000; font-weight: 800; }

    /* Donor Card Styling */
    .donor-name { font-size: 16px; font-weight: 800; color: #1e293b; margin-bottom: 4px; }
    .blood-type-badge {
        width: 38px; height: 38px; border-radius: 10px;
        background: var(--clr-blood); color: white;
        display: flex; align-items: center; justify-content: center;
        font-weight: 900; font-size: 14px;
    }

    .nav-item a.nav-link i { color: #64748b; font-size: 18px; width: 20px; }
    .nav-item a.nav-link.active { background: rgba(220, 38, 38, 0.05); color: var(--clr-blood) !important; border-inline-start: 3px solid var(--clr-blood); }
    .nav-item a.nav-link.active i { color: var(--clr-blood) !important; }
    
    .sidebar-nav span { display: inline-block; font-size: 13px; font-weight: 700; }
    .nav-title { padding: 15px 25px 5px; font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; font-weight: 800; }
    .sidebar-overlay { 
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.6);
        backdrop-filter: blur(4px);
        z-index: 1999 !important;
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
    }
    .sidebar-overlay.visible { display: block; pointer-events: auto; opacity: 1; }

    /* Matching Modal Upgrade */
    #matching-results {
        display: none; 
        position: fixed; 
        inset: 0; 
        background: rgba(15, 23, 42, 0.7); 
        backdrop-filter: blur(8px);
        z-index: 9999; 
        align-items: center; 
        justify-content: center; 
        padding: 20px;
        animation: fadeIn 0.3s ease;
    }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

    .matching-card {
        background: #fff;
        width: 100%;
        max-width: 420px;
        border-radius: 24px;
        padding: 32px;
        text-align: center;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        transform: translateY(0);
        animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

    .matching-icon {
        width: 80px;
        height: 80px;
        background: #f0fdf4;
        color: #16a34a;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 40px;
        margin: 0 auto 20px;
        box-shadow: 0 0 20px rgba(22, 163, 74, 0.1);
    }

    /* Quick Search Bar */
    .search-bar {
        background: #fff;
        padding: 6px;
        border-radius: 14px;
        display: flex;
        gap: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        margin-bottom: 24px;
    }
    .search-bar select { border: none; background: #f8fafc; border-radius: 10px; padding: 8px; font-weight: 700; flex: 1; outline: none; }
  </style>
</head>
<body dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

  <div class="app-container">
    
    <aside class="sidebar" id="sidebar">
      <div class="sidebar-header">
        <div class="sidebar-logo sidebar-blood-logo">
          <i class="ph-fill ph-drop" style="color: white; font-size: 22px;"></i>
        </div>
        <div class="sidebar-brand">
          <h2>{{ __('public.blood_bank.title') }}</h2>
          <p>{{ __('public.blood_bank.subtitle') }}</p>
        </div>
      </div>

      <nav class="sidebar-nav">
        <div class="nav-title">Menu</div>
        <div class="nav-item">
          <a class="nav-link active" data-section="home" onclick="showSection('home')" href="javascript:void(0)">
            <i class="ph-bold ph-layout"></i>
            <span>{{ __('public.blood_bank.home') }}</span>
          </a>
        </div>
        <div class="nav-item">
          <a class="nav-link" data-section="urgent" onclick="showSection('urgent')" href="javascript:void(0)">
            <i class="ph-bold ph-warning-circle"></i>
            <span>{{ __('public.blood_bank.urgent_cases') }}</span>
          </a>
        </div>
        <div class="nav-item">
          <a class="nav-link" data-section="donors" onclick="showSection('donors')" href="javascript:void(0)">
            <i class="ph-bold ph-users-three"></i>
            <span>{{ __('public.blood_bank.donors') }}</span>
          </a>
        </div>

        <div class="nav-title">{{ app()->getLocale() === 'ar' ? 'الإجراءات' : 'Actions' }}</div>
        <div class="nav-item">
          <a class="nav-link" data-section="donate" onclick="showSection('donate')" href="javascript:void(0)">
            <i class="ph-bold ph-heart"></i>
            <span>{{ __('public.blood_bank.donate_now') }}</span>
          </a>
        </div>
        <div class="nav-item">
          <a class="nav-link" data-section="request" onclick="showSection('request')" href="javascript:void(0)">
            <i class="ph-bold ph-hand-heart"></i>
            <span>{{ __('public.blood_bank.request_blood') }}</span>
          </a>
        </div>
      </nav>

      <div class="sidebar-footer">
        <a href="{{ route('public.index') }}" class="logout-btn">
          <i class="ph-bold ph-arrow-left"></i>
          <span>الرجوع للموقع</span>
        </a>
      </div>
    </aside>

    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <div class="main-wrapper">
      
      <header class="topbar">
        <div class="topbar-start">
          <button class="mobile-toggle" onclick="console.log('Hamburger Clicked'); toggleSidebar()">
            <i class="ph-bold ph-list"></i>
          </button>
          <div class="breadcrumb d-none d-md-flex">
            <span class="current" id="breadcrumb-current">لوحة التحكم</span>
          </div>
          <div class="mobile-brand">
            <div class="logo-circle"><i class="ph-fill ph-drop"></i></div>
            <h2>{{ __('public.blood_bank.title') }}</h2>
          </div>
        </div>

        <div class="topbar-end">
           <a href="{{ route('locale.switch', app()->getLocale() === 'ar' ? 'en' : 'ar') }}" class="icon-btn">
              <i class="ph-bold ph-translate"></i>
           </a>
        </div>
      </header>

      <div class="content-area">
        
        <!-- SECTION: HOME (DASHBOARD) -->
        <section id="section-home" class="blood-section">
           
           <div class="search-bar">
               <select id="home-blood-filter">
                   <option value="all">{{ app()->getLocale() === 'ar' ? 'كل الفصائل' : 'All Types' }}</option>
                   <option>A+</option><option>A-</option><option>B+</option><option>B-</option>
                   <option>AB+</option><option>AB-</option><option>O+</option><option>O-</option>
               </select>
               <select id="home-gov-filter" class="gov-select">
                   <option value="all">{{ app()->getLocale() === 'ar' ? 'جميع المحافظات' : 'All Governorates' }}</option>
               </select>
               <button class="btn-blood rounded-3 px-4" onclick="searchFromHome()">{{ app()->getLocale() === 'ar' ? 'بحث' : 'Search' }}</button>
           </div>

           <div class="stat-row">
                <div class="stat-pill">
                    <div style="background: #fef2f2; padding: 10px; border-radius: 10px;"><i class="ph-fill ph-warning-circle" style="color: #ef4444;"></i></div>
                    <div><h4 id="stat-urgent">0</h4><p>{{ app()->getLocale() === 'ar' ? 'حالات عاجلة' : 'Urgent' }}</p></div>
                </div>
                <div class="stat-pill">
                    <div style="background: #f0fdf4; padding: 10px; border-radius: 10px;"><i class="ph-fill ph-users-three" style="color: #16a34a;"></i></div>
                    <div><h4 id="stat-donors">0</h4><p>{{ app()->getLocale() === 'ar' ? 'متبرعين' : 'Donors' }}</p></div>
                </div>
                <div class="stat-pill">
                    <div style="background: #eff6ff; padding: 10px; border-radius: 10px;"><i class="ph-fill ph-hand-heart" style="color: #3b82f6;"></i></div>
                    <div><h4 id="stat-requests">0</h4><p>{{ app()->getLocale() === 'ar' ? 'طلبات' : 'Requests' }}</p></div>
                </div>
           </div>

           <div class="d-flex justify-content-between align-items-center mb-3">
               <h3 class="fw-800 m-0 fs-5">🚨 {{ app()->getLocale() === 'ar' ? 'الحالات الطارئة الأخيرة' : 'Latest Urgent Cases' }}</h3>
               <button class="btn-blood-outline py-1 px-3 fs-xs" onclick="showSection('urgent')">{{ app()->getLocale() === 'ar' ? 'عرض الكل' : 'View All' }}</button>
           </div>
           <div id="urgent-list-home" class="dashboard-grid"></div>
        </section>

        <!-- SECTION: URGENT CASES -->
        <section id="section-urgent" class="blood-section" style="display: none;">
          <div class="search-bar">
              <select id="f-blood-urgent" onchange="loadRequests('urgent')">
                <option value="all">كل الفصائل</option>
                <option>A+</option><option>A-</option><option>B+</option><option>B-</option>
                <option>AB+</option><option>AB-</option><option>O+</option><option>O-</option>
              </select>
              <select class="gov-select" id="f-gov-urgent" onchange="loadRequests('urgent')">
                <option value="all">جميع المحافظات</option>
              </select>
          </div>
          <div id="urgent-all-list" class="dashboard-grid"></div>
        </section>

        <!-- SECTION: DONORS -->
        <section id="section-donors" class="blood-section" style="display: none;">
          <div class="search-bar">
              <select id="f-blood-donors" onchange="loadDonors()">
                <option value="all">كل الفصائل</option>
                <option>A+</option><option>A-</option><option>B+</option><option>B-</option>
                <option>AB+</option><option>AB-</option><option>O+</option><option>O-</option>
              </select>
              <select class="gov-select" id="f-gov-donors" onchange="loadDonors()">
                <option value="all">جميع المحافظات</option>
              </select>
          </div>
          <div id="donors-list" class="dashboard-grid"></div>
        </section>

        <!-- SECTION: DONATE (SIMPLE FORM) -->
        <section id="section-donate" class="blood-section" style="display: none;">
          <div class="row justify-content-center">
            <div class="col-md-7">
              <div class="blood-card">
                 <h3 class="fw-800 mb-4 fs-5 text-center">سجل كمتبرع الآن</h3>
                 <form id="form-donate">
                    <div class="mb-3"><label class="form-label small fw-700">{{ __('public.blood_bank.name') }}</label><input type="text" class="form-control" name="name" required placeholder="مثال: أحمد محمد"></div>
                    <div class="mb-3"><label class="form-label small fw-700">{{ __('public.blood_bank.phone') }}</label><input type="tel" class="form-control" name="phone" required placeholder="01xxxxxxxxx"></div>
                    <div class="row mb-3">
                        <div class="col-6"><label class="form-label small fw-700">{{ __('public.blood_bank.blood_type') }}</label><select class="form-control" name="blood_type" required><option value="">اختر</option><option>A+</option><option>A-</option><option>B+</option><option>B-</option><option>AB+</option><option>AB-</option><option>O+</option><option>O-</option></select></div>
                        <div class="col-6"><label class="form-label small fw-700">{{ __('public.governorate') }}</label><select class="form-control gov-select" name="governorate" required><option value="">اختر</option></select></div>
                        <div class="col-12 mt-3"><label class="form-label small fw-700">{{ app()->getLocale() === 'ar' ? 'المدينة / المنطقة' : 'City / Area' }}</label><input type="text" class="form-control" name="city" placeholder="مثال: مدينة نصر"></div>
                    </div>
                    <div class="mb-4"><label class="form-label small fw-700">{{ __('public.blood_bank.last_donation') }} ({{ app()->getLocale() === 'ar' ? 'اختياري' : 'Optional' }})</label><input type="date" class="form-control" name="last_donation_date"></div>
                    <button type="submit" id="btn-submit-donor" class="btn-blood w-100 py-2 fs-6" onclick="console.log('Button clicked!')">تأكيد التسجيل</button>
                 </form>
              </div>
            </div>
          </div>
        </section>

        <!-- SECTION: REQUEST FORM -->
        <section id="section-request" class="blood-section" style="display: none;">
          <div class="row justify-content-center">
            <div class="col-md-7">
              <div class="blood-card">
                 <h3 class="fw-800 mb-4 fs-5 text-center">طلب دم جديد</h3>
                 <form id="form-request">
                    <div id="urgent-warning" class="urgent-banner" style="display: none; background: #fee2e2; border: 1px solid #fecaca; padding: 10px; border-radius: 10px; color: #991b1b; margin-bottom: 15px;">
                        <i class="ph-bold ph-warning-octagon fs-5"></i>
                        <div class="small"><strong>تنبيه حالة طارئة!</strong> سيتم إرسال إشعارات فورية للمتبرعين.</div>
                    </div>
                    <div class="row g-3">
                      <div class="col-md-6"><label class="form-label small fw-700">{{ __('public.blood_bank.name') }}</label><input type="text" class="form-control" name="name" required></div>
                      <div class="col-md-6"><label class="form-label small fw-700">{{ __('public.blood_bank.phone') }}</label><input type="tel" class="form-control" name="phone" required></div>
                      <div class="col-6"><label class="form-label small fw-700">{{ __('public.blood_bank.blood_type') }}</label><select class="form-control" name="blood_type" required><option value="">{{ app()->getLocale() === 'ar' ? 'اختر' : 'Choose' }}</option><option>A+</option><option>A-</option><option>B+</option><option>B-</option><option>AB+</option><option>AB-</option><option>O+</option><option>O-</option></select></div>
                      <div class="col-6"><label class="form-label small fw-700">{{ __('public.governorate') }}</label><select class="form-control gov-select" name="governorate" id="req-gov-select" required onchange="fetchHospitalsForRequest(this.value)"></select></div>
                      <div class="col-6"><label class="form-label small fw-700">{{ app()->getLocale() === 'ar' ? 'المدينة / المنطقة' : 'City / Area' }}</label><input type="text" class="form-control" name="city"></div>
                      <div class="col-6"><label class="form-label small fw-700">{{ app()->getLocale() === 'ar' ? 'نوع الطلب' : 'Request Type' }}</label><select class="form-control" name="type" onchange="toggleUrgentWarning(this.value)"><option value="normal">{{ __('public.blood_bank.normal') }}</option><option value="urgent">{{ __('public.blood_bank.urgent') }}</option></select></div>
                      <div class="col-6" id="urgency-level-col" style="display: none;"><label class="form-label small fw-700">{{ app()->getLocale() === 'ar' ? 'مستوى الخطورة' : 'Urgency Level' }}</label><select class="form-control" name="urgency_level"><option value="low">{{ app()->getLocale() === 'ar' ? 'عادي' : 'Low' }}</option><option value="medium">{{ app()->getLocale() === 'ar' ? 'متوسط' : 'Medium' }}</option><option value="high">{{ app()->getLocale() === 'ar' ? 'عاجل جداً' : 'Very Urgent' }}</option></select></div>
                      <div class="col-6"><label class="form-label small fw-700">{{ __('public.blood_bank.quantity') }}</label><input type="text" class="form-control" name="quantity" placeholder="{{ app()->getLocale() === 'ar' ? 'مثال: 2 كيس' : 'Ex: 2 Bags' }}"></div>
                      <div class="col-12"><label class="form-label small fw-700">{{ __('public.blood_bank.hospital') }}</label><input type="text" class="form-control" name="hospital" list="hospitals-list" placeholder="{{ app()->getLocale() === 'ar' ? 'اسم المستشفى أو المكان' : 'Hospital Name' }}" onfocus="fetchHospitalsForRequest(document.getElementById('req-gov-select').value)"><datalist id="hospitals-list"></datalist></div>
                      <div class="col-12 mt-4"><button type="submit" class="btn-blood w-100 py-2 fs-6">{{ __('public.blood_bank.request_blood') }}</button></div>
                    </div>
                 </form>
              </div>
            </div>
          </div>
        </section>

      </div>
    </div>
  </div>

  <div id="matching-results">
       <div class="matching-card">
            <div class="matching-icon"><i class="ph-fill ph-check-circle"></i></div>
            <h3 class="fw-900 mb-2 fs-5 text-black" style="color: #000 !important;">{{ app()->getLocale() === 'ar' ? 'تم العثور على متبرعين!' : 'Donors Found!' }}</h3>
            <p id="matching-text" class="mb-4 fw-600 text-black lh-base px-2 small" style="color: #000 !important;"></p>
            <div class="d-flex flex-row gap-4 mt-3 justify-content-center">
                <button class="btn-blood py-2 px-0 rounded-3 fs-xs" style="width: 110px;" onclick="goToDonors()">
                    {{ app()->getLocale() === 'ar' ? 'عرض المتبرعين' : 'View Donors' }}
                </button>
                <button class="btn-blood-outline py-2 px-0 rounded-3 fs-xs border-1" style="width: 110px;" onclick="closeMatching()">
                    {{ app()->getLocale() === 'ar' ? 'تجاهل' : 'Ignore' }}
                </button>
            </div>
       </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    const API_BASE = '/api/public/blood-bank';
    const LOCATIONS_API = '/api/public/locations';
    const isAr = true;

    // Define toggleSidebar early
    window.toggleSidebar = function() {
        console.log("Toggling sidebar...");
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        if (sidebar && overlay) {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('visible');
        }
    };

    window.toggleUrgentWarning = function(val) {
        const warn = document.getElementById('urgent-warning');
        const col = document.getElementById('urgency-level-col');
        if (warn) warn.style.display = (val === 'urgent') ? 'flex' : 'none';
        if (col) col.style.display = (val === 'urgent') ? 'block' : 'none';
    };

    function showSection(id) {
        document.querySelectorAll('.nav-link').forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('data-section') === id) link.classList.add('active');
        });

        document.querySelectorAll('.blood-section').forEach(sec => sec.style.display = 'none');
        document.getElementById('section-' + id).style.display = 'block';

        const titles = { 
            home: "{{ __('public.blood_bank.home') }}", 
            urgent: "{{ __('public.blood_bank.urgent_cases') }}", 
            donors: "{{ __('public.blood_bank.donors') }}", 
            donate: "{{ __('public.blood_bank.donate_now') }}", 
            request: "{{ __('public.blood_bank.request_blood') }}" 
        };
        document.getElementById('breadcrumb-current').textContent = titles[id];

        if (window.innerWidth <= 992) {
            const sidebar = document.getElementById('sidebar');
            if (sidebar.classList.contains('active')) toggleSidebar();
        }

        if (id === 'home') { loadRequests('home'); loadStats(); }
        if (id === 'urgent') loadRequests('all');
        if (id === 'donors') loadDonors();
    }

    async function loadStats() {
        try {
            const [reqs, donors] = await Promise.all([
                fetch(`${API_BASE}/requests`).then(r => r.json()),
                fetch(`${API_BASE}/donors`).then(r => r.json())
            ]);
            document.getElementById('stat-urgent').textContent = (reqs.data || []).filter(r => r.type === 'urgent').length;
            document.getElementById('stat-donors').textContent = (donors.data || []).length;
            document.getElementById('stat-requests').textContent = (reqs.data || []).length;
        } catch (e) { console.error(e); }
    }

    function getTimeAgo(dateString) {
        const date = new Date(dateString);
        const diff = Math.floor((new Date() - date) / 1000);
        if (diff < 60) return isAr ? 'الآن' : 'Now';
        if (diff < 3600) return isAr ? `منذ ${Math.floor(diff / 60)} دقيقة` : `${Math.floor(diff / 60)} min ago`;
        if (diff < 86400) return isAr ? `منذ ${Math.floor(diff / 3600)} ساعة` : `${Math.floor(diff / 3600)} h ago`;
        return date.toLocaleDateString();
    }

    async function loadRequests(mode) {
        const container = document.getElementById(mode === 'home' ? 'urgent-list-home' : 'urgent-all-list');
        let url = `${API_BASE}/requests` + (mode === 'home' ? '?type=urgent&limit=6' : `?blood_type=${encodeURIComponent(document.getElementById('f-blood-urgent').value)}&governorate=${encodeURIComponent(document.getElementById('f-gov-urgent').value)}`);
        try {
            const res = await fetch(url);
            const data = (await res.json()).data || [];
            if (data.length === 0) { container.innerHTML = `<div class="col-12 text-center py-4 text-muted small">${isAr ? 'لا توجد حالات حالياً' : 'No cases at the moment'}</div>`; return; }
            container.innerHTML = data.map(req => `
                <div class="blood-card">
                    <div class="d-flex justify-content-between mb-2">
                        <div class="blood-type-badge">${req.blood_type}</div>
                        <span class="timer-box" data-time="${req.created_at}"><i class="ph ph-clock"></i> <span>${getTimeAgo(req.created_at)}</span></span>
                    </div>
                    <div class="donor-name">${req.name}</div>
                    <div class="small text-muted mb-3"><i class="ph ph-map-pin"></i> ${req.governorate} ${req.city ? '| ' + req.city : ''}</div>
                    <a href="tel:01021021036" class="btn-blood py-1 px-2 w-100 fs-xs">
                        <i class="ph-bold ph-phone me-1"></i>
                        ${isAr ? 'طلب التنسيق عبر الإدارة' : 'Request Coordination'}
                    </a>
                </div>
            `).join('');
        } catch (e) { console.error(e); }
    }

    async function loadDonors() {
        const container = document.getElementById('donors-list');
        const bType = document.getElementById('f-blood-donors').value;
        const gov = document.getElementById('f-gov-donors').value;
        const url = `${API_BASE}/donors?blood_type=${encodeURIComponent(bType)}&governorate=${encodeURIComponent(gov)}`;
        try {
            const res = await fetch(url);
            const data = (await res.json()).data || [];
            if (data.length === 0) { 
                container.innerHTML = `<div class="col-12 text-center py-4 text-muted small">${isAr ? 'لا يوجد متبرعين مطابقين للبحث حالياً' : 'No donors matching your search at the moment'}</div>`; 
                return; 
            }
            container.innerHTML = data.map(donor => `
                <div class="blood-card text-center">
                    <div class="blood-type-badge mx-auto mb-2" style="background: #16a34a;">${donor.blood_type}</div>
                    <div class="donor-name">${donor.name}</div>
                    <div class="small text-muted mb-3">${donor.governorate} ${donor.city ? '| ' + donor.city : ''}</div>
                    <a href="tel:01021021036" class="btn-blood py-1 px-2 w-100 fs-xs" style="background: #16a34a;">
                        <i class="ph-bold ph-phone me-1"></i>
                        ${isAr ? 'تواصل مع الإدارة' : 'Contact Admin'}
                    </a>
                </div>
            `).join('');
        } catch (e) { console.error(e); }
    }

    async function fetchGovs() {
        try {
            const res = await fetch(LOCATIONS_API);
            const json = await res.json();
            const html = json.data.governorates.map(g => `<option value="${g}">${g}</option>`).join('');
            document.querySelectorAll('.gov-select').forEach(s => s.innerHTML += html);
        } catch (e) { console.error(e); }
    }

    function searchFromHome() {
        document.getElementById('f-blood-urgent').value = document.getElementById('home-blood-filter').value;
        document.getElementById('f-gov-urgent').value = document.getElementById('home-gov-filter').value;
        showSection('urgent');
    }

    async function fetchHospitalsForRequest(gov) {
        if (!gov || gov === 'all') return;
        try {
            const res = await fetch(`${API_BASE}/hospitals?governorate=${encodeURIComponent(gov)}`);
            const hospitals = (await res.json()).data || [];
            document.getElementById('hospitals-list').innerHTML = hospitals.map(h => `<option value="${h.name}">`).join('');
        } catch (e) { console.error(e); }
    }

    function closeMatching() { document.getElementById('matching-results').style.display = 'none'; }
    function goToDonors() { 
        closeMatching(); 
        showSection('donors');
        loadDonors(); // Explicitly reload with new filters
    }

    window.onload = () => {
        console.log("Blood Bank Dashboard Loaded");
        fetchGovs();
        loadRequests('home');
        loadStats();

        // Attach Event Listeners inside onload
        const donorForm = document.getElementById('form-donate');
        if (donorForm) {
            donorForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                console.log("Donor form submitting...");
                const btn = e.target.querySelector('button[type="submit"]');
                btn.disabled = true;
                btn.innerHTML = '<i class="ph-bold ph-spinner-gap ph-spin"></i> جاري الحفظ...';

                try {
                    const res = await fetch(`${API_BASE}/donors`, {
                        method: 'POST',
                        headers: { 
                            'Content-Type': 'application/json', 
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}' 
                        },
                        body: JSON.stringify(Object.fromEntries(new FormData(e.target).entries()))
                    });
                    const json = await res.json();
                    if (json.status === 'success' || json.status === true) {
                        Swal.fire('تم بنجاح!', json.message || "شكراً لتطوعك يا بطل!", 'success');
                        e.target.reset();
                        showSection('home');
                    } else {
                        Swal.fire('خطأ!', json.message || 'حدث خطأ ما', 'error');
                    }
                } catch (err) {
                    console.error(err);
                    Swal.fire('خطأ!', 'فشل الاتصال بالسيرفر', 'error');
                } finally {
                    btn.disabled = false;
                    btn.textContent = 'تأكيد التسجيل';
                }
            });
        }

        const requestForm = document.getElementById('form-request');
        if (requestForm) {
            requestForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                console.log("Request form submitting...");
                const btn = e.target.querySelector('button[type="submit"]');
                btn.disabled = true;

                try {
                    const data = Object.fromEntries(new FormData(e.target).entries());
                    const res = await fetch(`${API_BASE}/requests`, {
                        method: 'POST',
                        headers: { 
                            'Content-Type': 'application/json', 
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}' 
                        },
                        body: JSON.stringify(data)
                    });
                    const json = await res.json();
                    if (json.status === 'success' || json.status === true) {
                        if (json.matched_donors_count > 0) {
                            const msg = isAr ? `وجدنا ${json.matched_donors_count} متبرع قريب منك فصيلة ${data.blood_type}.` : `Found ${json.matched_donors_count} donors near you with ${data.blood_type}.`;
                            document.getElementById('matching-text').textContent = msg;
                            document.getElementById('matching-results').style.display = 'flex';
                            
                            // Set filters for the donors page
                            document.getElementById('f-blood-donors').value = data.blood_type;
                            document.getElementById('f-gov-donors').value = data.governorate;
                        } else {
                            const msg = isAr ? "تم نشر طلبك بنجاح وسيظهر للجميع." : "Your request has been published successfully.";
                            Swal.fire(isAr ? 'تم النشر!' : 'Published!', msg, 'success');
                        }
                        e.target.reset();
                        if (json.matched_donors_count === 0) showSection('home');
                    }
                } catch (err) {
                    console.error(err);
                } finally {
                    btn.disabled = false;
                    btn.textContent = 'نشر الطلب';
                }
            });
        }

        // Auto-update timers
        setInterval(() => {
            document.querySelectorAll('.timer-box').forEach(box => {
                const time = box.getAttribute('data-time');
                if (time) box.querySelector('span').textContent = getTimeAgo(time);
            });
        }, 60000);
    };
  </script>
</body>
</html>
