<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\System\Company;
use App\Models\System\Plan;
use App\Models\Core\User;
use App\Models\System\Subscription;
use App\Models\System\SubscriptionInvoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Key Metrics
        $totalCompanies = Company::count();
        $totalUsers = User::count();
        $activePlansCount = Plan::where('is_active', true)->count();
        
        // Calculate MRR (Monthly Recurring Revenue)
        // Roughly summing up active subscriptions plan prices
        $mrr = DB::table('subscriptions')
            ->join('plans', 'subscriptions.plan_id', '=', 'plans.id')
            ->where('subscriptions.status', 'active')
            ->sum('plans.price_monthly');

        // 2. Recent Companies (last 5)
        $recentCompanies = Company::with(['subscriptions.plan'])
            ->latest()
            ->take(5)
            ->get();

        // 3. Plan Distribution Chart Data (Donut Chart)
        $planDistributionRaw = DB::table('subscriptions')
            ->join('plans', 'subscriptions.plan_id', '=', 'plans.id')
            ->select('plans.name', DB::raw('count(*) as count'))
            ->where('subscriptions.status', 'active')
            ->groupBy('plans.id', 'plans.name')
            ->get();

        $planLabels = $planDistributionRaw->pluck('name');
        $planSeries = $planDistributionRaw->pluck('count');

        // 4. Revenue Over Time (Area Chart - Last 6 Months)
        $sixMonthsAgo = Carbon::now()->subMonths(5)->startOfMonth();
        
        // Use paid invoices for actual revenue collected
        $revenueRaw = SubscriptionInvoice::where('status', 'paid')
            ->where('created_at', '>=', $sixMonthsAgo)
            ->select(
                DB::raw('sum(amount) as total'),
                DB::raw("DATE_FORMAT(created_at, '%b %Y') as month"), // MySQL specific
                DB::raw("YEAR(created_at) as year, MONTH(created_at) as month_num")
            )
            ->groupBy('year', 'month_num', 'month')
            ->orderBy('year')
            ->orderBy('month_num')
            ->get();

        // Fill in missing months with 0
        $revenueLabels = [];
        $revenueSeries = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStr = Carbon::now()->subMonths($i)->format('M Y');
            $revenueLabels[] = $monthStr;
            
            $monthData = $revenueRaw->firstWhere('month', $monthStr);
            $revenueSeries[] = $monthData ? (float) $monthData->total : 0;
        }

        return view('superadmin.dashboard', compact(
            'totalCompanies',
            'totalUsers',
            'activePlansCount',
            'mrr',
            'recentCompanies',
            'planLabels',
            'planSeries',
            'revenueLabels',
            'revenueSeries'
        ));
    }
}
