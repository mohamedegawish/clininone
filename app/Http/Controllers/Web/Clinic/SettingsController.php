<?php

namespace App\Http\Controllers\Web\Clinic;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\Clinic\Concerns\ResolvesClinic;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\core\OtpVerification;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    use ResolvesClinic;

    public function index(): View
    {
        $user   = auth()->user();
        $clinic = $this->resolveClinic();
        return view('clinic.settings.index', compact('user', 'clinic'));
    }

    public function requestOtp(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'type' => 'required|in:email_change,password_change',
            'new_email' => 'required_if:type,email_change|email|unique:users,email',
            'new_password' => 'required_if:type,password_change|min:8|confirmed',
        ]);

        $code = strtoupper(Str::random(6));
        
        $payload = [];
        if ($request->type === 'email_change') {
            $payload['email'] = $request->new_email;
        } elseif ($request->type === 'password_change') {
            $payload['password'] = Hash::make($request->new_password);
        }

        OtpVerification::create([
            'user_id' => $user->id,
            'code' => $code,
            'type' => $request->type,
            'payload' => $payload,
            'expires_at' => now()->addMinutes(10),
        ]);

        // Mocking email sending
        Log::info("OTP for {$user->email} is: {$code}");

        return response()->json([
            'status' => 'success',
            'message' => 'OTP sent to your email.'
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'code' => 'required|string',
            'type' => 'required|in:email_change,password_change',
        ]);

        $otp = OtpVerification::where('user_id', $user->id)
            ->where('code', $request->code)
            ->where('type', $request->type)
            ->whereNull('verified_at')
            ->where('expires_at', '>', now())
            ->first();

        if (!$otp) {
            return response()->json(['status' => 'error', 'message' => 'Invalid or expired OTP.'], 400);
        }

        $otp->update(['verified_at' => now()]);

        if ($otp->type === 'email_change') {
            $user->update(['email' => $otp->payload['email']]);
            $message = 'Email updated successfully.';
        } elseif ($otp->type === 'password_change') {
            $user->update(['password' => $otp->payload['password']]);
            $message = 'Password updated successfully.';
        }

        return response()->json(['status' => 'success', 'message' => $message]);
    }

    public function updateLocale(Request $request)
    {
        $request->validate([
            'locale' => 'required|in:en,ar',
        ]);

        \Illuminate\Support\Facades\Session::put('locale', $request->locale);

        return redirect()->back()->with('success', __('clinic.settings.save_preferences'));
    }

    public function updatePrices(Request $request)
    {
        $user = auth()->user();
        if (!$user->doctor) {
            return redirect()->back()->with('error', 'Doctor profile not found.');
        }

        $validated = $request->validate([
            'price' => 'nullable|numeric|min:0',
            'followup_price' => 'nullable|numeric|min:0',
        ]);

        $user->doctor->update($validated);

        return redirect()->back()->with('success', 'Prices updated successfully.');
    }

    public function storeService(Request $request)
    {
        $user = auth()->user();
        if (!$user->doctor) {
            return redirect()->back()->with('error', 'Doctor profile not found.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        $user->doctor->services()->create([
            'clinic_id' => $this->resolveClinic()->id,
            'name' => $validated['name'],
            'price' => $validated['price'],
        ]);

        return redirect()->back()->with('success', 'Service added successfully.');
    }

    public function updateBranding(Request $request)
    {
        $request->validate([
            'primary_color'  => 'nullable|regex:/^#[0-9a-fA-F]{6}$/',
            'clinic_phone'   => 'nullable|string|max:30',
            'clinic_address' => 'nullable|string|max:255',
            'clinic_logo'    => 'nullable|image|mimes:jpg,jpeg,png,svg,webp|max:2048',
        ]);

        $clinic = $this->resolveClinic();

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

        return redirect()->back()->with('success', 'Branding updated successfully.');
    }

    public function destroyService(\App\Models\core\DoctorService $service)
    {
        $user = auth()->user();
        if (!$user->doctor || $service->doctor_id !== $user->doctor->id) {
            abort(403);
        }

        $service->delete();

        return redirect()->back()->with('success', 'Service removed successfully.');
    }
}
