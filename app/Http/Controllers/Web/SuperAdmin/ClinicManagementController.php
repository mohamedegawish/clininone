<?php

namespace App\Http\Controllers\Web\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\core\Appointment;
use App\Models\core\Clinic;
use App\Models\saas\Plan;
use App\Models\saas\Subscription;
use Illuminate\Http\Request;

class ClinicManagementController extends Controller
{
    public function index()
    {
        $clinics = Clinic::query()
            ->with(['activeSubscription.plan'])
            ->withCount(['doctors', 'patients', 'appointments'])
            ->latest()
            ->paginate(10);

        return view('admin.clinics.index', compact('clinics'));
    }

    public function show(Clinic $clinic)
    {
        $clinic->load(['activeSubscription.plan', 'subscriptions.plan']);
        $clinic->loadCount(['doctors', 'patients', 'appointments']);

        // 1. Admin User
        $adminDoctor = $clinic->doctors()->wherePivot('role', 'admin')->first();
        $adminUser = $adminDoctor ? $adminDoctor->user : null;

        // 3. Usage vs Limits (counts pre-loaded above)
        $stats = [
            'doctors_count'      => $clinic->doctors_count,
            'patients_count'     => $clinic->patients_count,
            'appointments_count' => $clinic->appointments_count,
            'revenue' => Subscription::query()
                ->where('clinic_id', $clinic->id)
                ->join('plans', 'plans.id', '=', 'subscriptions.plan_id')
                ->sum('plans.price'),
        ];

        // 4. Medical Staff
        $doctors = $clinic->doctors()->with('schedules')->get();

        // 5. Operational Insights
        $appointmentStatus = \Illuminate\Support\Facades\DB::table('appointments')
            ->where('clinic_id', $clinic->id)
            ->select('status', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        $averageRating = \Illuminate\Support\Facades\DB::table('ratings')
            ->join('clinic_doctor', 'ratings.doctor_id', '=', 'clinic_doctor.doctor_id')
            ->where('clinic_doctor.clinic_id', $clinic->id)
            ->avg('rating');

        // Billing history (mocked or from subscriptions since payments/invoices lack columns currently)
        $billingHistory = $clinic->subscriptions()->with('plan')->orderBy('created_at', 'desc')->get();

        return view('admin.clinics.show', compact(
            'clinic', 
            'adminUser', 
            'stats', 
            'doctors', 
            'appointmentStatus', 
            'averageRating',
            'billingHistory'
        ));
    }

    public function create()
    {
        return view('admin.clinics.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $clinic = Clinic::create($validated);

        return redirect()->route('admin.clinics.index')->with('success', 'تم إنشاء العيادة بنجاح.');
    }

    public function edit(Clinic $clinic)
    {
        return view('admin.clinics.edit', compact('clinic'));
    }

    public function update(Request $request, Clinic $clinic)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $clinic->update($validated);

        return redirect()->route('admin.clinics.index')->with('success', 'تم تحديث بيانات العيادة بنجاح.');
    }

    public function toggleStatus(Clinic $clinic)
    {
        $clinic->status = $clinic->status === 'active' ? 'inactive' : 'active';
        $clinic->save();

        return back()->with('success', 'تم تحديث حالة العيادة.');
    }

    public function assignPlanForm(Clinic $clinic)
    {
        $plans = \Illuminate\Support\Facades\Cache::rememberForever('plans.all', fn () => Plan::all());
        return view('admin.clinics.assign-plan', compact('clinic', 'plans'));
    }

    public function assignPlan(Request $request, Clinic $clinic)
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
        ]);

        $clinic->subscriptions()->create([
            'plan_id' => $validated['plan_id'],
            'start_at' => $validated['start_at'],
            'end_at' => $validated['end_at'],
            'status' => 'active',
        ]);

        return redirect()->route('admin.clinics.index')->with('success', 'تم تعيين الخطة بنجاح.');
    }

    public function destroy(Clinic $clinic)
    {
        $clinic->delete();
        return redirect()->route('admin.clinics.index')->with('success', 'تم حذف العيادة.');
    }
}
