<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Business\Sale;
use App\Models\Business\SaleCharge;
use App\Models\Business\SalePayment;
use App\Models\Business\SaleLotAllocation;
use App\Models\Business\Lot;
use App\Models\Business\Godown;
use App\Models\Business\Grain;
use App\Models\Business\GrainStock;
use App\Models\Business\InventoryLog;
use App\Models\Business\LedgerEntry;
use App\Models\Core\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
    public function print(Sale $sale)
    {
        $sale->load(['party', 'broker', 'grain', 'collections', 'charges', 'payments']);
        $company = Auth::user()->company;
        return view('business.sales.print', compact('sale', 'company'));
    }

    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $query = Sale::with(['party', 'broker', 'grain', 'saleLotAllocations.lot', 'collections', 'charges', 'payments'])
            ->where('company_id', $companyId);

        // Search by Sale No, PO No, Truck No, Customer Name/Phone
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('sale_no', 'like', "%{$search}%")
                    ->orWhere('po_no', 'like', "%{$search}%")
                    ->orWhere('truck_no', 'like', "%{$search}%")
                    ->orWhere('driver_name', 'like', "%{$search}%")
                    ->orWhereHas('party', function ($pq) use ($search) {
                        $pq->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by Party (Customer)
        if ($request->filled('party_id')) {
            $query->where('party_id', $request->party_id);
        }

        // Filter by Grain
        if ($request->filled('grain_id')) {
            $query->where('grain_id', $request->grain_id);
        }

        // Filter by Date Range
        if ($request->filled('from_date')) {
            $query->whereDate('date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('date', '<=', $request->to_date);
        }

        $allSales = $query->orderBy('date', 'desc')
            ->orderBy('sale_time', 'desc')
            ->get();

        // Filter by Payment Status (paid, partial, unpaid)
        if ($request->filled('payment_status')) {
            $status = $request->payment_status;
            $sales = $allSales->filter(function ($s) use ($status) {
                return $s->payment_status === $status;
            })->values();
        } else {
            $sales = $allSales;
        }

        // Calculate Summary Statistics
        $totalSalesAmount = $allSales->sum(function ($s) {
            return (float) ($s->net_amount ?? $s->total_amount ?? 0);
        });
        $totalPaidAmount = $allSales->sum(function ($s) {
            return $s->total_paid;
        });
        $totalDueAmount = $allSales->sum(function ($s) {
            return $s->remaining_outstanding;
        });

        $stats = [
            'total_amount' => $totalSalesAmount,
            'total_paid' => $totalPaidAmount,
            'total_due' => $totalDueAmount,
            'total_count' => $allSales->count(),
            'paid_count' => $allSales->where('payment_status', 'paid')->count(),
            'partial_count' => $allSales->where('payment_status', 'partial')->count(),
            'unpaid_count' => $allSales->where('payment_status', 'unpaid')->count(),
        ];

        $parties = User::where('company_id', $companyId)->where('role', 'party')->orderBy('name')->get();
        $grains = Grain::where('company_id', $companyId)->orderBy('name')->get();

        return view('business.sales.index', compact('sales', 'parties', 'grains', 'stats'));
    }

    public function getLotsForGrain(Request $request)
    {
        try {
            $company = auth()->user()->company;
            $companyId = $company->id;
            $displayUnit = $company->display_unit ?? 'Quintal';
            $bagWeight = $company->bag_weight_kg ?? 50;

            $query = Lot::with([
                'godown',
                'purchase',
                'saleAllocations' => function ($q) use ($request) {
                    if ($request->sale_id) {
                        $q->where('sale_id', $request->sale_id);
                    }
                }
            ])
                ->where('company_id', $companyId)
                ->where('grain_id', $request->grain_id);

            // If editing a sale, we might need lots that are closed but were part of this sale.
            if ($request->sale_id) {
                $query->where(function ($q) use ($request) {
                    $q->where('remaining_quantity', '>', 0)
                        ->where('status', 'open')
                        ->orWhereHas('saleAllocations', function ($sq) use ($request) {
                            $sq->where('sale_id', $request->sale_id);
                        });
                });
            } else {
                $query->where('remaining_quantity', '>', 0)
                    ->where('status', 'open');
            }

            $lots = $query->orderBy('created_at', 'asc')
                ->get()
                ->map(function ($l) use ($request, $displayUnit, $bagWeight) {
                    $allocatedForSale = 0;
                    if ($request->sale_id && $l->saleAllocations) {
                        $allocatedForSale = $l->saleAllocations->sum('quantity_taken');
                    }
                    $actualRemaining = (float) $l->remaining_quantity + $allocatedForSale;
                    return [
                        'id' => $l->id,
                        'lot_no' => $l->lot_no ?? ('Lot #' . $l->id),
                        // Raw quintals for internal math
                        'remaining_quantity' => $actualRemaining,
                        'rate' => (float) $l->rate,
                        // Converted formats for UI display based on user preference
                        'remaining_quantity_display' => \App\Helpers\UnitHelper::formatQty($actualRemaining, $displayUnit, $bagWeight),
                        'rate_display' => '₹' . number_format(\App\Helpers\UnitHelper::rateFromQtl($l->rate, $displayUnit, $bagWeight), 2) . ' / ' . \App\Helpers\UnitHelper::label($displayUnit),
                        // Also provide numeric converted qty for max-validation
                        'remaining_quantity_converted' => \App\Helpers\UnitHelper::fromQtl($actualRemaining, $displayUnit, $bagWeight),

                        'godown' => optional($l->godown)->name ?? 'N/A',
                        'purchase_date' => optional($l->purchase)->date
                            ? date('d M Y', strtotime($l->purchase->date))
                            : ($l->created_at ? $l->created_at->format('d M Y') : '—'),
                    ];
                });

            return response()->json(['lots' => $lots, 'displayUnit' => \App\Helpers\UnitHelper::label($displayUnit)]);
        } catch (\Exception $e) {
            return response()->json(['lots' => [], 'error' => $e->getMessage()], 200);
        }
    }

    public function create()
    {
        $companyId = auth()->user()->company_id;
        $customers = User::where('company_id', $companyId)->where('role', 'party')->get();
        $brokers = User::where('company_id', $companyId)->where('role', 'broker')->get();
        $grains = Grain::where('company_id', $companyId)->get();
        $partyTypes = \App\Models\Business\PartyType::whereNull('company_id')
            ->orWhere('company_id', $companyId)->get();

        return view('business.sales.create', compact('customers', 'brokers', 'grains', 'partyTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'sale_time' => 'required',
            'party_id' => 'required|exists:users,id',
            'broker_id' => 'nullable|exists:users,id',
            'grain_id' => 'required|exists:grains,id',
            'quantity' => 'required|numeric|min:0.01',
            'unit' => 'required|string',
            'rate' => 'required|numeric|min:0',
            'po_no' => 'nullable|string|max:100',
            'bags_count' => 'nullable|integer|min:0',
            'truck_no' => 'nullable|string|max:100',
            'driver_name' => 'nullable|string|max:100',
            'driver_phone' => 'nullable|string|max:20',
            'truck_fare' => 'nullable|numeric|min:0',
            'freight_advance' => 'nullable|numeric|min:0',
            'freight_balance' => 'nullable|numeric',
            'payment_mode' => 'required|in:regular,cash_discount',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'charges' => 'nullable|array',
            'charges.*.name' => 'required_with:charges|string',
            'charges.*.type' => 'required_with:charges|string',
            'charges.*.amount' => 'required_with:charges|numeric',
            'payments' => 'nullable|array',
            'payments.*.mode' => 'required_with:payments|string',
            'payments.*.amount' => 'required_with:payments|numeric|min:0',
            'lots' => 'required|array|min:1',
            'lots.*.id' => 'required|exists:lots,id',
            'lots.*.quantity' => 'nullable|numeric|min:0',
        ]);

        $companyId = auth()->user()->company_id;
        $totalAmount = $request->quantity * $request->rate;

        // --- UNIT CONVERSION LOGIC ---
        $bagWeight = auth()->user()->company->bag_weight_kg ?? 50;
        $requiredQuantity = $request->quantity; // Original value
        $qtyInQtl = \App\Helpers\UnitHelper::toQtl($request->quantity, $request->unit, $bagWeight);
        $ratePerQtl = \App\Helpers\UnitHelper::rateToQtl($request->rate, $request->unit, $bagWeight);
        // -----------------------------

        // Check if enough total stock is available for this grain (in Quintals)
        $grainStock = GrainStock::where('company_id', $companyId)
            ->where('grain_id', $request->grain_id)
            ->first();

        if (!$grainStock || $grainStock->quantity < $qtyInQtl) {
            throw ValidationException::withMessages([
                'quantity' => 'Insufficient stock. You only have ' . ($grainStock ? $grainStock->quantity : 0) . ' Quintals available.'
            ]);
        }

        // Calculate charges
        $chargesTotal = 0;
        if ($request->charges) {
            foreach ($request->charges as $charge) {
                $amt = floatval($charge['amount']);
                $chargesTotal += $charge['type'] === 'deduct' ? -$amt : $amt;
            }
        }

        $itemsTotal = $request->quantity * $request->rate;
        $grandTotal = $itemsTotal + $chargesTotal;

        // Discount (Cash Discount Mode)
        $discountPercent = 0;
        $discountAmount = 0;
        $netAmount = $grandTotal;
        if ($request->payment_mode === 'cash_discount') {
            $discountPercent = floatval($request->discount_percent ?? 0);
            $discountAmount = round($grandTotal * $discountPercent / 100, 2);
            $netAmount = $grandTotal - $discountAmount;
        }

        // Amount Paid from payment rows
        $amountPaid = 0;
        if ($request->payments) {
            foreach ($request->payments as $p) {
                $amountPaid += floatval($p['amount']);
            }
        }
        $outstandingAmount = $netAmount - $amountPaid;

        $partyId = $request->party_id; // already validated as exists:users,id

        DB::transaction(function () use ($request, $companyId, $grandTotal, $netAmount, $discountPercent, $discountAmount, $amountPaid, $outstandingAmount, $chargesTotal, $partyId, $qtyInQtl, $ratePerQtl) {

            // Auto-generate Sale No
            $company = Auth::user()->company;
            $prefix = $company->sale_prefix ?? '';
            $yearFormat = $company->sale_year_format;
            $length = $company->sale_sequence_length ?? 4;
            $startSeq = $company->sale_sequence_start ?? 1;

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
            $lastSeq = Sale::where('company_id', $companyId)->max('sequence_no');
            $nextSeq = max((int) $lastSeq + 1, (int) $startSeq);

            $saleNo = $prefix . $yearStr . str_pad($nextSeq, $length, '0', STR_PAD_LEFT);

            // 1. Create Sale
            $sale = Sale::create([
                'company_id' => $companyId,
                'sale_no' => $saleNo,
                'sequence_no' => $nextSeq,
                'date' => $request->date,
                'sale_time' => $request->sale_time,
                'party_id' => $partyId,
                'broker_id' => $request->broker_id,
                'grain_id' => $request->grain_id,
                'quantity' => $qtyInQtl,
                'unit' => $request->unit,
                'rate' => $ratePerQtl,
                'total_amount' => $grandTotal,
                'payment_mode' => $request->payment_mode,
                'discount_percent' => $discountPercent,
                'discount_amount' => $discountAmount,
                'net_amount' => $netAmount,
                'amount_paid' => $amountPaid,
                'po_no' => $request->po_no,
                'bags_count' => $request->bags_count,
                'truck_no' => $request->truck_no,
                'driver_name' => $request->driver_name,
                'driver_phone' => $request->driver_phone,
                'truck_fare' => $request->truck_fare,
                'freight_advance' => $request->freight_advance,
                'freight_balance' => $request->freight_balance,
                'notes' => $request->notes,
                'created_by' => auth()->id(),
            ]);

            // 1b. Save Add-on Charges
            if ($request->charges) {
                foreach ($request->charges as $charge) {
                    SaleCharge::create([
                        'sale_id' => $sale->id,
                        'name' => $charge['name'],
                        'type' => $charge['type'],
                        'amount' => $charge['amount'],
                    ]);
                }
            }

            // 1c. Save Payments
            if ($request->payments) {
                foreach ($request->payments as $payment) {
                    SalePayment::create([
                        'sale_id' => $sale->id,
                        'mode' => $payment['mode'],
                        'amount' => $payment['amount'],
                    ]);
                }
            }

            // 2. Manual Lot Allocation Logic
            $company = auth()->user()->company;
            $displayUnit = $company->display_unit ?? 'Quintal';
            $bagWeight = $company->bag_weight_kg ?? 50;

            $totalTakenQtl = 0;

            foreach ($request->lots as $lotData) {
                if ($lotData['quantity'] <= 0)
                    continue;

                $lot = Lot::where('company_id', $companyId)->lockForUpdate()->findOrFail($lotData['id']);

                // Convert the taken quantity (which is in user's selected dropdown unit) back to Quintals
                $takeQtyQtl = \App\Helpers\UnitHelper::toQtl($lotData['quantity'], $request->unit, $bagWeight);

                if (round($takeQtyQtl, 3) > round($lot->remaining_quantity, 3)) {
                    throw new \Exception("Lot {$lot->lot_no} does not have enough remaining quantity.");
                }

                $totalTakenQtl += $takeQtyQtl;

                // Create Sale Lot Allocation
                SaleLotAllocation::create([
                    'sale_id' => $sale->id,
                    'lot_id' => $lot->id,
                    'quantity_taken' => $takeQtyQtl,
                    'cost_rate' => $lot->rate, // Original purchase rate
                ]);

                // Update Lot Remaining Quantity
                $lot->remaining_quantity -= $takeQtyQtl;
                if ($lot->remaining_quantity <= 0.001) {
                    $lot->status = 'closed';
                    $lot->remaining_quantity = 0;
                }
                $lot->save();

                // Update Godown Stock
                $godown = Godown::find($lot->godown_id);
                if ($godown) {
                    $godown->current_stock_in_quintals -= $takeQtyQtl;
                    $godown->save();
                }

                // Inventory Log
                InventoryLog::create([
                    'company_id' => $companyId,
                    'grain_id' => $request->grain_id,
                    'godown_id' => $lot->godown_id,
                    'lot_id' => $lot->id,
                    'transaction_type' => 'sale',
                    'quantity_changed' => -$takeQtyQtl,
                    'balance_after' => 0, // This is tracked globally below instead
                    'date' => $request->date,
                    'created_by' => auth()->id()
                ]);
            }

            // Verify that total taken matches the requested sale quantity (in Quintals)
            if (abs($totalTakenQtl - $qtyInQtl) > 0.01) {
                throw new \Exception('The allocated lot quantities do not match the total sale quantity.');
            }

            // 3. Update Grain Stock
            $grainStock = GrainStock::where('company_id', $companyId)
                ->where('grain_id', $request->grain_id)
                ->first();

            if ($grainStock) {
                $grainStock->quantity -= $qtyInQtl;
                $grainStock->save();
            }

            // 4. Update Customer Ledger
            $lastEntry = LedgerEntry::where('company_id', $companyId)
                ->where('party_id', $partyId)
                ->latest('id')
                ->first();

            $currentBalance = $lastEntry ? $lastEntry->balance_after : 0;
            // For sale: customer owes us money (debit on their account)
            // net_amount is what they owe after discount
            $newBalance = $currentBalance + $netAmount; // positive = they owe us

            LedgerEntry::create([
                'company_id' => $companyId,
                'party_id' => $partyId,
                'entry_type' => 'sale',
                'reference_type' => Sale::class,
                'reference_id' => $sale->id,
                'debit' => $netAmount,
                'credit' => $amountPaid > 0 ? $amountPaid : 0,
                'balance_after' => $newBalance - $amountPaid,
                'entry_date' => $request->date,
            ]);

            // 6. Calculate and save Broker Commission
            if ($request->broker_id) {
                $commissionRule = \App\Models\Business\BrokerCommissionRate::where('company_id', $companyId)
                    ->where('broker_id', $request->broker_id)
                    ->whereIn('applies_to', ['sale', 'both'])
                    ->first();

                if ($commissionRule) {
                    $commissionAmount = 0;
                    $qtyInQuintal = \App\Helpers\UnitHelper::toQtl($request->quantity, $request->unit, $company->bag_weight_kg ?? 50);
                    $qtyInKg = $qtyInQuintal * 100;

                    if ($commissionRule->commission_type == 'per_quintal') {
                        $commissionAmount = $qtyInQuintal * $commissionRule->rate;
                    } elseif ($commissionRule->commission_type == 'per_kg') {
                        $commissionAmount = $qtyInKg * $commissionRule->rate;
                    } elseif ($commissionRule->commission_type == 'percentage') {
                        $commissionAmount = ($grandTotal * $commissionRule->rate) / 100;
                    } elseif ($commissionRule->commission_type == 'fixed') {
                        $commissionAmount = $commissionRule->rate;
                    }

                    \App\Models\Business\BrokerCommissionEntry::create([
                        'company_id' => $companyId,
                        'broker_id' => $request->broker_id,
                        'reference_type' => Sale::class,
                        'reference_id' => $sale->id,
                        'date' => $request->date,
                        'quantity' => \App\Helpers\UnitHelper::toQtl($request->quantity, $request->unit, $company->bag_weight_kg ?? 50),
                        'rate' => \App\Helpers\UnitHelper::rateToQtl($request->rate, $request->unit, $company->bag_weight_kg ?? 50),
                        'commission_type' => $commissionRule->commission_type,
                        'commission_rate' => $commissionRule->rate,
                        'commission_amount' => $commissionAmount
                    ]);
                }
            }
        });

        return redirect()->route('business.sales.index')->with('success', 'Sale recorded successfully.');
    }

    public function edit($id)
    {
        $company = auth()->user()->company;
        $sale = Sale::with(['saleLotAllocations', 'charges', 'payments'])->where('company_id', $company->id)->findOrFail($id);

        $parties = \App\Models\Core\User::where('company_id', $company->id)->where('role', 'party')->get();
        $brokers = \App\Models\Core\User::where('company_id', $company->id)->where('role', 'broker')->get();
        $grains = \App\Models\Business\Grain::where('company_id', $company->id)->get();
        $partyTypes = \App\Models\Business\PartyType::whereNull('company_id')
            ->orWhere('company_id', $company->id)
            ->get();

        // Pass to view
        return view('business.sales.edit', compact('sale', 'parties', 'brokers', 'grains', 'company', 'partyTypes'));
    }

    public function update(Request $request, $id)
    {
        $companyId = auth()->user()->company_id;
        $company = auth()->user()->company;
        $sale = Sale::where('company_id', $companyId)
            ->with(['collections', 'saleLotAllocations'])
            ->findOrFail($id);

        // Validations
        if ($sale->collections && $sale->collections->count() > 0) {
            return back()->with('error', 'Cannot edit sale: Payments have already been collected for this sale. Please delete the collections first.');
        }

        $brokerCommission = \App\Models\Business\BrokerCommissionEntry::where('reference_type', Sale::class)
            ->where('reference_id', $sale->id)
            ->first();

        if ($brokerCommission && $brokerCommission->payment_status !== 'pending') {
            return back()->with('error', 'Cannot edit sale: The broker commission for this sale has already been paid. Please delete the commission payment first.');
        }

        $request->validate([
            'date' => 'required|date',
            'sale_time' => 'required',
            'party_id' => 'required|exists:users,id',
            'broker_id' => 'nullable|exists:users,id',
            'grain_id' => 'required|exists:grains,id',
            'quantity' => 'required|numeric|min:0.01',
            'unit' => 'required|string',
            'rate' => 'required|numeric|min:0',
            'po_no' => 'nullable|string|max:100',
            'bags_count' => 'nullable|integer|min:0',
            'truck_no' => 'nullable|string|max:100',
            'driver_name' => 'nullable|string|max:100',
            'driver_phone' => 'nullable|string|max:20',
            'truck_fare' => 'nullable|numeric|min:0',
            'freight_advance' => 'nullable|numeric|min:0',
            'freight_balance' => 'nullable|numeric',
            'payment_mode' => 'nullable|in:regular,cash_discount',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'charges' => 'nullable|array',
            'charges.*.name' => 'required_with:charges|string',
            'charges.*.type' => 'required_with:charges|string',
            'charges.*.amount' => 'required_with:charges|numeric',
            'payments' => 'nullable|array',
            'payments.*.mode' => 'required_with:payments|string',
            'payments.*.amount' => 'required_with:payments|numeric|min:0',
            'lots' => 'nullable|array',
            'lots.*.id' => 'required_with:lots|exists:lots,id',
            'lots.*.quantity' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // ==========================================
            // STEP 1: REVERT THE OLD SALE
            // ==========================================
            // Restore Lots and Stock
            foreach ($sale->saleLotAllocations as $allocation) {
                $lot = \App\Models\Business\Lot::find($allocation->lot_id);
                if ($lot) {
                    $lot->remaining_quantity += $allocation->quantity_taken;
                    if ($lot->remaining_quantity > 0) {
                        $lot->status = 'open';
                    }
                    $lot->save();

                    // Restore Godown Stock
                    $godown = \App\Models\Business\Godown::find($lot->godown_id);
                    if ($godown) {
                        $godown->current_stock_in_quintals += $allocation->quantity_taken;
                        $godown->save();
                    }
                }
            }

            \App\Models\Business\InventoryLog::where('company_id', $companyId)
                ->where('transaction_type', 'sale')
                ->where('date', $sale->date)
                ->whereIn('lot_id', $sale->saleLotAllocations->pluck('lot_id'))
                ->delete();

            $grainStock = \App\Models\Business\GrainStock::where('company_id', $companyId)
                ->where('grain_id', $sale->grain_id)
                ->first();
            if ($grainStock) {
                $grainStock->quantity += $sale->quantity;
                $grainStock->save();
            }

            // Delete associated records
            \App\Models\Business\SaleLotAllocation::where('sale_id', $sale->id)->delete();
            \App\Models\Business\SaleCharge::where('sale_id', $sale->id)->delete();
            \App\Models\Business\SalePayment::where('sale_id', $sale->id)->delete();

            if ($brokerCommission) {
                $brokerCommission->delete();
            }

            \App\Models\Business\LedgerEntry::where('reference_type', Sale::class)
                ->where('reference_id', $sale->id)
                ->delete();

            $oldPartyId = $sale->party_id;

            // ==========================================
            // STEP 2: APPLY THE NEW SALE DATA
            // ==========================================
            $bagWeight = $company->bag_weight_kg ?? 50;
            $qtyInQtl = \App\Helpers\UnitHelper::toQtl($request->quantity, $request->unit, $bagWeight);
            $ratePerQtl = \App\Helpers\UnitHelper::rateToQtl($request->rate, $request->unit, $bagWeight);

            $totalTakenQtl = 0;

            // Manual lot allocation if lots provided
            if ($request->lots && count($request->lots) > 0) {
                foreach ($request->lots as $lotData) {
                    $takeQtyUnit = floatval($lotData['quantity'] ?? 0);
                    if ($takeQtyUnit <= 0) continue;

                    $lot = Lot::where('company_id', $companyId)->lockForUpdate()->findOrFail($lotData['id']);
                    $takeQtyQtl = \App\Helpers\UnitHelper::toQtl($takeQtyUnit, $request->unit, $bagWeight);

                    if (round($takeQtyQtl, 3) > round($lot->remaining_quantity, 3)) {
                        throw new \Exception("Lot {$lot->lot_no} does not have enough remaining quantity.");
                    }

                    $totalTakenQtl += $takeQtyQtl;

                    SaleLotAllocation::create([
                        'sale_id' => $sale->id,
                        'lot_id' => $lot->id,
                        'quantity_taken' => $takeQtyQtl,
                        'cost_rate' => $lot->rate,
                    ]);

                    $lot->remaining_quantity -= $takeQtyQtl;
                    if ($lot->remaining_quantity <= 0.001) {
                        $lot->status = 'closed';
                        $lot->remaining_quantity = 0;
                    }
                    $lot->save();

                    $godown = Godown::find($lot->godown_id);
                    if ($godown) {
                        $godown->current_stock_in_quintals -= $takeQtyQtl;
                        $godown->save();
                    }

                    InventoryLog::create([
                        'company_id' => $companyId,
                        'grain_id' => $request->grain_id,
                        'godown_id' => $lot->godown_id,
                        'lot_id' => $lot->id,
                        'transaction_type' => 'sale',
                        'quantity_changed' => -$takeQtyQtl,
                        'balance_after' => 0,
                        'date' => $request->date,
                        'created_by' => auth()->id()
                    ]);
                }
            }

            // Fallback to FIFO if not manually allocated
            if ($totalTakenQtl == 0) {
                $openLots = \App\Models\Business\Lot::where('company_id', $companyId)
                    ->where('grain_id', $request->grain_id)
                    ->where('status', 'open')
                    ->where('remaining_quantity', '>', 0)
                    ->orderBy('date', 'asc')
                    ->orderBy('id', 'asc')
                    ->get();

                $remainingToTakeQtl = $qtyInQtl;

                foreach ($openLots as $lot) {
                    if ($remainingToTakeQtl <= 0)
                        break;

                    $takeQtyQtl = min($lot->remaining_quantity, $remainingToTakeQtl);

                    SaleLotAllocation::create([
                        'sale_id' => $sale->id,
                        'lot_id' => $lot->id,
                        'quantity_taken' => $takeQtyQtl,
                        'cost_rate' => $lot->rate,
                    ]);

                    $lot->remaining_quantity -= $takeQtyQtl;
                    if ($lot->remaining_quantity <= 0.001) {
                        $lot->status = 'closed';
                        $lot->remaining_quantity = 0;
                    }
                    $lot->save();

                    $godown = Godown::find($lot->godown_id);
                    if ($godown) {
                        $godown->current_stock_in_quintals -= $takeQtyQtl;
                        $godown->save();
                    }

                    $totalTakenQtl += $takeQtyQtl;
                    $remainingToTakeQtl -= $takeQtyQtl;

                    InventoryLog::create([
                        'company_id' => $companyId,
                        'grain_id' => $request->grain_id,
                        'godown_id' => $lot->godown_id,
                        'lot_id' => $lot->id,
                        'transaction_type' => 'sale',
                        'quantity_changed' => -$takeQtyQtl,
                        'balance_after' => 0,
                        'date' => $request->date,
                        'created_by' => auth()->id()
                    ]);
                }
            }

            if (abs($totalTakenQtl - $qtyInQtl) > 0.01) {
                throw new \Exception('Not enough stock available to fulfill the updated sale quantity.');
            }

            $grainStock = GrainStock::firstOrCreate(
                ['company_id' => $companyId, 'grain_id' => $request->grain_id],
                ['quantity' => 0]
            );
            $grainStock->quantity -= $qtyInQtl;
            $grainStock->save();

            // Calculate amounts
            $chargesTotal = 0;
            if ($request->charges) {
                foreach ($request->charges as $charge) {
                    $amt = floatval($charge['amount']);
                    \App\Models\Business\SaleCharge::create([
                        'sale_id' => $sale->id,
                        'name' => $charge['name'],
                        'type' => $charge['type'],
                        'amount' => $amt,
                    ]);
                    $chargesTotal += $charge['type'] === 'deduct' ? -$amt : $amt;
                }
            }

            $itemsTotal = $request->quantity * $request->rate;
            $grandTotal = $itemsTotal + $chargesTotal;

            $discountPercent = 0;
            $discountAmount = 0;
            $netAmount = $grandTotal;
            if ($request->payment_mode === 'cash_discount') {
                $discountPercent = floatval($request->discount_percent ?? 0);
                $discountAmount = round($grandTotal * $discountPercent / 100, 2);
                $netAmount = $grandTotal - $discountAmount;
            }

            $amountPaid = 0;
            if ($request->payments) {
                foreach ($request->payments as $payment) {
                    $amt = floatval($payment['amount']);
                    \App\Models\Business\SalePayment::create([
                        'sale_id' => $sale->id,
                        'mode' => $payment['mode'],
                        'amount' => $amt,
                    ]);
                    $amountPaid += $amt;
                }
            }

            $outstandingAmount = $netAmount - $amountPaid;

            // Update Sale model
            $sale->update([
                'date' => $request->date,
                'sale_time' => $request->sale_time,
                'party_id' => $request->party_id,
                'broker_id' => $request->broker_id,
                'grain_id' => $request->grain_id,
                'quantity' => $qtyInQtl,
                'unit' => $request->unit,
                'rate' => $ratePerQtl,
                'total_amount' => $grandTotal,
                'payment_mode' => $request->payment_mode,
                'discount_percent' => $discountPercent,
                'discount_amount' => $discountAmount,
                'net_amount' => $netAmount,
                'amount_paid' => $amountPaid,
                'outstanding_amount' => $outstandingAmount,
                'po_no' => $request->po_no,
                'bags_count' => $request->bags_count,
                'truck_no' => $request->truck_no,
                'driver_name' => $request->driver_name,
                'driver_phone' => $request->driver_phone,
                'truck_fare' => $request->truck_fare,
                'freight_advance' => $request->freight_advance,
                'freight_balance' => $request->freight_balance,
                'notes' => $request->notes,
            ]);

            // Create New Ledger
            $partyId = $request->party_id;
            $lastEntry = \App\Models\Business\LedgerEntry::where('company_id', $companyId)
                ->where('party_id', $partyId)
                ->latest('id')
                ->first();

            $currentBalance = $lastEntry ? $lastEntry->balance_after : 0;
            $newBalance = $currentBalance + $netAmount;

            \App\Models\Business\LedgerEntry::create([
                'company_id' => $companyId,
                'party_id' => $partyId,
                'entry_type' => 'sale',
                'reference_type' => Sale::class,
                'reference_id' => $sale->id,
                'debit' => $netAmount,
                'credit' => $amountPaid > 0 ? $amountPaid : 0,
                'balance_after' => $newBalance - $amountPaid,
                'entry_date' => $request->date,
            ]);

            // Calculate new broker commission
            if ($request->broker_id) {
                $commissionRule = \App\Models\Business\BrokerCommissionRate::where('company_id', $companyId)
                    ->where('broker_id', $request->broker_id)
                    ->whereIn('applies_to', ['sale', 'both'])
                    ->first();

                if ($commissionRule) {
                    $commissionAmount = 0;
                    $qtyInKg = $qtyInQtl * 100;

                    if ($commissionRule->commission_type == 'per_quintal') {
                        $commissionAmount = $qtyInQtl * $commissionRule->rate;
                    } elseif ($commissionRule->commission_type == 'per_kg') {
                        $commissionAmount = $qtyInKg * $commissionRule->rate;
                    } elseif ($commissionRule->commission_type == 'percentage') {
                        $commissionAmount = ($grandTotal * $commissionRule->rate) / 100;
                    } elseif ($commissionRule->commission_type == 'fixed') {
                        $commissionAmount = $commissionRule->rate;
                    }

                    \App\Models\Business\BrokerCommissionEntry::create([
                        'company_id' => $companyId,
                        'broker_id' => $request->broker_id,
                        'reference_type' => Sale::class,
                        'reference_id' => $sale->id,
                        'date' => $request->date,
                        'quantity' => $qtyInQtl,
                        'rate' => $ratePerQtl,
                        'commission_type' => $commissionRule->commission_type,
                        'commission_rate' => $commissionRule->rate,
                        'commission_amount' => $commissionAmount
                    ]);
                }
            }

            // Recalculate Ledger for old party and new party (if changed)
            \App\Models\Business\LedgerEntry::recalculateForParty($companyId, $oldPartyId);
            if ($oldPartyId != $partyId) {
                \App\Models\Business\LedgerEntry::recalculateForParty($companyId, $partyId);
            }

            DB::commit();
            return redirect()->route('business.sales.index')->with('success', 'Sale updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update sale: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $companyId = auth()->user()->company_id;
        $sale = Sale::where('company_id', $companyId)
            ->with(['collections', 'saleLotAllocations'])
            ->findOrFail($id);

        // 1. Validation: Have any collections been made?
        if ($sale->collections && $sale->collections->count() > 0) {
            return back()->with('error', 'Cannot cancel sale: Payments have already been collected for this sale. Please delete the collections first.');
        }

        // 2. Validation: Broker Commission Paid?
        $brokerCommission = \App\Models\Business\BrokerCommissionEntry::where('reference_type', Sale::class)
            ->where('reference_id', $sale->id)
            ->first();

        if ($brokerCommission && $brokerCommission->payment_status !== 'pending') {
            return back()->with('error', 'Cannot cancel sale: The broker commission for this sale has already been paid. Please delete the commission payment first.');
        }

        DB::beginTransaction();
        try {
            // Restore Lots and Stock
            foreach ($sale->saleLotAllocations as $allocation) {
                $lot = \App\Models\Business\Lot::find($allocation->lot_id);
                if ($lot) {
                    $lot->remaining_quantity += $allocation->quantity_taken;
                    if ($lot->remaining_quantity > 0) {
                        $lot->status = 'open';
                    }
                    $lot->save();

                    // Restore Godown Stock
                    $godown = \App\Models\Business\Godown::find($lot->godown_id);
                    if ($godown) {
                        $godown->current_stock_in_quintals += $allocation->quantity_taken;
                        $godown->save();
                    }
                }
            }

            // Delete Inventory Logs (for this sale)
            \App\Models\Business\InventoryLog::where('company_id', $companyId)
                ->where('transaction_type', 'sale')
                ->where('date', $sale->date)
                ->whereIn('lot_id', $sale->saleLotAllocations->pluck('lot_id'))
                ->delete();

            // Revert Grain Stock
            // quantity is now stored in Quintals directly
            $qtyInQtl = $sale->quantity;

            $grainStock = \App\Models\Business\GrainStock::where('company_id', $companyId)
                ->where('grain_id', $sale->grain_id)
                ->first();
            if ($grainStock) {
                $grainStock->quantity += $qtyInQtl;
                $grainStock->save();
            }

            // Delete associated records
            \App\Models\Business\SaleLotAllocation::where('sale_id', $sale->id)->delete();
            \App\Models\Business\SaleCharge::where('sale_id', $sale->id)->delete();
            \App\Models\Business\SalePayment::where('sale_id', $sale->id)->delete();

            if ($brokerCommission) {
                $brokerCommission->delete();
            }

            // Delete Ledger Entries
            \App\Models\Business\LedgerEntry::where('reference_type', Sale::class)
                ->where('reference_id', $sale->id)
                ->delete();

            $partyId = $sale->party_id;

            // Delete the sale itself
            $sale->delete();

            // Recalculate Ledger for party
            \App\Models\Business\LedgerEntry::recalculateForParty($companyId, $partyId);

            DB::commit();
            return back()->with('success', 'Sale has been successfully canceled and all stocks/ledgers reversed.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to cancel sale: ' . $e->getMessage());
        }
    }
}
