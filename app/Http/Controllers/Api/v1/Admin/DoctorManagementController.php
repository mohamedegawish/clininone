<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDoctorRequest;
use App\Http\Requests\UpdateDoctorRequest;
use App\Http\Requests\FilterDoctorRequest;
use App\Http\Resources\DoctorResource;
use App\Models\core\Doctor;
use App\Services\DoctorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DoctorManagementController extends Controller
{
    public function __construct(protected DoctorService $service)
    {
    }

    public function index(FilterDoctorRequest $request): AnonymousResourceCollection
    {
        $doctors = $this->service->list($request->validated());

        return DoctorResource::collection(
            Doctor::with('clinics')->withCount('clinics')->get()
        );
    }

    public function store(StoreDoctorRequest $request): DoctorResource
    {
        $doctor = $this->service->store($request->validated());

        return new DoctorResource($doctor);
    }

    public function show(int $id): DoctorResource
    {
        $doctor = $this->service->show($id);

        return new DoctorResource($doctor);
    }


    public function update(UpdateDoctorRequest $request, int $id): DoctorResource
    {
        $doctor = $this->service->update($id, $request->validated());

        return new DoctorResource($doctor);
    }

    
    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);

        return response()->json([
            'message' => 'Doctor deleted successfully.'
        ]);
    }
}
