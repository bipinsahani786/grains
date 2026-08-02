<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Core\User;
use App\Models\Business\PartyType;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class PartyController extends Controller
{
    public function index()
    {
        $parties = User::where('company_id', auth()->user()->company_id)
            ->where('role', 'party')
            ->with('partyType')
            ->get();
            
        $partyTypes = PartyType::whereNull('company_id')
            ->orWhere('company_id', auth()->user()->company_id)
            ->get();
            
        return view('business.parties.index', compact('parties', 'partyTypes'));
    }

    public function create()
    {
        $partyTypes = PartyType::whereNull('company_id')
            ->orWhere('company_id', auth()->user()->company_id)
            ->get();
            
        return view('business.parties.create', compact('partyTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'entity_type' => 'required|in:Company,Individual',
            'name' => 'required|string|max:255',
            'party_type_id' => 'required|string',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'nullable|string|min:8|confirmed',
            'opening_balance' => 'nullable|numeric',
            'opening_balance_type' => 'nullable|string|in:credit,debit',
            'role' => 'nullable|in:party,broker',
        ]);

        $partyTypeId = $this->resolvePartyType($request->party_type_id);

        $user = User::create([
            'company_id' => auth()->user()->company_id,
            'role' => $request->role ?? 'party',
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'aadhar_no' => $request->aadhar_no,
            'gst_no' => $request->gst_no,
            'party_type_id' => $partyTypeId,
            'password' => $request->password ? Hash::make($request->password) : null,
            'login_enabled' => $request->has('login_enabled'),
            'opening_balance' => $request->opening_balance ?? 0,
            'opening_balance_type' => $request->opening_balance_type ?? 'credit',
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'party' => $user
            ]);
        }

        return redirect()->route('business.parties.index')->with('success', 'Party created successfully.');
    }

    public function show(Request $request, $id)
    {
        $companyId = auth()->user()->company_id;
        $party = User::where('company_id', $companyId)
            ->whereIn('role', ['party', 'broker'])
            ->findOrFail($id);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $ledgerQuery = \App\Models\Business\LedgerEntry::where('company_id', $companyId)
            ->where('party_id', $party->id);
            
        $purchasesQuery = \App\Models\Business\Purchase::where('company_id', $companyId)
            ->where('party_id', $party->id)
            ->with('items.grain');
            
        $paymentsQuery = \App\Models\Business\Payment::where('company_id', $companyId)
            ->where('party_id', $party->id);

        if ($startDate && $endDate) {
            $ledgerQuery->whereBetween('entry_date', [$startDate, $endDate]);
            $purchasesQuery->whereBetween('date', [$startDate, $endDate]);
            $paymentsQuery->whereBetween('date', [$startDate, $endDate]);
        }

        $ledgerEntries = $ledgerQuery->latest('entry_date')->latest('id')->get();
        $purchases = $purchasesQuery->latest('date')->get();
        $payments = $paymentsQuery->latest('date')->get();

        $lastEntry = \App\Models\Business\LedgerEntry::where('company_id', $companyId)
            ->where('party_id', $party->id)
            ->latest('id')
            ->first();
            
        $currentBalance = $lastEntry ? $lastEntry->balance_after : $party->opening_balance;
        
        $totalPurchases = \App\Models\Business\Purchase::where('company_id', $companyId)
            ->where('party_id', $party->id)
            ->sum('total_amount');
        
        return view('business.parties.show', compact('party', 'ledgerEntries', 'purchases', 'payments', 'currentBalance', 'totalPurchases'));
    }

    public function edit($id)
    {
        $party = User::where('company_id', auth()->user()->company_id)
            ->where('role', 'party')
            ->findOrFail($id);
            
        $partyTypes = PartyType::whereNull('company_id')
            ->orWhere('company_id', auth()->user()->company_id)
            ->get();

        return view('business.parties.edit', compact('party', 'partyTypes'));
    }

    public function update(Request $request, $id)
    {
        $party = User::where('company_id', auth()->user()->company_id)
            ->where('role', 'party')
            ->findOrFail($id);

        $request->validate([
            'entity_type' => 'required|in:Company,Individual',
            'name' => 'required|string|max:255',
            'party_type_id' => 'required|string',
            'email' => 'nullable|email|unique:users,email,'.$id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $partyTypeId = $this->resolvePartyType($request->party_type_id);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'aadhar_no' => $request->aadhar_no,
            'gst_no' => $request->gst_no,
            'party_type_id' => $partyTypeId,
            'login_enabled' => $request->has('login_enabled'),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $party->update($data);

        return redirect()->route('business.parties.index')->with('success', 'Party updated successfully.');
    }

    public function destroy($id)
    {
        $party = User::where('company_id', auth()->user()->company_id)
            ->where('role', 'party')
            ->findOrFail($id);
            
        $party->delete();

        return redirect()->route('business.parties.index')->with('success', 'Party deleted successfully.');
    }

    private function resolvePartyType($input)
    {
        if (is_numeric($input)) {
            return $input;
        }

        // It's a new custom string
        $partyType = PartyType::firstOrCreate(
            [
                'company_id' => auth()->user()->company_id,
                'slug' => Str::slug($input),
            ],
            [
                'name' => $input
            ]
        );

        return $partyType->id;
    }
}
