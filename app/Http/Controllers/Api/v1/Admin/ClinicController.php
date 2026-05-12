<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Models\core\Clinic;
use App\Services\ClinicService;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class ClinicController extends Controller
{
    use HttpResponses;

    public function __construct(protected ClinicService $service)
    {
    }

    /**
     * Display a listing of clinics.
     */
    public function index(): JsonResponse
    {
        $clinics = Clinic::query()
            ->select('id', 'name', 'address', 'created_at')
            ->paginate(10);

        return $this->success($clinics);
    }

    /**
     * Store a newly created clinic.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'doctor_id' => 'required|exists:doctors,id',
        ]);

        $clinic = $this->service->store(
            $request->only(['name', 'address']),
            $validated['doctor_id']
        );

        return $this->success($clinic, 'Clinic created successfully and doctor assigned as admin.', 201);
    }

    /**
     * Display the specified clinic.
     */
    public function show(int $id): JsonResponse
    {
        $clinic = Clinic::with(['doctors' => function ($q) {
            $q->select('doctors.id', 'doctors.name')->withPivot('role');
        }])->findOrFail($id);

        return $this->success($clinic);
    }

   
    public function update(Request $request, int $id): JsonResponse
    {
        $clinic = Clinic::findOrFail($id);
        $user = $request->user();

        if (!in_array($user->role, ['super_admin', 'admin']) && (!$user->doctor || !$user->doctor->isClinicAdmin($id))) {
            return $this->error('Unauthorized. Only Super Admin or Clinic Admin can update.', 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'address' => 'sometimes|string|max:255',
        ]);

        $clinic->update($validated);

        return $this->success($clinic, 'Clinic updated successfully.');
    }

    
    public function destroy(Request $request, int $id): JsonResponse
    {
        $clinic = Clinic::findOrFail($id);
        $user = $request->user();

        if (!in_array($user->role, ['super_admin', 'admin']) && (!$user->doctor || !$user->doctor->isClinicAdmin($id))) {
            return $this->error('Unauthorized. Only Super Admin or Clinic Admin can delete.', 403);
        }

        $clinic->delete();

        return $this->success(null, 'Clinic deleted successfully.');
    }
}
