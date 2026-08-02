<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Business\InventoryLog;
use Illuminate\Support\Facades\Auth;

class InventoryLogController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoryLog::with(['grain', 'godown', 'lot', 'user'])
            ->where('company_id', Auth::user()->company_id);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('type', 'like', "%{$search}%")
                  ->orWhereHas('grain', function($g) use ($search) {
                      $g->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('godown', function($gd) use ($search) {
                      $gd->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $allLogs = $query->get();

        $totalTransactions = $allLogs->count();
        $totalIn = $allLogs->where('quantity_changed', '>', 0)->sum('quantity_changed');
        $totalOut = abs($allLogs->where('quantity_changed', '<', 0)->sum('quantity_changed'));
        
        $logs = $query->latest('created_at')->get();
            
        return view('business.inventory-logs.index', compact('logs', 'totalTransactions', 'totalIn', 'totalOut'));
    }
}
