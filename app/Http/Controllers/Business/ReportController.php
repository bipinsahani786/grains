<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Business\Purchase;
use App\Models\Business\Sale;
use App\Models\Business\GrainStock;
use App\Models\Business\Lot;
use App\Models\Business\LedgerEntry;
use App\Models\Business\BrokerCommissionEntry;
use App\Models\Business\Expense;
use App\Models\Business\ExpenseCategory;
use App\Models\Core\User;
use App\Models\Business\Grain;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function purchases(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $query = Purchase::with(['party', 'broker', 'grain'])->where('company_id', $companyId);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }
        if ($request->filled('party_id')) {
            $query->where('party_id', $request->party_id);
        }
        if ($request->filled('grain_id')) {
            $query->where('grain_id', $request->grain_id);
        }

        $purchases = $query->orderBy('date', 'desc')->get();
        $parties = User::where('company_id', $companyId)->where('role', 'party')->get();
        $grains = Grain::where('company_id', $companyId)->get();

        return view('business.reports.purchases', compact('purchases', 'parties', 'grains'));
    }

    public function sales(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $query = Sale::with(['party', 'broker', 'grain'])->where('company_id', $companyId);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }
        if ($request->filled('party_id')) {
            $query->where('party_id', $request->party_id);
        }
        if ($request->filled('grain_id')) {
            $query->where('grain_id', $request->grain_id);
        }

        $sales = $query->orderBy('date', 'desc')->get();
        $parties = User::where('company_id', $companyId)->where('role', 'party')->get();
        $grains = Grain::where('company_id', $companyId)->get();

        return view('business.reports.sales', compact('sales', 'parties', 'grains'));
    }

    public function currentStock()
    {
        $companyId = auth()->user()->company_id;
        $stocks = GrainStock::with('grain')->where('company_id', $companyId)->get();
        
        return view('business.reports.current_stock', compact('stocks'));
    }

    public function lotWiseStock(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $query = Lot::with(['grain', 'purchase'])->where('company_id', $companyId);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $lots = $query->orderBy('created_at', 'desc')->get();
        
        return view('business.reports.lot_wise_stock', compact('lots'));
    }

    public function partyLedger(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $parties = User::where('company_id', $companyId)->where('role', 'party')->get();
        $entries = collect();
        $selectedParty = null;

        if ($request->filled('party_id')) {
            $selectedParty = User::find($request->party_id);
            $query = LedgerEntry::with('reference')->where('company_id', $companyId)->where('party_id', $request->party_id);
            
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('entry_date', [$request->start_date, $request->end_date]);
            }
            $entries = $query->orderBy('entry_date', 'asc')->orderBy('id', 'asc')->get();
        }

        return view('business.reports.party_ledger', compact('parties', 'entries', 'selectedParty'));
    }

    public function brokerCommission(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $brokers = User::where('company_id', $companyId)->where('role', 'broker')->get();
        $entries = collect();
        $selectedBroker = null;

        if ($request->filled('broker_id')) {
            $selectedBroker = User::find($request->broker_id);
            $query = BrokerCommissionEntry::with('reference')->where('company_id', $companyId)->where('broker_id', $request->broker_id);
            
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('date', [$request->start_date, $request->end_date]);
            }
            $entries = $query->orderBy('date', 'desc')->get();
        }

        return view('business.reports.broker_commission', compact('brokers', 'entries', 'selectedBroker'));
    }

    public function profit(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $query = Sale::with(['grain', 'saleLotAllocations.lot', 'brokerCommission'])->where('company_id', $companyId);

        $startDate = $request->start_date ?? null;
        $endDate   = $request->end_date ?? null;

        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        }
        
        $sales = $query->orderBy('date', 'desc')->get();
        
        // Calculate profitability
        $profitData = [];
        foreach ($sales as $sale) {
            $cogs = 0; // Cost of Goods Sold
            foreach ($sale->saleLotAllocations as $allocation) {
                $cogs += ($allocation->quantity_taken * $allocation->cost_rate);
            }
            
            $grossProfit = $sale->total_amount - $cogs;
            $brokerCommission = $sale->brokerCommission ? $sale->brokerCommission->commission_amount : 0;
            $netProfit = $grossProfit - $brokerCommission;
            
            $profitData[] = (object)[
                'sale'             => $sale,
                'revenue'          => $sale->total_amount,
                'cogs'             => $cogs,
                'gross_profit'     => $grossProfit,
                'broker_commission'=> $brokerCommission,
                'net_profit'       => $netProfit
            ];
        }

        // Deduct Expenses from profit
        $expenseQuery = Expense::where('company_id', $companyId);
        if ($startDate && $endDate) {
            $expenseQuery->whereBetween('date', [$startDate, $endDate]);
        }
        $totalExpenses = $expenseQuery->sum('amount');
        $expensesByCategory = $expenseQuery->selectRaw('category_id, SUM(amount) as total')
            ->with('category')->groupBy('category_id')->get();

        return view('business.reports.profit', compact('profitData', 'totalExpenses', 'expensesByCategory', 'startDate', 'endDate'));
    }

    public function expenseSummary(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $startDate = $request->start_date ?? now()->startOfMonth()->format('Y-m-d');
        $endDate   = $request->end_date ?? now()->format('Y-m-d');

        $query = Expense::with(['category'])
            ->where('company_id', $companyId)
            ->whereBetween('date', [$startDate, $endDate]);

        $expenses = $query->orderBy('date', 'desc')->get();
        $totalExpenses = $expenses->sum('amount');
        $expenseCount  = $expenses->count();

        $categoryBreakdown = Expense::selectRaw('category_id, COUNT(*) as count, SUM(amount) as total')
            ->with('category')
            ->where('company_id', $companyId)
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->get();

        $modeBreakdown = Expense::selectRaw('payment_mode, COUNT(*) as count, SUM(amount) as total')
            ->where('company_id', $companyId)
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('payment_mode')
            ->orderByDesc('total')
            ->get();

        return view('business.reports.expenses', compact(
            'expenses', 'totalExpenses', 'expenseCount',
            'categoryBreakdown', 'modeBreakdown', 'startDate', 'endDate'
        ));
    }
}
