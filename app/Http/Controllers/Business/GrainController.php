<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Business\Grain;
use Illuminate\Validation\Rule;

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

        Grain::create([
            'company_id' => auth()->user()->company_id,
            'name' => $request->name,
            'unit' => $request->unit,
            'opening_stock' => $request->opening_stock ?? 0,
        ]);

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

        return redirect()->route('business.grains.index')->with('success', 'Grain updated successfully.');
    }

    public function destroy($id)
    {
        $grain = Grain::where('company_id', auth()->user()->company_id)->findOrFail($id);
        $grain->delete();

        return redirect()->route('business.grains.index')->with('success', 'Grain deleted successfully.');
    }
}
