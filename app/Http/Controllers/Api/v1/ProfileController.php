<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    use HttpResponses;

    /**
     * GET /api/profile
     * Return the authenticated user + doctor profile.
     */
    public function show(Request $request): JsonResponse
    {
        $user   = $request->user();
        $doctor = $user->doctor;

        return $this->success([
            'user' => [
                'id'        => $user->id,
                'name'      => $user->name,
                'email'     => $user->email,
                'role'      => $user->role,
                'clinic_id' => $user->clinic_id,
            ],
            'doctor' => $doctor ? [
                'id'               => $doctor->id,
                'name'             => $doctor->name,
                'arabic_name'      => $doctor->arabic_name,
                'email'            => $doctor->email,
                'phone'            => $doctor->phone,
                'specialty'        => $doctor->specialty,
                'gender'           => $doctor->gender,
                'experience_years' => $doctor->experience_years,
                'qualification'    => $doctor->qualification,
                'bio'              => $doctor->bio,
                'price'            => (float) $doctor->price,
                'followup_price'   => (float) $doctor->followup_price,
                'photo'            => $doctor->photo_path
                    ? asset('storage/' . $doctor->photo_path)
                    : null,
                'governorate'      => $doctor->governorate,
                'city'             => $doctor->city,
                'address'          => $doctor->address,
            ] : null,
        ]);
    }

    /**
     * PUT /api/profile
     * Update authenticated user's name, email, and doctor profile fields.
     */
    public function update(Request $request): JsonResponse
    {
        $user   = $request->user();
        $doctor = $user->doctor;

        $validated = $request->validate([
            'name'             => 'sometimes|string|max:255',
            'email'            => ['sometimes', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            // Doctor fields (only if the user is a doctor)
            'specialty'        => 'sometimes|string|max:100',
            'phone'            => 'sometimes|string|max:30',
            'bio'              => 'sometimes|nullable|string',
            'qualification'    => 'sometimes|nullable|string|max:255',
            'experience_years' => 'sometimes|nullable|integer|min:0',
            'governorate'      => 'sometimes|nullable|string|max:100',
            'city'             => 'sometimes|nullable|string|max:100',
            'address'          => 'sometimes|nullable|string',
        ]);

        // Update user model
        $userFields = array_filter([
            'name'  => $validated['name'] ?? null,
            'email' => $validated['email'] ?? null,
        ]);
        if ($userFields) {
            $user->update($userFields);
        }

        // Update doctor model
        if ($doctor) {
            $doctorFields = array_intersect_key($validated, array_flip([
                'specialty', 'phone', 'bio', 'qualification',
                'experience_years', 'governorate', 'city', 'address',
            ]));
            if ($doctorFields) {
                $doctor->update($doctorFields);
            }
        }

        return $this->success(null, 'Profile updated successfully.');
    }

    /**
     * POST /api/profile/photo
     * Upload/replace doctor profile photo.
     */
    public function updatePhoto(Request $request): JsonResponse
    {
        $doctor = $request->user()?->doctor;

        if (! $doctor) {
            return $this->error('Doctor profile not found.', 403);
        }

        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Delete old photo
        if ($doctor->photo_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($doctor->photo_path);
        }

        $path = $request->file('photo')->store('doctors/photos', 'public');
        $doctor->update(['photo_path' => $path]);

        return $this->success([
            'photo' => asset('storage/' . $path),
        ], 'Photo updated.');
    }

    /**
     * PUT /api/profile/password
     * Change password (requires current_password verification).
     */
    public function changePassword(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'current_password' => 'required|string',
            'new_password'     => 'required|string|min:8|confirmed',
        ]);

        if (! \Illuminate\Support\Facades\Hash::check($request->current_password, $user->password)) {
            return $this->error('Current password is incorrect.', 422);
        }

        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->new_password),
        ]);

        return $this->success(null, 'Password changed successfully.');
    }
}
