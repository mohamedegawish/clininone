<?php

namespace App\Http\Controllers\Web\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\core\Clinic;
use App\Models\saas\Plan;
use App\Models\saas\Subscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function index(): View
    {
        $subscriptions = Subscription::query()
            ->with(['clinic:id,name', 'plan:id,name,price'])
            ->latest()
            ->paginate(12);

        $expiringSoonCount = Subscription::query()
            ->where('status', Subscription::STATUS_ACTIVE)
            ->whereDate('end_at', '<=', now()->addDays(7))
            ->count();

        $expiredCount = Subscription::query()
            ->whereDate('end_at', '<', now())
            ->count();

        return view('admin.subscriptions.index', compact('subscriptions', 'expiringSoonCount', 'expiredCount'));
    }

    public function create(): View
    {
        $clinics = Clinic::query()->latest()->get(['id', 'name']);
        $plans = Plan::query()->latest()->get(['id', 'name', 'price', 'duration']);

        return view('admin.subscriptions.create', compact('clinics', 'plans'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'clinic_id' => 'required|exists:clinics,id',
            'plan_id' => 'required|exists:plans,id',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
            'status' => 'required|in:pending,active,expired,cancelled',
            'auto_renew' => 'nullable|boolean',
        ]);

        Subscription::create([
            ...$validated,
            'auto_renew' => (bool) ($validated['auto_renew'] ?? false),
        ]);

        return redirect()->route('admin.subscriptions.index')->with('success', 'تم إنشاء الاشتراك بنجاح.');
    }
}
