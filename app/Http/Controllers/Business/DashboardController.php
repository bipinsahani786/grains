<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Business\Purchase;
use App\Models\Business\Sale;
use App\Models\Business\GrainStock;
use App\Models\Core\User;
use App\Models\Business\LedgerEntry;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $companyId = auth()->user()->company_id;
        $today = Carbon::today();

        // 1. Today's Purchases
        $todayPurchases = Purchase::where('company_id', $companyId)
            ->whereDate('date', $today)
            ->sum('total_amount');

        // 2. Today's Sales
        $todaySales = Sale::where('company_id', $companyId)
            ->whereDate('date', $today)
            ->sum('total_amount');

        // 3. Current Stock (Total units across all grains)
        $currentStock = GrainStock::where('company_id', $companyId)->sum('quantity');

        // 4 & 5. Total Receivable and Total Payable
        // Receivable = Negative balance_after, Payable = Positive balance_after
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

        // 6. Today's Profit
        $todaySalesRecords = Sale::with(['saleLotAllocations.lot', 'brokerCommission'])
            ->where('company_id', $companyId)
            ->whereDate('date', $today)
            ->get();
            
        $todayProfit = 0;
        foreach ($todaySalesRecords as $sale) {
            $cogs = 0;
            foreach ($sale->saleLotAllocations as $allocation) {
                $cogs += ($allocation->quantity_taken * $allocation->cost_rate);
            }
            $grossProfit = $sale->total_amount - $cogs;
            $brokerCommission = $sale->brokerCommission ? $sale->brokerCommission->commission_amount : 0;
            $todayProfit += ($grossProfit - $brokerCommission);
        }

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
            'todayPurchases', 
            'todaySales', 
            'currentStock', 
            'totalReceivable', 
            'totalPayable', 
            'todayProfit',
            'datesData',
            'salesData',
            'purchasesData',
            'grainLabels',
            'grainSeries'
        ));
    }
}
