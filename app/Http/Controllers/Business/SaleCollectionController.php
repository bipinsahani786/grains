<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Business\Sale;
use App\Models\Business\SaleCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SaleCollectionController extends Controller
{
    /**
     * Store a new collection (partial or full payment received on a sale).
     */
    public function store(Request $request, Sale $sale)
    {
        $request->validate([
            'amount'       => 'required|numeric|min:0.01',
            'mode'         => 'required|string|in:Cash,Cheque,Online,NEFT,UPI',
            'collected_at' => 'required|date',
            'reference_no' => 'nullable|string|max:100',
            'notes'        => 'nullable|string|max:500',
        ]);

        $company = Auth::user()->company;

        // Ensure this sale belongs to the company
        abort_if($sale->company_id !== $company->id, 403);

        $outstanding = $sale->remaining_outstanding;
        $amount = min((float) $request->amount, $outstanding); // cap at outstanding

        SaleCollection::create([
            'company_id'   => $company->id,
            'sale_id'      => $sale->id,
            'amount'       => $amount,
            'mode'         => $request->mode,
            'collected_at' => $request->collected_at,
            'reference_no' => $request->reference_no,
            'notes'        => $request->notes,
            'created_by'   => Auth::id(),
        ]);

        // Update outstanding_amount on sale
        $newCollected = $sale->collections()->sum('amount');
        $net = (float) ($sale->net_amount ?? $sale->total_amount ?? 0);
        $sale->update([
            'amount_paid'       => $newCollected,
            'outstanding_amount' => max(0, $net - $newCollected),
        ]);

        return response()->json([
            'success'             => true,
            'message'             => 'Payment of ₹' . number_format($amount, 2) . ' recorded successfully.',
            'total_collected'     => $newCollected,
            'remaining_outstanding' => max(0, $net - $newCollected),
            'is_fully_collected'  => ($net - $newCollected) < 0.01,
        ]);
    }

    /**
     * List all collections for a sale (for the modal history table).
     */
    public function history(Sale $sale)
    {
        $company = Auth::user()->company;
        abort_if($sale->company_id !== $company->id, 403);

        $collections = $sale->collections()->orderBy('collected_at', 'desc')->get()->map(fn($c) => [
            'id'           => $c->id,
            'amount'       => $c->amount,
            'mode'         => $c->mode,
            'collected_at' => $c->collected_at->format('d M Y'),
            'reference_no' => $c->reference_no,
            'notes'        => $c->notes,
        ]);

        return response()->json([
            'collections'           => $collections,
            'total_collected'       => $sale->total_collected,
            'remaining_outstanding' => $sale->remaining_outstanding,
            'net_amount'            => (float) ($sale->net_amount ?? $sale->total_amount),
        ]);
    }
}
