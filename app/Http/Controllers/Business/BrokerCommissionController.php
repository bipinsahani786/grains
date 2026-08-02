<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Business\BrokerCommissionRate;
use App\Models\Business\Grain;
use App\Models\Core\User;
use Illuminate\Http\Request;

class BrokerCommissionController extends Controller
{
    public function index()
    {
        $companyId = auth()->user()->company_id;
        
        $commissions = BrokerCommissionRate::with(['broker', 'grain'])
            ->where('company_id', $companyId)
            ->get();
            
        // We will pass brokers (who are not admins) and grains to the view for the "Add New" modal/form
        $brokers = User::where('company_id', $companyId)
            ->where('role', 'broker')
            ->get();
            
        $grains = Grain::where('company_id', $companyId)->get();
            
        return view('business.financials.commissions.index', compact('commissions', 'brokers', 'grains'));
    }

    public function storeBroker(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'phone'           => 'nullable|string|max:20',
            'address'         => 'nullable|string',
            'commission_type' => 'nullable|string',
            'rate'            => 'nullable|numeric|min:0',
            'applies_to'      => 'nullable|in:purchase,sale,both',
        ]);

        $companyId = auth()->user()->company_id;

        $broker = \App\Models\Core\User::create([
            'company_id' => $companyId,
            'role'       => 'broker',
            'name'       => $request->name,
            'phone'      => $request->phone,
            'address'    => $request->address,
            'email'      => strtolower(preg_replace('/\s+/', '', $request->name)) . rand(100, 999) . '@broker.com',
            'password'   => bcrypt('password'),
        ]);

        // Optionally create default commission rate
        if ($request->filled('commission_type') && $request->filled('rate') && $request->filled('applies_to')) {
            BrokerCommissionRate::create([
                'company_id'      => $companyId,
                'broker_id'       => $broker->id,
                'grain_id'        => null,
                'commission_type' => $request->commission_type,
                'rate'            => $request->rate,
                'applies_to'      => $request->applies_to,
            ]);
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'broker' => $broker]);
        }

        return redirect()->route('business.financials.commissions.index')->with('success', 'Broker created successfully!');
    }


    public function store(Request $request)
    {
        $request->validate([
            'broker_name' => 'required|string|max:255',
            'commission_type' => 'required|string', // e.g. per_quintal, per_kg, percentage
            'rate' => 'required|numeric|min:0',
            'applies_to' => 'required|in:purchase,sale,both',
        ]);

        $companyId = auth()->user()->company_id;

        // Find or create broker by name
        $broker = User::where('company_id', $companyId)
            ->where('role', 'broker')
            ->where('name', $request->broker_name)
            ->first();

        if (!$broker) {
            $broker = User::create([
                'name' => $request->broker_name,
                'email' => strtolower(str_replace(' ', '', $request->broker_name)) . rand(100,999) . '@broker.com',
                'password' => bcrypt('password'),
                'company_id' => $companyId,
                'role' => 'broker',
            ]);
        }

        // Check if a rule already exists for this broker and applies_to
        $exists = BrokerCommissionRate::where('company_id', $companyId)
            ->where('broker_id', $broker->id)
            ->whereNull('grain_id')
            ->where('applies_to', $request->applies_to)
            ->exists();
            
        if ($exists) {
            return back()->with('error', 'A commission rule for this broker and transaction type already exists. Please edit the existing one.');
        }

        BrokerCommissionRate::create([
            'company_id' => $companyId,
            'broker_id' => $broker->id,
            'grain_id' => null,
            'commission_type' => $request->commission_type,
            'rate' => $request->rate,
            'applies_to' => $request->applies_to,
        ]);

        return redirect()->route('business.financials.commissions.index')->with('success', 'Broker commission rate added successfully!');
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'commission_type' => 'required|string',
            'rate' => 'required|numeric|min:0',
            'applies_to' => 'required|in:purchase,sale,both',
        ]);

        $companyId = auth()->user()->company_id;
        $commission = BrokerCommissionRate::where('company_id', $companyId)->findOrFail($id);
        
        $commission->update([
            'commission_type' => $request->commission_type,
            'rate' => $request->rate,
            'applies_to' => $request->applies_to,
        ]);

        return redirect()->route('business.financials.commissions.index')->with('success', 'Broker commission rate updated successfully!');
    }

    public function destroy(string $id)
    {
        $companyId = auth()->user()->company_id;
        $commission = BrokerCommissionRate::where('company_id', $companyId)->findOrFail($id);
        
        $commission->delete();

        return redirect()->route('business.financials.commissions.index')->with('success', 'Broker commission rate deleted successfully!');
    }

    /**
     * Broker profile: analytics + commission ledger.
     */
    public function profile(string $broker)
    {
        $companyId = auth()->user()->company_id;
        $brokerUser = User::where('company_id', $companyId)->where('role', 'broker')->findOrFail($broker);

        $entries = \App\Models\Business\BrokerCommissionEntry::with([])
            ->where('company_id', $companyId)
            ->where('broker_id', $broker)
            ->orderByDesc('date')
            ->get();

        $stats = [
            'total_earned'  => $entries->sum('commission_amount'),
            'total_paid'    => $entries->sum('amount_paid'),
            'total_pending' => $entries->where('payment_status', 'pending')->sum(fn($e) => max(0, $e->commission_amount - $e->amount_paid)),
            'entry_count'   => $entries->count(),
        ];

        return view('business.financials.brokers.profile', compact('brokerUser', 'entries', 'stats'));
    }

    /**
     * Mark a commission entry as paid (partial or full).
     */
    public function markPaid(Request $request, string $entry)
    {
        $request->validate([
            'amount'    => 'required|numeric|min:0.01',
            'paid_mode' => 'required|string|in:Cash,Cheque,Online,NEFT,UPI',
            'paid_at'   => 'required|date',
            'payment_notes' => 'nullable|string|max:500',
        ]);

        $companyId = auth()->user()->company_id;
        $commission = \App\Models\Business\BrokerCommissionEntry::where('company_id', $companyId)->findOrFail($entry);

        $newPaid = min(
            $commission->amount_paid + (float)$request->amount,
            $commission->commission_amount
        );

        $commission->update([
            'amount_paid'    => $newPaid,
            'paid_at'        => $request->paid_at,
            'paid_mode'      => $request->paid_mode,
            'payment_notes'  => $request->payment_notes,
            'payment_status' => $newPaid >= $commission->commission_amount ? 'paid' : 'partial',
        ]);

        return response()->json([
            'success'         => true,
            'message'         => 'Commission payment of ₹' . number_format($request->amount, 2) . ' recorded.',
            'amount_paid'     => $newPaid,
            'pending_amount'  => max(0, $commission->commission_amount - $newPaid),
            'payment_status'  => $commission->fresh()->payment_status,
        ]);
    }
}

