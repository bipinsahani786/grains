<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Business\GrainStock;
use Illuminate\Http\Request;

class GrainStockController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;
        
        $stocks = GrainStock::with('grain')
            ->where('company_id', $companyId)
            ->get();
            
        $lotsQuery = \App\Models\Business\Lot::with(['purchase.party'])
            ->where('company_id', $companyId);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if ($startDate && $endDate) {
            $lotsQuery->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }

        $lotsByGrain = $lotsQuery->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('grain_id');
            
        return view('business.inventory.stocks.index', compact('stocks', 'lotsByGrain'));
    }
}
