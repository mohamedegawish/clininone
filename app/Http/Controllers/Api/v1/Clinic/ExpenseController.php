<?php

namespace App\Http\Controllers\Api\v1\Clinic;

use App\Http\Controllers\Controller;
use App\Models\core\Expense;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Resources\ExpenseResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ExpenseController extends Controller
{
    use HttpResponses, AuthorizesRequests;

    public function index(Request $request): JsonResponse
    {
        $clinicId = $this->resolveClinicId($request);

        if (!$clinicId) {
            return $this->error('Clinic could not be determined', 400);
        }

        $expenses = Expense::where('clinic_id', $clinicId)
            ->when($request->filled('date'), function ($query) use ($request) {
                return $query->whereDate('date', $request->date);
            })
            ->orderBy('date', 'desc')
            ->get();

        return $this->success(ExpenseResource::collection($expenses));
    }

    public function store(Request $request): JsonResponse
    {
        $clinicId = $this->resolveClinicId($request);

        if (!$clinicId) {
            return $this->error('Clinic could not be determined', 400);
        }

        $validated = $request->validate([
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $expense = Expense::create([
            ...$validated,
            'clinic_id' => $clinicId,
            'doctor_id' => $request->user()->doctor?->id,
        ]);

        return $this->success(new ExpenseResource($expense), 'Expense recorded successfully', 201);
    }

    public function show(int $id, Request $request): JsonResponse
    {
        $clinicId = $this->resolveClinicId($request);
        $expense = Expense::where('id', $id)->where('clinic_id', $clinicId)->firstOrFail();

        return $this->success(new ExpenseResource($expense));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $clinicId = $this->resolveClinicId($request);
        $expense = Expense::where('id', $id)->where('clinic_id', $clinicId)->firstOrFail();

        $validated = $request->validate([
            'category' => 'sometimes|required|string|max:255',
            'amount' => 'sometimes|required|numeric|min:0',
            'date' => 'sometimes|required|date',
            'description' => 'nullable|string',
        ]);

        $expense->update($validated);

        return $this->success(new ExpenseResource($expense), 'Expense updated successfully');
    }

    public function destroy(int $id, Request $request): JsonResponse
    {
        $clinicId = $this->resolveClinicId($request);
        $expense = Expense::where('id', $id)->where('clinic_id', $clinicId)->firstOrFail();

        $expense->delete();

        return $this->success(null, 'Expense deleted successfully');
    }

    private function resolveClinicId(Request $request): ?int
    {
        $user = $request->user();

        return $user?->clinic_id
            ?? $user?->doctor?->clinics()->value('clinics.id');
    }
}
