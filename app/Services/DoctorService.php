<?php

namespace App\Services;

use App\Models\User;
use App\Models\core\Doctor;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DoctorService
{
    
    public function list(array $filters): LengthAwarePaginator
    {
        $query = Doctor::query()
            ->select('id', 'user_id', 'name', 'email', 'phone', 'address', 'governorate', 'city', 'specialty', 'status');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['specialty'])) {
            $query->where('specialty', $filters['specialty']);
        }

        if (!empty($filters['governorate'])) {
            $query->where('governorate', $filters['governorate']);
        }

        if (!empty($filters['city'])) {
            $query->where('city', $filters['city']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('email', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('phone', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->paginate(10);
    }

    public function store(array $data): Doctor
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 'doctor',
            ]);

            return Doctor::create(array_merge($data, [
                'user_id' => $user->id
            ]));
        });
    }

    
    public function show(int $id): Doctor
    {
        return Doctor::findOrFail($id);
    }

    
    public function update(int $id, array $data): Doctor
    {
        $doctor = Doctor::findOrFail($id);
        
        DB::transaction(function () use ($doctor, $data) {
            $doctor->update($data);
            
            if (isset($data['name']) || isset($data['email'])) {
                $doctor->user()->update(array_filter([
                    'name' => $data['name'] ?? null,
                    'email' => $data['email'] ?? null,
                ]));
            }
        });

        return $doctor;
    }

    /**
     * Delete a doctor and their user account.
     */
    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $doctor = Doctor::findOrFail($id);
            $user = $doctor->user;
            
            $doctor->delete();
            if ($user) {
                $user->delete();
            }
            
            return true;
        });
    }
}
