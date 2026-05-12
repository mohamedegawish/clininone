<?php

namespace App\Http\Controllers\Web\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\core\Appointment;
use App\Models\core\Clinic;
use App\Models\core\Doctor;
use App\Models\core\Patient;
use App\Models\saas\Subscription;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Heavy aggregates cached for 5 minutes — safe for a dashboard overview
        $stats = Cache::remember('admin.dashboard.stats', 300, function () {
            return [
                'total_revenue' => Subscription::query()
                    ->join('plans', 'plans.id', '=', 'subscriptions.plan_id')
                    ->sum('plans.price'),
                'active_clinics' => Clinic::where('status', 'active')
                    ->whereHas('activeSubscription')
                    ->count(),
                'total_patients'      => Patient::count(),
                'todays_appointments' => Appointment::whereDate('appointment_date', Carbon::today())->count(),
            ];
        });

        $chartsData = Cache::remember('admin.dashboard.charts', 300, function () {
            $clinicsGrowth = Clinic::query()
                ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month_key")
                ->selectRaw('COUNT(*) as total')
                ->groupBy('month_key')
                ->orderBy('month_key')
                ->limit(12)
                ->get();

            $subscriptionDistribution = Subscription::query()
                ->join('plans', 'plans.id', '=', 'subscriptions.plan_id')
                ->select('plans.name', DB::raw('count(*) as total'))
                ->where('subscriptions.status', 'active')
                ->groupBy('plans.name')
                ->get();

            $revenueByMonth = Subscription::query()
                ->join('plans', 'plans.id', '=', 'subscriptions.plan_id')
                ->selectRaw("DATE_FORMAT(subscriptions.start_at, '%Y-%m') as month_key")
                ->selectRaw('SUM(plans.price) as total_revenue')
                ->groupBy('month_key')
                ->orderBy('month_key')
                ->limit(12)
                ->get();

            return [
                'clinicsGrowth' => [
                    'labels' => $clinicsGrowth->pluck('month_key')->all(),
                    'data'   => $clinicsGrowth->pluck('total')->all(),
                ],
                'subDistribution' => [
                    'labels' => $subscriptionDistribution->pluck('name')->all(),
                    'data'   => $subscriptionDistribution->pluck('total')->all(),
                ],
                'monthlyRevenue' => [
                    'labels' => $revenueByMonth->pluck('month_key')->all(),
                    'data'   => $revenueByMonth->pluck('total_revenue')->map(fn ($v) => (float) $v)->all(),
                ],
            ];
        });

        $doctorInsights = Cache::remember('admin.dashboard.doctors', 300, function () {
            return [
                'top_rated' => DB::table('ratings')
                    ->join('doctors', 'ratings.doctor_id', '=', 'doctors.id')
                    ->select('doctors.name', 'doctors.specialty', DB::raw('AVG(ratings.rating) as avg_rating'), DB::raw('COUNT(ratings.id) as reviews_count'))
                    ->groupBy('doctors.id', 'doctors.name', 'doctors.specialty')
                    ->having('reviews_count', '>', 0)
                    ->orderByDesc('avg_rating')
                    ->limit(5)
                    ->get(),
                'top_specialties' => Doctor::query()
                    ->select('specialty', DB::raw('count(*) as total'))
                    ->whereNotNull('specialty')
                    ->groupBy('specialty')
                    ->orderByDesc('total')
                    ->limit(5)
                    ->get(),
            ];
        });

        // Alerts and activity are intentionally not cached — they need to be current
        $alerts = [
            'recent_clinics' => Clinic::with(['activeSubscription.plan'])->latest()->limit(5)->get(),
            'expiring_subscriptions' => Subscription::with(['clinic', 'plan'])
                ->where('status', 'active')
                ->where('end_at', '<=', Carbon::now()->addDays(7))
                ->where('end_at', '>', Carbon::now())
                ->get(),
            'system_failures' => DB::table('failed_jobs')->count(),
        ];

        $activity = [
            'active_sessions' => DB::table('sessions')->where('last_activity', '>=', now()->subMinutes(15)->getTimestamp())->count(),
            'latest_confirmed_appointments' => Appointment::with(['patient:id,full_name', 'doctor:id,name', 'clinic:id,name'])
                ->where('status', 'confirmed')
                ->latest()
                ->limit(5)
                ->get(),
        ];

        return view('admin.dashboard', compact('stats', 'chartsData', 'alerts', 'activity', 'doctorInsights'));
    }
}
