<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\System\Company;
use App\Models\System\Plan;
use App\Models\System\Subscription;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index()
    {
        $data = Company::with(['subscriptions' => function ($q) {
            $q->where('status', 'active')->with('plan');
        }])->latest()->paginate(10);
        return view('superadmin.companies.index', compact('data'));
    }

    public function create()
    {
        $plans = Plan::where('is_active', true)->get();
        return view('superadmin.companies.create', compact('plans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:companies,email|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:20',
            'gstin' => ['nullable', 'string', 'max:15', 'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/'],
            'type' => 'nullable|string|in:trader,broker,commission_agent',
            'address' => 'nullable|string|max:500',
            'plan_id' => 'nullable|exists:plans,id',
            'is_active' => 'nullable'
        ], [
            'gstin.regex' => 'Please enter a valid 15-character GSTIN (e.g., 22AAAAA0000A1Z5).',
            'email.unique' => 'This email is already in use by a company or user.',
        ]);

        $validated['is_active'] = $request->has('is_active');

        // Extract password and remove from validated data for company creation
        $password = $validated['password'];
        unset($validated['password']);

        $company = Company::create($validated);

        // Create the primary admin user for this company
        \App\Models\Core\User::create([
            'name' => $company->name . ' Admin',
            'email' => $company->email,
            'password' => \Illuminate\Support\Facades\Hash::make($password),
            'company_id' => $company->id,
            'role' => 'admin',
            'phone' => $company->phone,
            'login_enabled' => true,
        ]);

        if (!empty($validated['plan_id'])) {
            $plan = Plan::find($validated['plan_id']);
            $subscription = Subscription::create([
                'company_id' => $company->id,
                'plan_id' => $validated['plan_id'],
                'status' => 'active',
                'current_period_start' => now(),
                'current_period_end' => now()->addMonth(),
            ]);

            // Generate Invoice automatically
            \App\Models\System\SubscriptionInvoice::create([
                'company_id' => $company->id,
                'subscription_id' => $subscription->id,
                'amount' => $plan ? $plan->price_monthly : 0,
                'status' => 'paid', // Defaulting to paid as assigned by SuperAdmin
                'invoice_number' => 'INV-' . strtoupper(uniqid()),
                'due_at' => now(),
                'paid_at' => now(),
            ]);
        }

        return redirect()->route('superadmin.companies.index')->with('success', 'Company and Admin User created successfully.');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $company = Company::findOrFail($id);
        $plans = Plan::where('is_active', true)->get();
        $subscription = Subscription::where('company_id', $id)->where('status', 'active')->first();
        return view('superadmin.companies.edit', compact('company', 'plans', 'subscription'));
    }

    public function update(Request $request, $id)
    {
        $company = Company::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:companies,email,' . $id,
            'password' => 'nullable|string|min:6',
            'phone' => 'nullable|string|max:20',
            'gstin' => ['nullable', 'string', 'max:15', 'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/'],
            'type' => 'nullable|string|in:trader,broker,commission_agent',
            'address' => 'nullable|string|max:500',
            'plan_id' => 'nullable|exists:plans,id',
            'is_active' => 'nullable'
        ], [
            'gstin.regex' => 'Please enter a valid 15-character GSTIN (e.g., 22AAAAA0000A1Z5).',
            'email.unique' => 'A company with this email already exists.',
        ]);

        $validated['is_active'] = $request->has('is_active');
        
        $password = $validated['password'] ?? null;
        unset($validated['password']);
        
        $company->update($validated);

        // Find primary admin user and update email (and password if provided)
        $adminUser = \App\Models\Core\User::where('company_id', $company->id)->where('role', 'admin')->first();
        if ($adminUser) {
            $adminUser->email = $company->email;
            if ($password) {
                $adminUser->password = \Illuminate\Support\Facades\Hash::make($password);
            }
            $adminUser->save();
        }

        if (!empty($validated['plan_id'])) {
            $subscription = Subscription::where('company_id', $id)->where('status', 'active')->first();
            if ($subscription) {
                $subscription->update(['plan_id' => $validated['plan_id']]);
                // We don't generate a new invoice for just updating the plan, normally it's handled via prorating or separate billing flow
            } else {
                $plan = Plan::find($validated['plan_id']);
                $newSubscription = Subscription::create([
                    'company_id' => $company->id,
                    'plan_id' => $validated['plan_id'],
                    'status' => 'active',
                    'current_period_start' => now(),
                    'current_period_end' => now()->addMonth(),
                ]);

                // Generate Invoice automatically
                \App\Models\System\SubscriptionInvoice::create([
                    'company_id' => $company->id,
                    'subscription_id' => $newSubscription->id,
                    'amount' => $plan ? $plan->price_monthly : 0,
                    'status' => 'paid', // Defaulting to paid as assigned by SuperAdmin
                    'invoice_number' => 'INV-' . strtoupper(uniqid()),
                    'due_at' => now(),
                    'paid_at' => now(),
                ]);
            }
        }

        return redirect()->route('superadmin.companies.index')->with('success', 'Company updated successfully.');
    }

    public function destroy($id)
    {
        $company = Company::findOrFail($id);

        // Check for active users under this company
        $userCount = \App\Models\Core\User::where('company_id', $id)->count();
        if ($userCount > 0) {
            return redirect()->route('superadmin.companies.index')
                ->with('error', "Cannot delete this company. It has {$userCount} user(s) associated with it. Please remove or reassign them first.");
        }

        // Check for active subscriptions
        $activeSubscriptions = Subscription::where('company_id', $id)->where('status', 'active')->count();
        if ($activeSubscriptions > 0) {
            return redirect()->route('superadmin.companies.index')
                ->with('error', 'Cannot delete this company. It has active subscriptions. Please cancel them first.');
        }

        // Safe to delete — also clean up inactive subscriptions
        Subscription::where('company_id', $id)->delete();
        $company->delete();
        return redirect()->route('superadmin.companies.index')->with('success', 'Company deleted successfully.');
    }
}