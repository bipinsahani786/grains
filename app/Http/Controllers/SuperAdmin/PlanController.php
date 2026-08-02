<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\System\Plan;
use App\Models\System\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlanController extends Controller
{
    public function index()
    {
        $data = Plan::withCount(['subscriptions' => function ($q) {
            $q->where('status', 'active');
        }])->latest()->paginate(10);
        return view('superadmin.plans.index', compact('data'));
    }

    public function create()
    {
        return view('superadmin.plans.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:plans,name',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'max_staff_users' => 'required|integer|min:-1',
            'max_parties' => 'required|integer|min:-1',
            'max_transactions_month' => 'required|integer|min:-1',
            'features' => 'nullable|array',
            'features.*' => 'string',
            'is_active' => 'nullable'
        ], [
            'name.unique' => 'A plan with this name already exists.',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');

        // Features come as array from checkboxes
        $validated['features'] = $request->input('features', []);

        Plan::create($validated);

        return redirect()->route('superadmin.plans.index')->with('success', 'Plan created successfully.');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $plan = Plan::findOrFail($id);
        return view('superadmin.plans.edit', compact('plan'));
    }

    public function update(Request $request, $id)
    {
        $plan = Plan::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:plans,name,' . $id,
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'max_staff_users' => 'required|integer|min:-1',
            'max_parties' => 'required|integer|min:-1',
            'max_transactions_month' => 'required|integer|min:-1',
            'features' => 'nullable|array',
            'features.*' => 'string',
            'is_active' => 'nullable'
        ], [
            'name.unique' => 'A plan with this name already exists.',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');

        // Features come as array from checkboxes
        $validated['features'] = $request->input('features', []);

        $plan->update($validated);

        return redirect()->route('superadmin.plans.index')->with('success', 'Plan updated successfully.');
    }

    public function destroy($id)
    {
        $plan = Plan::findOrFail($id);

        // Check for active subscriptions using this plan
        $activeCount = Subscription::where('plan_id', $id)->where('status', 'active')->count();
        if ($activeCount > 0) {
            return redirect()->route('superadmin.plans.index')
                ->with('error', "Cannot delete this plan. It has {$activeCount} active subscription(s). Please migrate them to another plan first.");
        }

        $plan->delete();
        return redirect()->route('superadmin.plans.index')->with('success', 'Plan deleted successfully.');
    }
}