<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Business\Lot;
use Illuminate\Http\Request;

class LotController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;
        
        $query = Lot::with('grain')
            ->where('company_id', $companyId);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('lot_no', 'like', "%{$search}%")
                  ->orWhereHas('grain', function($g) use ($search) {
                      $g->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Get analytics before pagination/fetching all if needed. For now, fetch all as it was doing.
        $allLots = $query->get();

        $totalLots = $allLots->count();
        $openLots = $allLots->where('status', 'open')->count();
        $totalInitialQty = $allLots->sum('initial_quantity');
        $totalRemainingQty = $allLots->sum('remaining_quantity');

        // Fetch ordered lots
        $lots = $query->orderBy('status', 'desc') // 'open' > 'closed'
            ->orderBy('created_at', 'asc')
            ->get();
            
        return view('business.inventory.lots.index', compact(
            'lots', 'totalLots', 'openLots', 'totalInitialQty', 'totalRemainingQty'
        ));
    }
}
