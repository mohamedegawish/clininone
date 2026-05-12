<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Mail\OtpMail;
use App\Traits\HttpResponses;

class AuthController extends Controller
{
    use HttpResponses;

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role'     => 'required|in:patient,doctor,admin'
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
            'role'     => $request->role
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        return $this->success(['user' => $user, 'token' => $token], 'Registration successful');
    }

    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->error('Invalid credentials', 401);
        }

        $tokens = $this->createTokens($user);

        return $this->success([
            'user' => $user,
            ...$tokens
        ], 'Login successful');
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success(null, 'Logged out');
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $existing = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if ($existing && Carbon::parse($existing->created_at)->addMinutes(1)->isFuture()) {
            return $this->error('Try again later', 429);
        }

        $otp = rand(100000, 999999);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'otp'        => $otp,
                'created_at' => now()
            ]
        );

        Mail::to($request->email)->send(new OtpMail($otp));

        return $this->success(null, 'OTP sent to email successfully');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'otp'      => 'required',
            'password' => 'required|min:6'
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('otp', $request->otp)
            ->first();

        if (!$record) {
            return $this->error('Invalid OTP', 400);
        }

        if (Carbon::parse($record->created_at)->addMinutes(10)->isPast()) {
            return $this->error('OTP expired', 400);
        }

        User::where('email', $request->email)->update([
            'password' => bcrypt($request->password)
        ]);

        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        return $this->success(null, 'Password updated');
    }
    private function createTokens($user)
    {
        $accessToken = $user->createToken('access-token')->plainTextToken;

        $accessTokenModel = $user->tokens()->latest()->first();
        $accessTokenModel->update([
            'expires_at' => now()->addMinutes(15)
        ]);

        $refreshToken = $user->createToken('refresh-token')->plainTextToken;

        $refreshTokenModel = $user->tokens()->latest()->first();
        $refreshTokenModel->update([
            'expires_at' => now()->addDays(7)
        ]);

        return [
            'access_token'  => $accessToken,
            'refresh_token' => $refreshToken,
        ];
    }
    public function refreshToken(Request $request)
    {
        $request->validate([
            'refresh_token' => 'required'
        ]);

        $parts = explode('|', $request->refresh_token);

        if (count($parts) < 2) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid refresh token format'
            ], 401);
        }

        $token = DB::table('personal_access_tokens')
            ->where('token', hash('sha256', $parts[1]))
            ->first();

        if (!$token) {
            return response()->json(['message' => 'Invalid refresh token'], 401);
        }

        if (Carbon::parse($token->expires_at)->isPast()) {
            return response()->json(['message' => 'Refresh token expired'], 401);
        }

        $user = User::find($token->tokenable_id);

        DB::table('personal_access_tokens')
            ->where('id', $token->id)
            ->delete();

        $tokens = $this->createTokens($user);

        return response()->json($tokens);
    }
}
