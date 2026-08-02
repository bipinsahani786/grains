<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Business\Godown;
use Illuminate\Support\Facades\Auth;

class GodownController extends Controller
{
    public function index()
    {
        $godowns = Godown::where('company_id', Auth::user()->company_id)
            ->withCount('lots')
            ->get();
            
        return view('business.godowns.index', compact('godowns'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'capacity_in_quintals' => 'required|numeric|min:1'
        ]);

        Godown::create([
            'company_id' => Auth::user()->company_id,
            'name' => $request->name,
            'location' => $request->location,
            'capacity_in_quintals' => $request->capacity_in_quintals,
            'current_stock_in_quintals' => 0
        ]);

        return redirect()->route('business.godowns.index')->with('success', 'Godown added successfully.');
    }

    public function update(Request $request, $id)
    {
        $godown = Godown::where('company_id', Auth::user()->company_id)->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'capacity_in_quintals' => 'required|numeric|min:1'
        ]);

        $godown->update($request->only('name', 'location', 'capacity_in_quintals'));

        return redirect()->route('business.godowns.index')->with('success', 'Godown updated successfully.');
    }

    public function destroy($id)
    {
        $godown = Godown::where('company_id', Auth::user()->company_id)->findOrFail($id);
        
        if ($godown->current_stock_in_quintals > 0 || $godown->lots()->exists()) {
            return back()->with('error', 'Cannot delete a Godown that contains stock or has associated lots.');
        }
        
        $godown->delete();
        return redirect()->route('business.godowns.index')->with('success', 'Godown deleted successfully.');
    }
}
