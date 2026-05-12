<?php

namespace App\Http\Controllers\Api\v1\Clinic;

use App\Http\Controllers\Controller;
use App\Models\core\FavoriteMedication;
use App\Models\core\Medication;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MedicationController extends Controller
{
    use HttpResponses;

    /**
     * GET /clinic/medications/search?q={term}
     * Search global medication library, favorite & own items appear first.
     */
    public function search(Request $request): JsonResponse
    {
        $doctor = $request->user()?->doctor;

        if (! $doctor) {
            return $this->error('Doctor profile not found for this user.', 403);
        }

        $search = trim($request->get('q', $request->get('search', '')));

        $favoriteIds = FavoriteMedication::where('doctor_id', $doctor->id)
            ->pluck('medication_id')
            ->all();

        $medications = Medication::when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->limit(30)
            ->get()
            ->map(fn ($m) => [
                'id'                   => $m->id,
                'name'                 => $m->name,
                'generic'              => $m->generic,
                'default_dosage'       => $m->default_dosage,
                'default_frequency'    => $m->default_frequency,
                'default_route'        => $m->default_route,
                'default_duration'     => $m->default_duration,
                'default_instructions' => $m->default_instructions,
                'is_favorite'          => in_array($m->id, $favoriteIds),
                'is_mine'              => $m->created_by === $doctor->id,
            ])
            ->sortByDesc(fn ($m) => ($m['is_favorite'] ? 2 : 0) + ($m['is_mine'] ? 1 : 0))
            ->values();

        return $this->success($medications);
    }

    /**
     * POST /clinic/medications
     * Create or find a medication by name.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'                 => 'required|string|max:255',
            'generic'              => 'nullable|string|max:255',
            'default_dosage'       => 'nullable|string|max:100',
            'default_frequency'    => 'nullable|string|max:100',
            'default_route'        => 'nullable|string|max:100',
            'default_duration'     => 'nullable|string|max:100',
            'default_instructions' => 'nullable|string',
        ]);

        $doctor = $request->user()?->doctor;

        if (! $doctor) {
            return $this->error('Doctor profile not found for this user.', 403);
        }

        $medication = Medication::firstOrCreate(
            ['name' => trim($validated['name'])],
            array_merge(['created_by' => $doctor->id], array_filter([
                'generic'              => $validated['generic'] ?? null,
                'default_dosage'       => $validated['default_dosage'] ?? null,
                'default_frequency'    => $validated['default_frequency'] ?? null,
                'default_route'        => $validated['default_route'] ?? null,
                'default_duration'     => $validated['default_duration'] ?? null,
                'default_instructions' => $validated['default_instructions'] ?? null,
            ], fn ($v) => $v !== null))
        );

        // Update defaults if medication already exists and belongs to this doctor
        if (!$medication->wasRecentlyCreated && $medication->created_by === $doctor->id) {
            $medication->update(array_filter([
                'generic'              => $validated['generic'] ?? null,
                'default_dosage'       => $validated['default_dosage'] ?? null,
                'default_frequency'    => $validated['default_frequency'] ?? null,
                'default_route'        => $validated['default_route'] ?? null,
                'default_duration'     => $validated['default_duration'] ?? null,
                'default_instructions' => $validated['default_instructions'] ?? null,
            ], fn ($v) => $v !== null));
        }

        return $this->success([
            'id'                   => $medication->id,
            'name'                 => $medication->name,
            'generic'              => $medication->generic,
            'default_dosage'       => $medication->default_dosage,
            'default_frequency'    => $medication->default_frequency,
            'default_route'        => $medication->default_route,
            'default_duration'     => $medication->default_duration,
            'default_instructions' => $medication->default_instructions,
            'is_favorite'          => false,
            'is_mine'              => $medication->created_by === $doctor->id,
        ], 'Medication saved.', 201);
    }

    /**
     * POST /clinic/medications/{medication}/favorite
     * Toggle favorite status for the authenticated doctor.
     */
    public function toggleFavorite(int $medicationId, Request $request): JsonResponse
    {
        $doctor = $request->user()?->doctor;

        if (! $doctor) {
            return $this->error('Doctor profile not found for this user.', 403);
        }

        $medication = Medication::findOrFail($medicationId);

        $existing = FavoriteMedication::where('doctor_id', $doctor->id)
            ->where('medication_id', $medication->id)
            ->first();

        if ($existing) {
            $existing->delete();
            return $this->success(['is_favorite' => false], 'Removed from favorites.');
        }

        FavoriteMedication::create([
            'doctor_id'     => $doctor->id,
            'medication_id' => $medication->id,
        ]);

        return $this->success(['is_favorite' => true], 'Added to favorites.');
    }
}
