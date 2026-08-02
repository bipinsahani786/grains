<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Business\Purchase;
use App\Models\Business\PurchaseItem;
use App\Models\Business\PurchaseCharge;
use App\Models\Business\GrainStock;
use App\Models\Business\Godown;
use App\Models\Business\Lot;
use App\Models\Business\Grain;
use App\Models\Business\LedgerEntry;
use App\Models\Business\Payment;
use App\Models\Business\InventoryLog;
use App\Models\Core\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function index()
    {
        $companyId = Auth::user()->company_id;
        $purchases = Purchase::with([
            'party' => function($q) { $q->select('id', 'name'); }, 
            'items.grain' => function($q) { $q->select('id', 'name'); },
            'items.godown',
            'lots'
        ])
            ->where('company_id', $companyId)
            ->latest('date')
            ->latest('purchase_time')
            ->get();
            
        return view('business.purchases.index', compact('purchases'));
    }

    public function create()
    {
        $companyId = Auth::user()->company_id;
        
        $parties = User::where('company_id', $companyId)
            ->where('role', 'party')
            ->get();
            
        $brokers = User::where('company_id', $companyId)
            ->where('role', 'broker')
            ->get();
            
        $grains = Grain::where('company_id', $companyId)->get();
        $godowns = Godown::where('company_id', $companyId)->get();
        $partyTypes = \App\Models\Business\PartyType::whereNull('company_id')
            ->orWhere('company_id', $companyId)
            ->get();
        
        return view('business.purchases.create', compact('parties', 'brokers', 'grains', 'godowns', 'partyTypes'));
    }

    public function print(Purchase $purchase)
    {
        $purchase->load(['party', 'broker', 'items.grain', 'charges']);
        $company = Auth::user()->company;
        return view('business.purchases.print', compact('purchase', 'company'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'purchase_time' => 'required',
            'party_id' => 'required|exists:users,id',
            'broker_id' => 'nullable|exists:users,id',
            
            'items' => 'required|array|min:1',
            'items.*.grain_id' => 'required|exists:grains,id',
            'items.*.godown_id' => 'required|exists:godowns,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.unit' => 'required|string',
            
            'payments' => 'nullable|array',
            'charges' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            $companyId = Auth::user()->company_id;
            
            $items = $request->items;
            $charges = $request->charges ?? [];
            $payments = $request->payments ?? [];

            // Calculate totals
            $itemTotal = 0;
            foreach ($items as $item) {
                $itemTotal += ($item['quantity'] * $item['rate']);
            }

            $chargeTotal = 0;
            foreach ($charges as $charge) {
                if ($charge['type'] == 'deduct') {
                     $chargeTotal -= $charge['amount'];
                } else {
                     $chargeTotal += $charge['amount'];
                }
            }

            $grandTotal = $itemTotal + $chargeTotal;

            // Auto-generate Purchase No
            $company = Auth::user()->company;
            $prefix = $company->purchase_prefix ?? '';
            $yearFormat = $company->purchase_year_format;
            $length = $company->purchase_sequence_length ?? 4;
            $startSeq = $company->purchase_sequence_start ?? 1;

            $yearStr = '';
            if ($yearFormat) {
                // assume April-March financial year
                $currentMonth = date('n');
                $currentYear = date('Y');
                $startYear = $currentMonth < 4 ? $currentYear - 1 : $currentYear;
                $endYear = $startYear + 1;

                if ($yearFormat == 'YY-YY') {
                    $yearStr = substr($startYear, -2) . '-' . substr($endYear, -2) . '-';
                } elseif ($yearFormat == 'YYYY-YY') {
                    $yearStr = $startYear . '-' . substr($endYear, -2) . '-';
                } elseif ($yearFormat == 'YYYY') {
                    $yearStr = $startYear . '-';
                }
            }

            // Find last sequence number for this company
            $lastSeq = Purchase::where('company_id', $companyId)->max('sequence_no');
            $nextSeq = max((int)$lastSeq + 1, (int)$startSeq);

            $purchaseNo = $prefix . $yearStr . str_pad($nextSeq, $length, '0', STR_PAD_LEFT);

            // 1. Create Purchase
            $purchase = Purchase::create([
                'company_id' => $companyId,
                'purchase_no' => $purchaseNo,
                'sequence_no' => $nextSeq,
                'date' => $request->date,
                'purchase_time' => $request->purchase_time,
                'party_id' => $request->party_id,
                'broker_id' => $request->broker_id,
                'total_amount' => $grandTotal,
                'notes' => $request->notes,
                'created_by' => Auth::id()
            ]);

            $bagWeight = Auth::user()->company->bag_weight_kg ?? 50;

            // 2. Process Items
            foreach ($items as $index => $item) {
                $lineTotal = $item['quantity'] * $item['rate'];
                
                // --- UNIT CONVERSION LOGIC ---
                $qtyInQtl = $item['quantity'];
                $ratePerQtl = $item['rate'];
                
                if ($item['unit'] === 'Kg') {
                    $qtyInQtl = $item['quantity'] / 100;
                    $ratePerQtl = $item['rate'] * 100;
                } elseif ($item['unit'] === 'Ton') {
                    $qtyInQtl = $item['quantity'] * 10;
                    $ratePerQtl = $item['rate'] / 10;
                } elseif ($item['unit'] === 'Bags') {
                    // Convert bags to Kg first, then to Quintal
                    $totalKg = $item['quantity'] * $bagWeight;
                    $qtyInQtl = $totalKg / 100;
                    // Rate is per Bag. Rate per Kg = Rate / BagWeight. Rate per Quintal = RatePerKg * 100
                    $ratePerQtl = ($item['rate'] / $bagWeight) * 100;
                }
                // -----------------------------
                
                $purchaseItem = PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'grain_id' => $item['grain_id'],
                    'godown_id' => $item['godown_id'],
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'],
                    'moisture' => $item['moisture'] ?? null,
                    'rate' => $item['rate'],
                    'total_amount' => $lineTotal
                ]);

                // Create Lot
                $lotNo = 'LOT-' . date('Ymd') . '-' . str_pad($purchase->id, 4, '0', STR_PAD_LEFT) . '-' . ($index + 1);
                $lot = Lot::create([
                    'company_id' => $companyId,
                    'lot_no' => $lotNo,
                    'grain_id' => $item['grain_id'],
                    'godown_id' => $item['godown_id'],
                    'purchase_id' => $purchase->id,
                    'initial_quantity' => $qtyInQtl,
                    'remaining_quantity' => $qtyInQtl,
                    'moisture' => $item['moisture'] ?? null,
                    'rate' => $ratePerQtl,
                    'status' => 'open'
                ]);

                // Update Godown Stock
                $godown = Godown::find($item['godown_id']);
                if ($godown) {
                    $godown->current_stock_in_quintals += $qtyInQtl;
                    $godown->save();
                }

                // Update Global GrainStock
                $stock = GrainStock::firstOrCreate(
                    ['company_id' => $companyId, 'grain_id' => $item['grain_id']],
                    ['quantity' => 0]
                );
                $stock->quantity += $qtyInQtl;
                $stock->save();

                // Inventory Log
                InventoryLog::create([
                    'company_id' => $companyId,
                    'grain_id' => $item['grain_id'],
                    'godown_id' => $item['godown_id'],
                    'lot_id' => $lot->id,
                    'transaction_type' => 'purchase',
                    'quantity_changed' => $qtyInQtl,
                    'balance_after' => $stock->quantity,
                    'date' => $request->date,
                    'created_by' => Auth::id()
                ]);
            }

            // 3. Process Charges
            foreach ($charges as $charge) {
                if ($charge['amount'] > 0) {
                    \App\Models\Business\PurchaseCharge::create([
                        'purchase_id' => $purchase->id,
                        'type' => $charge['type'] . ' - ' . ($charge['name'] ?? 'Other'),
                        'amount' => $charge['amount']
                    ]);
                }
            }

            // 4. Ledger & Payments
            $totalPaid = 0;
            foreach ($payments as $payment) {
                if ($payment['amount'] > 0) {
                    Payment::create([
                        'company_id' => $companyId,
                        'party_id' => $request->party_id,
                        'direction' => 'out', // We pay out for purchases
                        'amount' => $payment['amount'],
                        'mode' => $payment['mode'],
                        'related_type' => Purchase::class,
                        'related_id' => $purchase->id,
                        'date' => $request->date,
                        'created_by' => Auth::id()
                    ]);
                    $totalPaid += $payment['amount'];
                }
            }

            $outstandingAmount = $grandTotal - $totalPaid;

            $lastEntry = LedgerEntry::where('company_id', $companyId)
                ->where('party_id', $request->party_id)
                ->latest('id')
                ->first();
                
            $party = User::find($request->party_id);
            $currentBalance = $lastEntry ? $lastEntry->balance_after : $party->opening_balance;
            
            // Purchase increases our payable to them (Credit)
            // Payment decreases our payable to them (Debit)
            $newBalance = $currentBalance + $outstandingAmount;

            if ($grandTotal > 0) {
                LedgerEntry::create([
                    'company_id' => $companyId,
                    'party_id' => $request->party_id,
                    'entry_type' => 'purchase',
                    'reference_type' => Purchase::class,
                    'reference_id' => $purchase->id,
                    'debit' => $totalPaid,
                    'credit' => $grandTotal,
                    'balance_after' => $newBalance,
                    'entry_date' => $request->date,
                ]);
            }

            // 5. Broker Commission
            if ($request->broker_id) {
                $commissionRule = \App\Models\Business\BrokerCommissionRate::where('company_id', $companyId)
                    ->where('broker_id', $request->broker_id)
                    ->whereIn('applies_to', ['purchase', 'both'])
                    ->first();

                if ($commissionRule) {
                    // Loop through items since commission applies to the total purchase or per unit
                    $totalCommission = 0;
                    foreach ($items as $item) {
                        $lineCommission = 0;
                        $lineTotal = $item['quantity'] * $item['rate'];
                        
                        $qtyInKg = 0;
                        if ($item['unit'] === 'Kg') $qtyInKg = $item['quantity'];
                        if ($item['unit'] === 'Quintal') $qtyInKg = $item['quantity'] * 100;
                        if ($item['unit'] === 'Ton') $qtyInKg = $item['quantity'] * 1000;
                        if ($item['unit'] === 'Bags') $qtyInKg = $item['quantity'] * ($company->bag_weight_kg ?? 50);
                        
                        $qtyInQuintal = $qtyInKg / 100;

                        if ($commissionRule->commission_type === 'per_quintal') {
                            $lineCommission = $qtyInQuintal * $commissionRule->rate;
                        } elseif ($commissionRule->commission_type === 'per_kg') {
                            $lineCommission = $qtyInKg * $commissionRule->rate;
                        } elseif ($commissionRule->commission_type === 'percentage') {
                            $lineCommission = ($lineTotal * $commissionRule->rate) / 100;
                        }

                        $totalCommission += $lineCommission;
                    }
                    
                    if ($commissionRule->commission_type === 'fixed') {
                        $totalCommission = $commissionRule->rate;
                    }

                    // For the entry record, we summarize it in Quintals based on all items
                    $qtySummaryQtl = 0;
                    foreach ($items as $item) {
                        $qtySummaryQtl += \App\Helpers\UnitHelper::toQtl($item['quantity'], $item['unit'], $company->bag_weight_kg ?? 50);
                    }
                    $rateSummaryQtl = \App\Helpers\UnitHelper::rateToQtl($items[0]['rate'] ?? 0, $items[0]['unit'] ?? 'Quintal', $company->bag_weight_kg ?? 50);

                    \App\Models\Business\BrokerCommissionEntry::create([
                        'company_id' => $companyId,
                        'broker_id' => $request->broker_id,
                        'reference_type' => Purchase::class,
                        'reference_id' => $purchase->id,
                        'date' => $request->date,
                        'quantity' => $qtySummaryQtl,
                        'rate' => $rateSummaryQtl,
                        'commission_type' => $commissionRule->commission_type,
                        'commission_rate' => $commissionRule->rate,
                        'commission_amount' => $totalCommission
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('business.purchases.index')->with('success', 'Purchase recorded successfully with items and payments!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to record purchase: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $companyId = auth()->user()->company_id;
        $purchase = Purchase::where('company_id', $companyId)
            ->with(['lots', 'items'])
            ->findOrFail($id);

        // 1. Validation: Have any lots been used?
        foreach ($purchase->lots as $lot) {
            // Because float comparison can be tricky, we round them
            if (round($lot->remaining_quantity, 2) < round($lot->initial_quantity, 2)) {
                return back()->with('error', 'Cannot cancel purchase: Some lots from this purchase have already been sold. Please cancel the associated sales first.');
            }
        }

        // 2. Validation: Broker Commission Paid?
        $brokerCommission = \App\Models\Business\BrokerCommissionEntry::where('reference_type', Purchase::class)
            ->where('reference_id', $purchase->id)
            ->first();
        
        if ($brokerCommission && $brokerCommission->payment_status !== 'pending') {
            return back()->with('error', 'Cannot cancel purchase: The broker commission for this purchase has already been paid. Please delete the commission payment first.');
        }

        DB::beginTransaction();
        try {
            // Reverse Stock & Godown
            foreach ($purchase->lots as $lot) {
                // Deduct from grain stock
                $grainStock = \App\Models\Business\GrainStock::where('company_id', $companyId)
                    ->where('grain_id', $lot->grain_id)
                    ->first();
                if ($grainStock) {
                    $grainStock->quantity -= $lot->initial_quantity;
                    $grainStock->save();
                }

                // Deduct from godown stock
                $godown = \App\Models\Business\Godown::find($lot->godown_id);
                if ($godown) {
                    $godown->current_stock_in_quintals -= $lot->initial_quantity;
                    $godown->save();
                }

                // Delete Inventory Logs for this lot
                \App\Models\Business\InventoryLog::where('lot_id', $lot->id)->delete();
            }

            // Delete associated records
            \App\Models\Business\Lot::where('purchase_id', $purchase->id)->delete();
            \App\Models\Business\PurchaseItem::where('purchase_id', $purchase->id)->delete();
            \App\Models\Business\PurchaseCharge::where('purchase_id', $purchase->id)->delete();
            
            \App\Models\Business\Payment::where('related_type', Purchase::class)
                ->where('related_id', $purchase->id)
                ->delete();
            
            if ($brokerCommission) {
                $brokerCommission->delete();
            }

            // Delete Ledger Entries
            \App\Models\Business\LedgerEntry::where('reference_type', Purchase::class)
                ->where('reference_id', $purchase->id)
                ->delete();

            $partyId = $purchase->party_id;
            
            // Delete the purchase itself
            $purchase->delete();

            // Recalculate Ledger for party
            \App\Models\Business\LedgerEntry::recalculateForParty($companyId, $partyId);

            DB::commit();
            return back()->with('success', 'Purchase has been successfully canceled and all stocks/ledgers reversed.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to cancel purchase: ' . $e->getMessage());
        }
    }
}
