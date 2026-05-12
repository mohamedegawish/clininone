<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\LocaleController;
use App\Http\Controllers\Web\ProfileController;

Route::middleware('cache.headers:3600')->group(function () {
    Route::get('/', [App\Http\Controllers\Web\Public\LandingController::class, 'index'])->name('public.index');
    Route::get('/doctor', [App\Http\Controllers\Web\Public\LandingController::class, 'doctor'])->name('public.doctor');
    Route::get('/booking', [App\Http\Controllers\Web\Public\LandingController::class, 'booking'])->name('public.booking');
    Route::get('/blood-bank', [App\Http\Controllers\Web\Public\LandingController::class, 'bloodBank'])->name('public.blood-bank');
});

Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate']);
    
    Route::get('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.request');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/settings', [ProfileController::class, 'settings'])->name('settings');
    Route::post('/settings', [ProfileController::class, 'updateSystemSettings'])->name('settings.update');

    // Redirection after login
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

    // Super Admin Routes
    Route::prefix('admin')->name('admin.')->middleware('super_admin')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Web\SuperAdmin\DashboardController::class, 'index'])->name('dashboard');
        
        Route::resource('clinics', App\Http\Controllers\Web\SuperAdmin\ClinicManagementController::class);
        Route::post('clinics/{clinic}/toggle-status', [App\Http\Controllers\Web\SuperAdmin\ClinicManagementController::class, 'toggleStatus'])->name('clinics.toggle-status');
        Route::get('clinics/{clinic}/assign-plan', [App\Http\Controllers\Web\SuperAdmin\ClinicManagementController::class, 'assignPlanForm'])->name('clinics.assign-plan');
        Route::post('clinics/{clinic}/assign-plan', [App\Http\Controllers\Web\SuperAdmin\ClinicManagementController::class, 'assignPlan']);
        
        Route::resource('doctors', App\Http\Controllers\Web\SuperAdmin\DoctorManagementController::class)->except(['show', 'edit', 'update']);
        Route::get('doctors/{doctor}/schedule', [App\Http\Controllers\Web\SuperAdmin\DoctorScheduleController::class, 'edit'])->name('doctors.schedule.edit');
        Route::put('doctors/{doctor}/schedule', [App\Http\Controllers\Web\SuperAdmin\DoctorScheduleController::class, 'update'])->name('doctors.schedule.update');
        Route::resource('patients', App\Http\Controllers\Web\SuperAdmin\PatientManagementController::class);
        Route::get('appointments', [App\Http\Controllers\Web\SuperAdmin\AppointmentManagementController::class, 'index'])->name('appointments.index');
        
        Route::resource('plans', App\Http\Controllers\Web\SuperAdmin\PlanController::class)->except('show');
        Route::resource('subscriptions', App\Http\Controllers\Web\SuperAdmin\SubscriptionController::class)->only(['index', 'create', 'store']);
        Route::get('reports', [App\Http\Controllers\Web\SuperAdmin\ReportsController::class, 'index'])->name('reports.index');

        // Blood Bank Management
        Route::prefix('blood-bank')->name('blood-bank.')->group(function () {
            Route::get('/', [App\Http\Controllers\Web\SuperAdmin\BloodBankController::class, 'index'])->name('index');
            Route::get('/requests/{request}', [App\Http\Controllers\Web\SuperAdmin\BloodBankController::class, 'showRequest'])->name('requests.show');
            Route::post('/requests/{request}/status', [App\Http\Controllers\Web\SuperAdmin\BloodBankController::class, 'updateRequestStatus'])->name('requests.update-status');
            Route::get('/donors', [App\Http\Controllers\Web\SuperAdmin\BloodBankController::class, 'donors'])->name('donors');
            Route::post('/donors/{donor}/toggle-status', [App\Http\Controllers\Web\SuperAdmin\BloodBankController::class, 'toggleDonorStatus'])->name('donors.toggle-status');
        });
    });



    // Clinic Admin Routes — guarded by clinic.context to prevent cross-tenant access
    Route::prefix('clinic')->name('clinic.')->middleware('clinic.context')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Web\Clinic\DashboardController::class, 'index'])->name('dashboard');
        
        // Patients
        Route::resource('patients', App\Http\Controllers\Web\Clinic\PatientController::class);
        
        // Appointments & Consultations
        Route::resource('appointments', App\Http\Controllers\Web\Clinic\AppointmentController::class);
        Route::post('appointments/{appointment}/confirm', [App\Http\Controllers\Web\Clinic\AppointmentController::class, 'confirm'])->name('appointments.confirm');
        Route::post('appointments/{appointment}/mark-paid', [App\Http\Controllers\Web\Clinic\AppointmentController::class, 'markPaid'])->name('appointments.mark-paid');
        Route::get('consultations/create/{appointment}', [App\Http\Controllers\Web\Clinic\ConsultationController::class, 'create'])->name('consultations.create');
        Route::post('consultations/{appointment}', [App\Http\Controllers\Web\Clinic\ConsultationController::class, 'store'])->name('consultations.store');
        Route::get('consultations/{consultation}', [App\Http\Controllers\Web\Clinic\ConsultationController::class, 'show'])->name('consultations.show');
        Route::post('consultations/diagnoses/store', [App\Http\Controllers\Web\Clinic\ConsultationController::class, 'storeDiagnosis'])->name('consultations.diagnoses.store');

        // Smart Medication System
        Route::get('medications/search', [App\Http\Controllers\Web\Clinic\MedicationController::class, 'search'])->name('medications.search');
        Route::post('medications', [App\Http\Controllers\Web\Clinic\MedicationController::class, 'store'])->name('medications.store');
        Route::post('medications/{medication}/favorite', [App\Http\Controllers\Web\Clinic\MedicationController::class, 'toggleFavorite'])->name('medications.favorite');

        // Prescription Templates
        Route::get('prescription-templates', [App\Http\Controllers\Web\Clinic\TemplateController::class, 'index'])->name('prescription-templates.index');
        Route::post('prescription-templates', [App\Http\Controllers\Web\Clinic\TemplateController::class, 'store'])->name('prescription-templates.store');
        Route::get('prescription-templates/{template}/load', [App\Http\Controllers\Web\Clinic\TemplateController::class, 'load'])->name('prescription-templates.load');

        // Professional Print View
        Route::get('prescriptions/{consultation}/print', [App\Http\Controllers\Web\Clinic\ConsultationController::class, 'printView'])->name('prescriptions.print');
        
        // Queue
        Route::get('/queue', [App\Http\Controllers\Web\Clinic\QueueController::class, 'show'])->name('queue.show');
        Route::get('/queue/data', [App\Http\Controllers\Web\Clinic\QueueController::class, 'data'])->name('queue.data'); // For AJAX polling
        
        // Schedule
        Route::resource('schedule', App\Http\Controllers\Web\Clinic\ScheduleController::class)->only(['index', 'store']);
        
        // Expenses
        Route::resource('expenses', App\Http\Controllers\Web\Clinic\ExpenseController::class);
        
        // Settings & OTP & Locale
        Route::get('settings', [App\Http\Controllers\Web\Clinic\SettingsController::class, 'index'])->name('settings.index');
        Route::post('settings/request-otp', [App\Http\Controllers\Web\Clinic\SettingsController::class, 'requestOtp'])->name('settings.request-otp');
        Route::post('settings/verify-otp', [App\Http\Controllers\Web\Clinic\SettingsController::class, 'verifyOtp'])->name('settings.verify-otp');
        Route::post('settings/locale', [App\Http\Controllers\Web\Clinic\SettingsController::class, 'updateLocale'])->name('settings.locale');
        Route::post('settings/prices', [App\Http\Controllers\Web\Clinic\SettingsController::class, 'updatePrices'])->name('settings.prices');
        Route::post('settings/services', [App\Http\Controllers\Web\Clinic\SettingsController::class, 'storeService'])->name('settings.services.store');
        Route::delete('settings/services/{service}', [App\Http\Controllers\Web\Clinic\SettingsController::class, 'destroyService'])->name('settings.services.destroy');
        Route::post('settings/branding', [App\Http\Controllers\Web\Clinic\SettingsController::class, 'updateBranding'])->name('settings.branding');
        
        // Reports
        Route::get('reports', [App\Http\Controllers\Web\Clinic\ReportController::class, 'index'])->name('reports.index');

        // Notifications
        Route::get('/notifications/check', [App\Http\Controllers\Web\Clinic\NotificationController::class, 'check'])->name('notifications.check');
        Route::get('/notifications', [App\Http\Controllers\Web\Clinic\NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/{notification}/read', [App\Http\Controllers\Web\Clinic\NotificationController::class, 'markRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [App\Http\Controllers\Web\Clinic\NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    });
});

