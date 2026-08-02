<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Business\PartyType;
use Illuminate\Support\Str;

class PartyTypeController extends Controller
{
    public function index()
    {
        $partyTypes = PartyType::whereNull('company_id')
                        ->orWhere('company_id', auth()->user()->company_id)
                        ->get();
        return view('business.party-types.index', compact('partyTypes'));
    }

    public function create()
    {
        return view('business.party-types.create');
    }

    public function store(Request $request)
    {
        $slug = Str::slug($request->name);

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('party_types', 'slug')->where(function ($query) {
                    return $query->where('company_id', auth()->user()->company_id);
                })
            ],
        ]);

        PartyType::create([
            'company_id' => auth()->user()->company_id,
            'name' => $request->name,
            'slug' => $slug,
        ]);

        return redirect()->route('business.party-types.index')->with('success', 'Party Type added successfully.');
    }

    public function edit(PartyType $partyType)
    {
        // Don't allow editing global types
        if (is_null($partyType->company_id) || $partyType->company_id !== auth()->user()->company_id) {
            abort(403, 'Unauthorized action.');
        }

        return view('business.party-types.edit', compact('partyType'));
    }

    public function update(Request $request, PartyType $partyType)
    {
        if (is_null($partyType->company_id) || $partyType->company_id !== auth()->user()->company_id) {
            abort(403, 'Unauthorized action.');
        }

        $slug = Str::slug($request->name);

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('party_types', 'slug')
                    ->where(function ($query) {
                        return $query->where('company_id', auth()->user()->company_id);
                    })
                    ->ignore($partyType->id)
            ],
        ]);

        $partyType->update([
            'name' => $request->name,
            'slug' => $slug,
        ]);

        return redirect()->route('business.party-types.index')->with('success', 'Party Type updated successfully.');
    }

    public function destroy(PartyType $partyType)
    {
        if (is_null($partyType->company_id) || $partyType->company_id !== auth()->user()->company_id) {
            abort(403, 'Unauthorized action.');
        }

        $partyType->delete();

        return redirect()->route('business.party-types.index')->with('success', 'Party Type deleted successfully.');
    }
}
