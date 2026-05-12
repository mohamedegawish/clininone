<?php

namespace App\Http\Controllers\Web\Clinic;

use App\Http\Controllers\Controller;
use App\Models\core\FavoriteMedication;
use App\Models\core\Medication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MedicationController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $doctor   = auth()->user()->doctor;
        $search   = trim($request->get('search', ''));

        $favoriteIds = FavoriteMedication::where('doctor_id', $doctor->id)
            ->pluck('medication_id')
            ->all();

        $medications = Medication::where('name', 'like', "%{$search}%")
            ->limit(30)
            ->get()
            ->map(fn($m) => [
                'id'          => $m->id,
                'name'        => $m->name,
                'is_favorite' => in_array($m->id, $favoriteIds),
                'is_mine'     => $m->created_by === $doctor->id,
            ])
            ->sortByDesc(fn($m) => ($m['is_favorite'] ? 2 : 0) + ($m['is_mine'] ? 1 : 0))
            ->values();

        return response()->json($medications);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate(['name' => 'required|string|max:255']);

        $doctor     = auth()->user()->doctor;
        $medication = Medication::firstOrCreate(
            ['name' => trim($request->name)],
            ['created_by' => $doctor->id]
        );

        return response()->json([
            'id'          => $medication->id,
            'name'        => $medication->name,
            'is_favorite' => false,
            'is_mine'     => $medication->created_by === $doctor->id,
        ]);
    }

    public function toggleFavorite(Medication $medication): JsonResponse
    {
        $doctorId = auth()->user()->doctor->id;

        $existing = FavoriteMedication::where('doctor_id', $doctorId)
            ->where('medication_id', $medication->id)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['is_favorite' => false]);
        }

        FavoriteMedication::create([
            'doctor_id'     => $doctorId,
            'medication_id' => $medication->id,
        ]);

        return response()->json(['is_favorite' => true]);
    }
}
