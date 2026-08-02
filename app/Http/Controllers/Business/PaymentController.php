<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Business\Payment;
use App\Models\Business\LedgerEntry;
use App\Models\Core\User;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function store(Request $request, $partyId)
    {
        $request->validate([
            'type' => 'required|in:advance,payment',
            'direction' => 'required|in:in,out',
            'amount' => 'required|numeric|min:0.01',
            'mode' => 'required|in:cash,bank,upi,cheque',
            'date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $companyId = auth()->user()->company_id;
        
        $party = User::where('company_id', $companyId)
            ->whereIn('role', ['party', 'broker'])
            ->findOrFail($partyId);

        DB::beginTransaction();

        try {
            // 1. Create Payment Record
            $payment = Payment::create([
                'company_id' => $companyId,
                'party_id' => $party->id,
                'direction' => $request->direction,
                'amount' => $request->amount,
                'mode' => $request->mode,
                'notes' => $request->notes,
                'date' => $request->date,
                'created_by' => auth()->id(),
            ]);

            // 2. Create Ledger Entry
            // Determine entry type
            $entryType = 'payment_' . $request->direction;
            if ($request->type === 'advance') {
                $entryType = 'advance';
            }

            // Determine debit/credit
            // If direction is 'in' (Received money from party), we credit the party's ledger.
            // If direction is 'out' (Paid money to party), we debit the party's ledger.
            $debit = 0;
            $credit = 0;
            if ($request->direction === 'in') {
                $credit = $request->amount;
            } else {
                $debit = $request->amount;
            }

            // Calculate balance_after
            // Get last balance
            $lastEntry = LedgerEntry::where('company_id', $companyId)
                ->where('party_id', $party->id)
                ->orderBy('id', 'desc')
                ->first();

            $balance = $lastEntry ? $lastEntry->balance_after : $party->opening_balance;
            
            // Adjust opening balance sign if this is the very first entry
            if (!$lastEntry) {
                if ($party->opening_balance_type === 'credit') {
                    $balance = -abs($balance);
                } elseif ($party->opening_balance_type === 'debit') {
                    $balance = abs($balance);
                }
            }
            
            $newBalance = $balance + $debit - $credit;

            LedgerEntry::create([
                'company_id' => $companyId,
                'party_id' => $party->id,
                'entry_type' => $entryType,
                'reference_type' => Payment::class,
                'reference_id' => $payment->id,
                'debit' => $debit,
                'credit' => $credit,
                'balance_after' => $newBalance,
                'entry_date' => $request->date,
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Payment/Advance recorded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to record transaction: ' . $e->getMessage());
        }
    }
}
