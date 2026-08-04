<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Business\Purchase;
use App\Models\Business\Sale;
use App\Models\Business\GrainStock;
use App\Models\Business\Expense;
use App\Models\Core\User;
use App\Models\Business\LedgerEntry;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $companyId = auth()->user()->company_id;
        
        $period = $request->input('period', 'today');
        if ($period == 'this_month') {
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
        } elseif ($period == 'custom' && $request->start_date && $request->end_date) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
        } else {
            // Default to today
            $startDate = Carbon::today();
            $endDate = Carbon::today()->endOfDay();
        }

        // 1. Period Purchases
        $periodPurchases = Purchase::where('company_id', $companyId)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->sum('total_amount');

        // 2. Period Sales
        $periodSales = Sale::where('company_id', $companyId)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->sum('total_amount');

        // 3. Current Stock (Total units across all grains)
        $currentStock = GrainStock::where('company_id', $companyId)->sum('quantity');

        // 4 & 5. Total Receivable and Total Payable
        $parties = User::where('company_id', $companyId)->where('role', 'party')->get();
        
        $totalReceivable = 0;
        $totalPayable = 0;

        foreach ($parties as $party) {
            $lastEntry = LedgerEntry::where('company_id', $companyId)
                ->where('party_id', $party->id)
                ->latest('id')
                ->first();
                
            if ($lastEntry) {
                if ($lastEntry->balance_after > 0) {
                    $totalPayable += $lastEntry->balance_after;
                } elseif ($lastEntry->balance_after < 0) {
                    $totalReceivable += abs($lastEntry->balance_after);
                }
            }
        }

        // 6. Period Profit
        $periodSalesRecords = Sale::with(['saleLotAllocations.lot', 'brokerCommission'])
            ->where('company_id', $companyId)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get();
            
        $periodProfit = 0;
        foreach ($periodSalesRecords as $sale) {
            $cogs = 0;
            foreach ($sale->saleLotAllocations as $allocation) {
                $cogs += ($allocation->quantity_taken * $allocation->cost_rate);
            }
            $grossProfit = $sale->total_amount - $cogs;
            $brokerCommission = $sale->brokerCommission ? $sale->brokerCommission->commission_amount : 0;
            $periodProfit += ($grossProfit - $brokerCommission);
        }

        // 6b. Expense stats
        $periodExpenses = Expense::where('company_id', $companyId)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->sum('amount');
            
        $monthStart = Carbon::now()->startOfMonth();
        $monthExpenses = Expense::where('company_id', $companyId)
            ->whereBetween('date', [$monthStart, Carbon::now()])->sum('amount');
        $recentExpenses = Expense::with('category')
            ->where('company_id', $companyId)
            ->orderByDesc('date')->orderByDesc('id')->limit(3)->get();
            
        // Deduct period expenses from period profit
        $periodProfit -= $periodExpenses;

        // 7. Graph Data: Last 7 Days Sales and Purchases
        $last7Days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $last7Days->push(Carbon::today()->subDays($i)->format('Y-m-d'));
        }

        $salesData = [];
        $purchasesData = [];
        $datesData = [];

        foreach ($last7Days as $dateStr) {
            $datesData[] = Carbon::parse($dateStr)->format('d M');
            $salesData[] = Sale::where('company_id', $companyId)->whereDate('date', $dateStr)->sum('total_amount');
            $purchasesData[] = Purchase::where('company_id', $companyId)->whereDate('date', $dateStr)->sum('total_amount');
        }

        // 8. Graph Data: Stock by Grain
        $stockByGrain = GrainStock::with('grain')
            ->where('company_id', $companyId)
            ->where('quantity', '>', 0)
            ->get();
            
        $grainLabels = [];
        $grainSeries = [];
        foreach ($stockByGrain as $stock) {
            $grainLabels[] = $stock->grain->name ?? 'Unknown';
            $grainSeries[] = (float) $stock->quantity;
        }

        return view('dashboard.index', compact(
            'period',
            'startDate',
            'endDate',
            'periodPurchases', 
            'periodSales', 
            'currentStock', 
            'totalReceivable', 
            'totalPayable', 
            'periodProfit',
            'datesData',
            'salesData',
            'purchasesData',
            'grainLabels',
            'grainSeries',
            'periodExpenses',
            'monthExpenses',
            'recentExpenses'
        ));
    }
}
