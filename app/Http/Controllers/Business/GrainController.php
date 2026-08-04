<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Business\Grain;
use Illuminate\Validation\Rule;
use App\Helpers\UnitHelper;
use App\Models\System\Company;
use App\Models\Business\Lot;
use App\Models\Business\GrainStock;
use App\Models\Business\InventoryLog;

class GrainController extends Controller
{
    public function index()
    {
        $grains = Grain::where('company_id', auth()->user()->company_id)->get();
        return view('business.grains.index', compact('grains'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('grains', 'name')->where(function ($query) {
                    return $query->where('company_id', auth()->user()->company_id);
                })
            ],
            'unit' => 'required|string|max:50',
            'opening_stock' => 'nullable|numeric|min:0',
        ]);

        $grain = Grain::create([
            'company_id' => auth()->user()->company_id,
            'name' => $request->name,
            'unit' => $request->unit,
            'opening_stock' => $request->opening_stock ?? 0,
        ]);

        $this->syncOpeningStock($grain);

        return redirect()->route('business.grains.index')->with('success', 'Grain added successfully.');
    }

    public function update(Request $request, $id)
    {
        $grain = Grain::where('company_id', auth()->user()->company_id)->findOrFail($id);

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('grains', 'name')
                    ->where(function ($query) {
                        return $query->where('company_id', auth()->user()->company_id);
                    })
                    ->ignore($grain->id)
            ],
            'unit' => 'required|string|max:50',
            'opening_stock' => 'nullable|numeric|min:0',
        ]);

        $grain->update([
            'name' => $request->name,
            'unit' => $request->unit,
            'opening_stock' => $request->opening_stock ?? 0,
        ]);

        $this->syncOpeningStock($grain);

        return redirect()->route('business.grains.index')->with('success', 'Grain updated successfully.');
    }

    public function destroy($id)
    {
        $grain = Grain::where('company_id', auth()->user()->company_id)->findOrFail($id);
        $grain->delete();

        return redirect()->route('business.grains.index')->with('success', 'Grain deleted successfully.');
    }

    private function syncOpeningStock(Grain $grain)
    {
        $companyId = $grain->company_id;
        $company = Company::find($companyId);
        $qtyInQtl = UnitHelper::toQtl($grain->opening_stock, $grain->unit, $company->bag_weight_kg ?? 50);

        $lot = Lot::where('company_id', $companyId)
            ->where('grain_id', $grain->id)
            ->where('lot_no', 'LIKE', 'OPENING-%')
            ->first();

        if (!$lot) {
            if ($qtyInQtl > 0) {
                // Create Lot
                $lot = Lot::create([
                    'company_id' => $companyId,
                    'lot_no' => 'OPENING-' . date('Ymd') . '-' . $grain->id,
                    'grain_id' => $grain->id,
                    'initial_quantity' => $qtyInQtl,
                    'remaining_quantity' => $qtyInQtl,
                    'rate' => 0,
                    'status' => 'open'
                ]);

                // Update GrainStock
                $stock = GrainStock::firstOrCreate(
                    ['company_id' => $companyId, 'grain_id' => $grain->id],
                    ['quantity' => 0]
                );
                $stock->quantity += $qtyInQtl;
                $stock->save();

                // Create Log
                InventoryLog::create([
                    'company_id' => $companyId,
                    'grain_id' => $grain->id,
                    'lot_id' => $lot->id,
                    'transaction_type' => 'opening_stock',
                    'quantity_changed' => $qtyInQtl,
                    'balance_after' => $stock->quantity,
                    'date' => now(),
                    'created_by' => auth()->id()
                ]);
            }
        } else {
            $diffQtl = $qtyInQtl - $lot->initial_quantity;
            if ($diffQtl != 0) {
                $lot->initial_quantity = $qtyInQtl;
                $lot->remaining_quantity += $diffQtl;
                
                if ($lot->remaining_quantity <= 0.001) {
                    $lot->remaining_quantity = 0;
                    $lot->status = 'closed';
                } else {
                    $lot->status = 'open';
                }
                $lot->save();

                $stock = GrainStock::firstOrCreate(
                    ['company_id' => $companyId, 'grain_id' => $grain->id],
                    ['quantity' => 0]
                );
                $stock->quantity += $diffQtl;
                $stock->save();

                InventoryLog::create([
                    'company_id' => $companyId,
                    'grain_id' => $grain->id,
                    'lot_id' => $lot->id,
                    'transaction_type' => 'opening_stock_adjustment',
                    'quantity_changed' => $diffQtl,
                    'balance_after' => $stock->quantity,
                    'date' => now(),
                    'created_by' => auth()->id()
                ]);
            }
        }
    }
}
