<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Business\LedgerEntry;
use Illuminate\Http\Request;

class LedgerEntryController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;
        
        $query = LedgerEntry::with(['party', 'reference' => function ($morphTo) {
            // Preload the related models (Purchase or Sale) and their respective nested relations
            $morphTo->morphWith([
                \App\Models\Business\Purchase::class => ['items.grain', 'lots'],
                \App\Models\Business\Sale::class => ['grain', 'saleLotAllocations.lot']
            ]);
        }])
        ->where('company_id', $companyId);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('entry_date', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('entry_type', 'like', "%{$search}%")
                  ->orWhereHas('party', function($p) use ($search) {
                      $p->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $allEntries = $query->get();

        $totalDebit = $allEntries->sum('debit');
        $totalCredit = $allEntries->sum('credit');
        $netBalance = $totalDebit - $totalCredit;
        $totalTransactions = $allEntries->count();

        $entries = $query->orderBy('entry_date', 'desc')
                         ->orderBy('id', 'desc')
                         ->get();

        if ($request->export === 'csv') {
            return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\LedgerExport($entries), 'ledger.csv');
        }

        if ($request->export === 'pdf') {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('business.financials.ledger.pdf', compact('entries', 'totalDebit', 'totalCredit', 'netBalance', 'totalTransactions'));
            return $pdf->download('ledger.pdf');
        }
            
        return view('business.financials.ledger.index', compact('entries', 'totalDebit', 'totalCredit', 'netBalance', 'totalTransactions'));
    }
}
