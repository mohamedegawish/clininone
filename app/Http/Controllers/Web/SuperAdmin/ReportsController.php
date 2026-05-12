<?php

namespace App\Http\Controllers\Web\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\core\Clinic;
use App\Models\core\Appointment;
use App\Models\core\Doctor;
use App\Models\core\Patient;
use App\Models\saas\Subscription;
use App\Models\saas\Plan;
use App\Models\saas\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        // 1. Financial & Revenue
        $financialReports = [
            'total_revenue' => Subscription::join('plans', 'subscriptions.plan_id', '=', 'plans.id')
                ->whereIn('subscriptions.status', ['active', 'expired'])
                ->sum('plans.price'),
            
            'revenue_by_plan' => Subscription::join('plans', 'subscriptions.plan_id', '=', 'plans.id')
                ->select('plans.name', DB::raw('SUM(plans.price) as total'))
                ->groupBy('plans.name')
                ->get(),
            
            'mrr' => Subscription::join('plans', 'subscriptions.plan_id', '=', 'plans.id')
                ->where('subscriptions.status', 'active')
                ->sum('plans.price'),
        ];

        // 2. Subscriptions
        $subscriptionReports = [
            'status_breakdown' => Subscription::select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->get(),
            
            'expiring_soon' => Subscription::with(['clinic', 'plan'])
                ->where('status', 'active')
                ->where('end_at', '<=', Carbon::now()->addDays(30))
                ->where('end_at', '>', Carbon::now())
                ->get(),
            
            'plan_popularity' => Plan::withCount('subscriptions')
                ->get(),
        ];

        // 3. Clinics
        $clinicReports = [
            'status_stats' => Clinic::select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->get(),
            
            'growth_rate' => Clinic::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('count(*) as total')
            )
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get(),
        ];

        // 4. Usage & Limits
        $usageReports = [
            'approaching_limits' => Clinic::with(['activeSubscription.plan'])
                ->withCount(['patients', 'appointments'])
                ->get()
                ->filter(function($clinic) {
                    if (!$clinic->activeSubscription || !$clinic->activeSubscription->plan) return false;
                    $plan = $clinic->activeSubscription->plan;
                    $patientUsage = $plan->max_patients > 0 ? ($clinic->patients_count / $plan->max_patients) : 0;
                    $appointmentUsage = $plan->max_appointments > 0 ? ($clinic->appointments_count / $plan->max_appointments) : 0;
                    return $patientUsage > 0.8 || $appointmentUsage > 0.8;
                }),
            
            'system_totals' => [
                'doctors' => Doctor::count(),
                'patients' => Patient::count(),
                'appointments' => Appointment::count(),
            ],
            
            'peak_usage' => DB::table('usage_logs')
                ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('count(*) as total'))
                ->groupBy('hour')
                ->orderBy('total', 'desc')
                ->limit(5)
                ->get(),
        ];

        // 5. System Health
        $healthReports = [
            'failed_jobs' => DB::table('failed_jobs')->count(),
            'active_sessions' => DB::table('sessions')->where('last_activity', '>=', now()->subMinutes(15)->getTimestamp())->count(),
        ];

        return view('admin.reports.index', compact(
            'financialReports', 
            'subscriptionReports', 
            'clinicReports', 
            'usageReports', 
            'healthReports'
        ));
    }
}
