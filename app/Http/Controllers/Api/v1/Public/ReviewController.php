<?php

namespace App\Http\Controllers\Api\v1\Public;

use App\Http\Controllers\Controller;
use App\Models\core\Doctor;
use App\Models\core\Patient;
use App\Models\core\Rating;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    use HttpResponses;

    /**
     * List reviews for a specific doctor.
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'doctor_id' => 'required|integer|exists:doctors,id',
        ]);

        $reviews = Rating::where('doctor_id', $request->doctor_id)
            ->with('patient:id,full_name') // Include reviewer name
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return $this->success($reviews);
    }

    /**
     * Store a new review. We require a phone number to bind or create a patient profile.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'doctor_id' => 'required|integer|exists:doctors,id',
            'rating' => 'required|integer|min:1|max:5',
            'reviewer_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'comment' => 'nullable|string|max:1000',
        ]);

        // Find doctor to get clinic context (simplified: just take first clinic for patient profile if anonymous)
        $doctor = Doctor::findOrFail($validated['doctor_id']);
        $clinicId = $doctor->clinics()->first()?->id;

        if (!$clinicId) {
            return $this->error('The selected doctor is not assigned to any clinic.', 400);
        }

        // Find or create patient
        $patient = Patient::firstOrCreate(
            [
                'phone' => $validated['phone'],
                'clinic_id' => $clinicId,
            ],
            [
                'full_name' => $validated['reviewer_name'],
            ]
        );

        $review = Rating::create([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
        ]);

        return $this->success($review->load('patient:id,full_name'), 'Review submitted successfully.', 201);
    }
}
