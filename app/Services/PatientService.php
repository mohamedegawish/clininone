<?php

namespace App\Services;

use App\Models\core\Patient;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PatientService
{
    /**
     * List patients with filtering, searching, and sorting.
     */
    public function list(array $filters, int $clinicId): LengthAwarePaginator
    {
        $query = Patient::where('clinic_id', $clinicId);

        // Search name, email, phone
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('full_name', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('english_name', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('email', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('phone', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('ssn', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('card_no', 'like', '%' . $filters['search'] . '%');
            });
        }

        // Filtering by status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filtering by birth_date range
        if (!empty($filters['dob_from'])) {
            $query->whereDate('birth_date', '>=', $filters['dob_from']);
        }
        if (!empty($filters['dob_to'])) {
            $query->whereDate('birth_date', '<=', $filters['dob_to']);
        }

        // Filtering by created_at range
        if (!empty($filters['created_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_from']);
        }
        if (!empty($filters['created_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_to']);
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        
        $allowedSorts = ['full_name', 'created_at', 'birth_date'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        return $query->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Store a new patient.
     */
    public function store(array $data, int $clinicId): Patient
    {
        return DB::transaction(function () use ($data, $clinicId) {
            return Patient::create(array_merge($data, [
                'clinic_id' => $clinicId
            ]));
        });
    }

    /**
     * Show a patient.
     */
    public function show(int $id, int $clinicId): Patient
    {
        return Patient::where('clinic_id', $clinicId)->with('clinic')->findOrFail($id);
    }

    /**
     * Update an existing patient.
     */
    public function update(Patient $patient, array $data): Patient
    {
        return DB::transaction(function () use ($patient, $data) {
            $patient->update($data);
            return $patient;
        });
    }

    /**
     * Delete a patient (soft delete).
     */
    public function delete(Patient $patient): bool
    {
        return $patient->delete();
    }
}
