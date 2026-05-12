<?php

namespace App\Http\Controllers\Web\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\saas\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::query()->latest()->paginate(9);
        return view('admin.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.plans.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
            'max_patients' => 'required|integer|min:0',
            'max_appointments' => 'required|integer|min:0',
            'features' => 'nullable|string',
        ]);


        Plan::create($validated);

        return redirect()->route('admin.plans.index')->with('success', 'تم إنشاء الخطة بنجاح.');
    }

    public function edit(Plan $plan)
    {
        return view('admin.plans.edit', compact('plan'));
    }

    public function update(Request $request, Plan $plan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
            'max_patients' => 'required|integer|min:0',
            'max_appointments' => 'required|integer|min:0',
        ]);

        $plan->update($validated);

        return redirect()->route('admin.plans.index')->with('success', 'تم تحديث الخطة بنجاح.');
    }

    public function destroy(Plan $plan)
    {
        $plan->delete();
        return redirect()->route('admin.plans.index')->with('success', 'تم حذف الخطة.');
    }
}
