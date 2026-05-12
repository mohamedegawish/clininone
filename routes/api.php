<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\ProfileController;
use App\Http\Controllers\Api\v1\Admin\DoctorManagementController;
use App\Http\Controllers\Api\v1\Admin\ClinicController;
use App\Http\Controllers\Api\v1\Admin\StatsController;
use App\Http\Controllers\Api\v1\Clinic\ConsultationController;
use App\Http\Controllers\Api\v1\Clinic\DashboardController;
use App\Http\Controllers\Api\v1\Clinic\MedicationController;
use App\Http\Controllers\Api\v1\Clinic\PrescriptionTemplateController;
use App\Http\Controllers\Api\v1\Clinic\SettingsController;
use App\Http\Controllers\Api\v1\Public\DoctorController as PublicDoctorController;
use App\Http\Controllers\Api\v1\Public\ReviewController as PublicReviewController;
use App\Http\Controllers\Api\v1\Public\BloodBankController;
use App\Http\Controllers\Api\v1\Public\AppointmentBookingController as PublicBookingController;

Route::get('/ping', function() { return response()->json(['message' => 'pong']); });

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

Route::post('/forgot-password', [AuthController::class, 'sendOtp'])->middleware('throttle:3,1');
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::post('/refresh-token', [AuthController::class, 'refreshToken']);

Route::middleware(['auth:sanctum', 'check.token.expiry'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // ── Profile (authenticated user) ─────────────────────────────────────────
    Route::prefix('profile')->group(function () {
        Route::get('/',        [ProfileController::class, 'show']);
        Route::put('/',        [ProfileController::class, 'update']);
        Route::post('/photo',  [ProfileController::class, 'updatePhoto']);
        Route::put('/password',[ProfileController::class, 'changePassword']);
    });

    Route::prefix('admin')->group(function () {
        Route::middleware('super_admin')->group(function () {
            Route::apiResource('doctors', DoctorManagementController::class);
            Route::get('stats', [StatsController::class, 'index']);
        });

        Route::apiResource('clinics', ClinicController::class);
    });

    Route::prefix('clinic')->group(function () {
        // ── Dashboard ────────────────────────────────────────────────────────
        Route::get('dashboard', [DashboardController::class, 'index']);

        // ── Patients ─────────────────────────────────────────────────────────
        Route::apiResource('patients', \App\Http\Controllers\Api\v1\Clinic\PatientController::class);

        // ── Doctor Schedules ─────────────────────────────────────────────────
        Route::get('schedules',         [\App\Http\Controllers\Api\v1\Clinic\DoctorScheduleController::class, 'index']);
        Route::post('schedules',        [\App\Http\Controllers\Api\v1\Clinic\DoctorScheduleController::class, 'store']);
        Route::post('schedules/bulk',   [\App\Http\Controllers\Api\v1\Clinic\DoctorScheduleController::class, 'bulkSet']);
        Route::delete('schedules/{id}', [\App\Http\Controllers\Api\v1\Clinic\DoctorScheduleController::class, 'destroy']);

        // ── Appointments ─────────────────────────────────────────────────────
        Route::get('appointments/available-slots', [\App\Http\Controllers\Api\v1\Clinic\AppointmentController::class, 'availableSlots']);
        Route::apiResource('appointments', \App\Http\Controllers\Api\v1\Clinic\AppointmentController::class)->only(['index', 'store', 'show']);
        Route::patch('appointments/{id}/cancel',   [\App\Http\Controllers\Api\v1\Clinic\AppointmentController::class, 'cancel']);
        Route::patch('appointments/{id}/confirm',  [\App\Http\Controllers\Api\v1\Clinic\AppointmentController::class, 'confirm']);
        Route::patch('appointments/{id}/complete', [\App\Http\Controllers\Api\v1\Clinic\AppointmentController::class, 'complete']);

        // ── Queue ────────────────────────────────────────────────────────────
        Route::get('queue/{doctorId}',         [\App\Http\Controllers\Api\v1\Clinic\QueueController::class, 'show']);
        Route::put('queue/{doctorId}/advance', [\App\Http\Controllers\Api\v1\Clinic\QueueController::class, 'advance']);

        // ── Consultations & Diagnoses ────────────────────────────────────────
        Route::get('consultations',                         [ConsultationController::class, 'index']);
        Route::post('consultations/{appointmentId}',        [ConsultationController::class, 'store']);
        Route::get('consultations/{id}',                    [ConsultationController::class, 'show']);
        Route::get('consultations/{id}/print',              [ConsultationController::class, 'printData']);
        Route::get('consultations/{id}/prescription',       [ConsultationController::class, 'prescriptionHtml']);
        Route::get('diagnoses',                             [ConsultationController::class, 'listDiagnoses']);
        Route::post('diagnoses',                            [ConsultationController::class, 'storeDiagnosis']);

        // ── Medications ──────────────────────────────────────────────────────
        Route::get('medications/search',                    [MedicationController::class, 'search']);
        Route::post('medications',                          [MedicationController::class, 'store']);
        Route::post('medications/{medicationId}/favorite',  [MedicationController::class, 'toggleFavorite']);

        // ── Prescription Templates ───────────────────────────────────────────
        Route::get('prescription-templates',        [PrescriptionTemplateController::class, 'index']);
        Route::post('prescription-templates',       [PrescriptionTemplateController::class, 'store']);
        Route::get('prescription-templates/{id}',   [PrescriptionTemplateController::class, 'show']);
        Route::delete('prescription-templates/{id}',[PrescriptionTemplateController::class, 'destroy']);

        // ── Expenses & Stats ─────────────────────────────────────────────────
        Route::apiResource('expenses', \App\Http\Controllers\Api\v1\Clinic\ExpenseController::class);
        Route::get('reports', [\App\Http\Controllers\Api\v1\Clinic\StatsController::class, 'index']);

        // ── Settings ─────────────────────────────────────────────────────────
        Route::get('settings',                      [SettingsController::class, 'index']);
        Route::put('settings/prices',               [SettingsController::class, 'updatePrices']);
        Route::post('settings/services',            [SettingsController::class, 'storeService']);
        Route::delete('settings/services/{id}',     [SettingsController::class, 'destroyService']);
        Route::post('settings/branding',            [SettingsController::class, 'updateBranding']);
        Route::post('settings/otp/request',         [SettingsController::class, 'requestOtp']);
        Route::post('settings/otp/verify',          [SettingsController::class, 'verifyOtp']);

        // ── Notifications ────────────────────────────────────────────────────
        Route::get('notifications',              [\App\Http\Controllers\Api\v1\Clinic\NotificationController::class, 'index']);
        Route::get('notifications/unread-count', [\App\Http\Controllers\Api\v1\Clinic\NotificationController::class, 'unreadCount']);
        Route::post('notifications/{id}/read',   [\App\Http\Controllers\Api\v1\Clinic\NotificationController::class, 'markRead']);
        Route::post('notifications/read-all',    [\App\Http\Controllers\Api\v1\Clinic\NotificationController::class, 'markAllRead']);
    });
});

Route::prefix('public')->middleware('throttle:120,1')->group(function () {
    Route::get('doctors', [PublicDoctorController::class, 'index']);
    Route::get('doctors/{doctor}', [PublicDoctorController::class, 'show']);
    Route::get('specialties', [PublicDoctorController::class, 'specialties']);
    Route::get('locations', [PublicDoctorController::class, 'locations']);

    Route::prefix('blood-bank')->group(function () {
        // Donor and Blood Request APIs
        Route::get('donors', [BloodBankController::class, 'donors']);
        Route::post('donors', [BloodBankController::class, 'storeDonor']);
        Route::get('requests', [BloodBankController::class, 'requests']);
        Route::post('requests', [BloodBankController::class, 'storeRequest']);
        Route::get('hospitals', [BloodBankController::class, 'getHospitals']);
    });
    
    Route::get('reviews', [PublicReviewController::class, 'index']);
    Route::post('reviews', [PublicReviewController::class, 'store']);
    
    Route::get('appointments/available-slots', [PublicBookingController::class, 'availableSlots']);
    Route::post('appointments/book', [PublicBookingController::class, 'store']);
});
