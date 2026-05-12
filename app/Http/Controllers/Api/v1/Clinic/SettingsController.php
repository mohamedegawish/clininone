<?php

namespace App\Http\Controllers\Api\v1\Clinic;

use App\Http\Controllers\Controller;
use App\Http\Resources\DoctorServiceResource;
use App\Models\core\DoctorService;
use App\Models\core\OtpVerification;
use App\Models\Scopes\ClinicScope;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SettingsController extends Controller
{
    use HttpResponses;

    /**
     * GET /clinic/settings
     * Return current user + doctor + clinic profile.
     */
    public function index(Request $request): JsonResponse
    {
        $user   = $request->user();
        $doctor = $user->doctor;
        $clinicId = $this->resolveClinicId($request);

        $clinic = $clinicId
            ? \App\Models\core\Clinic::find($clinicId)
            : null;

        return $this->success([
            'user' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role,
            ],
            'doctor' => $doctor ? [
                'id'              => $doctor->id,
                'name'            => $doctor->name,
                'arabic_name'     => $doctor->arabic_name,
                'specialty'       => $doctor->specialty,
                'phone'           => $doctor->phone,
                'price'           => (float) $doctor->price,
                'followup_price'  => (float) $doctor->followup_price,
                'photo'           => $doctor->photo_path
                    ? asset('storage/' . $doctor->photo_path)
                    : null,
                'services'        => DoctorServiceResource::collection(
                    DoctorService::withoutGlobalScope(ClinicScope::class)
                        ->where('doctor_id', $doctor->id)
                        ->where('clinic_id', $clinicId)
                        ->get()
                ),
            ] : null,
            'clinic' => $clinic ? [
                'id'            => $clinic->id,
                'name'          => $clinic->name,
                'phone'         => $clinic->phone,
                'address'       => $clinic->address,
                'primary_color' => $clinic->primaryColor(),
                'logo'          => $clinic->logoUrl(),
            ] : null,
        ]);
    }

    /**
     * PUT /clinic/settings/prices
     * Update doctor consultation & follow-up prices.
     */
    public function updatePrices(Request $request): JsonResponse
    {
        $doctor = $request->user()?->doctor;

        if (! $doctor) {
            return $this->error('Doctor profile not found.', 403);
        }

        $validated = $request->validate([
            'price'          => 'nullable|numeric|min:0',
            'followup_price' => 'nullable|numeric|min:0',
        ]);

        $doctor->update($validated);

        return $this->success([
            'price'          => (float) $doctor->fresh()->price,
            'followup_price' => (float) $doctor->fresh()->followup_price,
        ], 'Prices updated successfully.');
    }

    /**
     * POST /clinic/settings/services
     * Add a new service for the doctor in this clinic.
     */
    public function storeService(Request $request): JsonResponse
    {
        $doctor   = $request->user()?->doctor;
        $clinicId = $this->resolveClinicId($request);

        if (! $doctor || ! $clinicId) {
            return $this->error('Doctor or clinic context missing.', 403);
        }

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        $service = DoctorService::withoutGlobalScope(ClinicScope::class)->create([
            'doctor_id' => $doctor->id,
            'clinic_id' => $clinicId,
            'name'      => $validated['name'],
            'price'     => $validated['price'],
        ]);

        return $this->success(new DoctorServiceResource($service), 'Service added.', 201);
    }

    /**
     * DELETE /clinic/settings/services/{service}
     */
    public function destroyService(int $serviceId, Request $request): JsonResponse
    {
        $doctor = $request->user()?->doctor;

        $service = DoctorService::withoutGlobalScope(ClinicScope::class)->findOrFail($serviceId);

        if (! $doctor || $service->doctor_id !== $doctor->id) {
            return $this->error('Unauthorized.', 403);
        }

        $service->delete();

        return $this->success(null, 'Service removed.');
    }

    /**
     * POST /clinic/settings/branding
     * Update clinic branding (name, phone, address, color, logo).
     */
    public function updateBranding(Request $request): JsonResponse
    {
        $clinicId = $this->resolveClinicId($request);
        if (! $clinicId) {
            return $this->error('Clinic context missing.', 403);
        }

        $request->validate([
            'primary_color'  => 'nullable|regex:/^#[0-9a-fA-F]{6}$/',
            'clinic_phone'   => 'nullable|string|max:30',
            'clinic_address' => 'nullable|string|max:255',
            'clinic_logo'    => 'nullable|image|mimes:jpg,jpeg,png,svg,webp|max:2048',
        ]);

        $clinic = \App\Models\core\Clinic::findOrFail($clinicId);

        $data = [];
        if ($request->filled('primary_color'))  $data['primary_color'] = $request->primary_color;
        if ($request->filled('clinic_phone'))   $data['phone']         = $request->clinic_phone;
        if ($request->filled('clinic_address')) $data['address']       = $request->clinic_address;

        if ($request->hasFile('clinic_logo')) {
            if ($clinic->logo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($clinic->logo);
            }
            $data['logo'] = $request->file('clinic_logo')->store('branding', 'public');
        }

        if ($data) {
            $clinic->update($data);
        }

        return $this->success([
            'primary_color' => $clinic->fresh()->primaryColor(),
            'logo'          => $clinic->fresh()->logoUrl(),
            'phone'         => $clinic->fresh()->phone,
            'address'       => $clinic->fresh()->address,
        ], 'Branding updated.');
    }

    /**
     * POST /clinic/settings/otp/request
     * Send OTP for email or password change.
     */
    public function requestOtp(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'type'         => 'required|in:email_change,password_change',
            'new_email'    => 'required_if:type,email_change|email|unique:users,email',
            'new_password' => 'required_if:type,password_change|min:8|confirmed',
        ]);

        $code    = strtoupper(Str::random(6));
        $payload = [];

        if ($request->type === 'email_change') {
            $payload['email'] = $request->new_email;
        } elseif ($request->type === 'password_change') {
            $payload['password'] = Hash::make($request->new_password);
        }

        OtpVerification::create([
            'user_id'    => $user->id,
            'code'       => $code,
            'type'       => $request->type,
            'payload'    => $payload,
            'expires_at' => now()->addMinutes(10),
        ]);

        // In production this would send an email/SMS
        Log::info("OTP for {$user->email}: {$code}");

        return $this->success(null, 'OTP sent to your email. It expires in 10 minutes.');
    }

    /**
     * POST /clinic/settings/otp/verify
     * Verify OTP and apply the change.
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'code' => 'required|string',
            'type' => 'required|in:email_change,password_change',
        ]);

        $otp = OtpVerification::where('user_id', $user->id)
            ->where('code', strtoupper($request->code))
            ->where('type', $request->type)
            ->whereNull('verified_at')
            ->where('expires_at', '>', now())
            ->first();

        if (! $otp) {
            return $this->error('Invalid or expired OTP.', 422);
        }

        $otp->update(['verified_at' => now()]);

        $message = '';
        if ($otp->type === 'email_change') {
            $user->update(['email' => $otp->payload['email']]);
            $message = 'Email updated successfully.';
        } elseif ($otp->type === 'password_change') {
            $user->update(['password' => $otp->payload['password']]);
            $message = 'Password updated successfully.';
        }

        return $this->success(null, $message);
    }

    private function resolveClinicId(Request $request): ?int
    {
        $user = $request->user();
        return $user?->clinic_id ?? $user?->doctor?->clinics()->value('clinics.id');
    }
}
