<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Business\Lot;
use App\Models\Business\GrainStock;
use App\Models\Business\StockAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockAdjustmentController extends Controller
{
    public function index()
    {
        $companyId = auth()->user()->company_id;
        
        $adjustments = StockAdjustment::with(['lot', 'grain', 'user'])
            ->where('company_id', $companyId)
            ->latest()
            ->get();
            
        $lots = Lot::with('grain')
            ->where('company_id', $companyId)
            ->where('status', 'open')
            ->get();
            
        return view('business.inventory.adjustments.index', compact('adjustments', 'lots'));
    }

    public function create()
    {
        $companyId = auth()->user()->company_id;
        
        $lots = Lot::with('grain')
            ->where('company_id', $companyId)
            ->where('status', 'open')
            ->get();
            
        return view('business.inventory.adjustments.create', compact('lots'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'lot_id' => 'required|exists:lots,id',
            'date' => 'required|date',
            'quantity_after' => 'required|numeric|min:0',
            'reason' => 'required|string',
        ]);

        $companyId = auth()->user()->company_id;
        $lot = Lot::where('company_id', $companyId)->findOrFail($request->lot_id);
        
        $quantityBefore = $lot->remaining_quantity;
        $quantityAfter = $request->quantity_after;
        $difference = $quantityAfter - $quantityBefore; // negative if stock decreased

        if ($difference == 0) {
            return back()->with('error', 'New quantity is exactly the same as old quantity. No adjustment needed.');
        }

        try {
            DB::transaction(function () use ($lot, $companyId, $request, $quantityBefore, $quantityAfter, $difference) {
                // 1. Create the adjustment record
                StockAdjustment::create([
                    'company_id' => $companyId,
                    'lot_id' => $lot->id,
                    'grain_id' => $lot->grain_id,
                    'quantity_before' => $quantityBefore,
                    'quantity_after' => $quantityAfter,
                    'reason' => $request->reason,
                    'notes' => $request->notes,
                    'adjusted_by' => auth()->id(),
                    'date' => $request->date,
                ]);

                // 2. Update Lot remaining quantity
                $lot->remaining_quantity = $quantityAfter;
                if ($quantityAfter == 0) {
                    $lot->status = 'closed';
                }
                $lot->save();

                // 3. Update total Grain Stock
                $stock = GrainStock::where('company_id', $companyId)
                    ->where('grain_id', $lot->grain_id)
                    ->first();
                    
                if ($stock) {
                    $stock->quantity += $difference;
                    $stock->save();

                    // 4. Create Inventory Log
                    \App\Models\Business\InventoryLog::create([
                        'company_id' => $companyId,
                        'grain_id' => $lot->grain_id,
                        'godown_id' => $lot->godown_id,
                        'lot_id' => $lot->id,
                        'transaction_type' => 'adjustment',
                        'quantity_changed' => $difference,
                        'balance_after' => $stock->quantity,
                        'date' => $request->date,
                        'created_by' => auth()->id()
                    ]);
                }
            });

            return redirect()->route('business.inventory.adjustments.index')
                ->with('success', 'Stock adjustment recorded successfully.');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to adjust stock: ' . $e->getMessage())->withInput();
        }
    }
}
