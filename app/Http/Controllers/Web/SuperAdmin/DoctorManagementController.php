<?php

namespace App\Http\Controllers\Web\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\core\Clinic;
use App\Models\core\Doctor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DoctorManagementController extends Controller
{
    public function index()
    {
        $doctors = Doctor::query()
            ->with(['user:id,name,email,role', 'clinics:id,name'])
            ->latest()
            ->paginate(12);
        return view('admin.doctors.index', compact('doctors'));
    }

    public function create()
    {
        $clinics = Clinic::query()->latest()->get(['id', 'name']);
        $specialties = array_values(\App\Services\SpecialtyService::getReverseMap());
        return view('admin.doctors.create', compact('clinics', 'specialties'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'arabic_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:30',
            'password' => 'required|string|min:8',
            'clinic_id' => 'required|exists:clinics,id',
            'gender' => ['required', Rule::in(['male', 'female'])],
            'specialty' => 'required|string|max:255',
            'experience_years' => 'nullable|integer|min:0|max:80',
            'price' => 'required|numeric|min:0',
            'photo' => 'nullable|image|max:2048',
        ]);

        $photoPath = $request->hasFile('photo')
            ? $request->file('photo')->store('doctors', 'public')
            : null;

        DB::transaction(function () use ($validated, $photoPath) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'doctor',
            ]);

            $doctor = Doctor::create([
                'user_id' => $user->id,
                'name' => $validated['name'],
                'arabic_name' => $validated['arabic_name'] ?? null,
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'specialty' => $validated['specialty'],
                'gender' => $validated['gender'],
                'experience_years' => $validated['experience_years'] ?? 0,
                'price' => $validated['price'],
                'status' => 'active',
                'photo_path' => $photoPath,
            ]);

            $doctor->clinics()->attach($validated['clinic_id'], ['role' => 'doctor']);
        });

        return redirect()->route('admin.doctors.index')->with('success', 'تم إنشاء الطبيب وربطه بالعيادة بنجاح.');
    }

    public function destroy(Doctor $doctor)
    {
        $doctor->user->delete(); // This should cascade if set up, or handle manually
        $doctor->delete();
        return redirect()->route('admin.doctors.index')->with('success', 'تم حذف الطبيب.');
    }
}
