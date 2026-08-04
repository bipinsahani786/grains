<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Business\Expense;
use App\Models\Business\ExpenseCategory;
use App\Models\Core\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;

        $query = Expense::with(['category', 'vendorParty'])
            ->where('company_id', $companyId);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('payment_mode')) {
            $query->where('payment_mode', $request->payment_mode);
        }

        $expenses = $query->orderBy('date', 'desc')->orderBy('id', 'desc')->paginate(25)->withQueryString();

        // Summary Stats
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth   = Carbon::now()->endOfMonth();
        $startOfYear  = Carbon::now()->startOfYear();

        $monthTotal = Expense::where('company_id', $companyId)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])->sum('amount');
        $yearTotal = Expense::where('company_id', $companyId)
            ->whereBetween('date', [$startOfYear, Carbon::now()])->sum('amount');

        $topCategory = Expense::selectRaw('category_id, SUM(amount) as total')
            ->where('company_id', $companyId)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->with('category')
            ->first();

        $categories = ExpenseCategory::where(function ($q) use ($companyId) {
            $q->whereNull('company_id')->orWhere('company_id', $companyId);
        })->orderBy('name')->get();

        return view('business.expenses.index', compact(
            'expenses', 'categories', 'monthTotal', 'yearTotal', 'topCategory'
        ));
    }

    public function create()
    {
        $companyId = Auth::user()->company_id;
        $categories = ExpenseCategory::where(function ($q) use ($companyId) {
            $q->whereNull('company_id')->orWhere('company_id', $companyId);
        })->orderBy('name')->get();

        $parties = User::where('company_id', $companyId)->whereIn('role', ['party'])->get();

        return view('business.expenses.create', compact('categories', 'parties'));
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;

        $validated = $request->validate([
            'date'                 => 'required|date',
            'category_id'          => 'required|exists:expense_categories,id',
            'amount'               => 'required|numeric|min:0.01',
            'payment_mode'         => 'required|in:cash,bank,upi,cheque',
            'description'          => 'nullable|string|max:500',
            'reference_no'         => 'nullable|string|max:100',
            'vendor_name'          => 'nullable|string|max:200',
            'vendor_party_id'      => 'nullable|exists:users,id',
            'notes'                => 'nullable|string',
            'is_recurring'         => 'nullable|boolean',
            'recurring_frequency'  => 'nullable|in:monthly,weekly,yearly',
            'recurring_next_date'  => 'nullable|date',
        ]);

        // Auto-generate expense number
        $lastSeq = Expense::where('company_id', $companyId)->max('sequence_no') ?? 0;
        $nextSeq = $lastSeq + 1;
        $expenseNo = 'EXP-' . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);

        $expense = Expense::create(array_merge($validated, [
            'company_id'  => $companyId,
            'expense_no'  => $expenseNo,
            'sequence_no' => $nextSeq,
            'created_by'  => Auth::id(),
            'is_recurring' => $request->boolean('is_recurring'),
            'recurring_next_date' => $request->boolean('is_recurring') ? $request->recurring_next_date : null,
            'recurring_frequency' => $request->boolean('is_recurring') ? $request->recurring_frequency : null,
        ]));

        return redirect()->route('business.expenses.index')
            ->with('success', "Expense {$expenseNo} recorded successfully.");
    }

    public function edit(Expense $expense)
    {
        $this->authorizeExpense($expense);

        $companyId = Auth::user()->company_id;
        $categories = ExpenseCategory::where(function ($q) use ($companyId) {
            $q->whereNull('company_id')->orWhere('company_id', $companyId);
        })->orderBy('name')->get();

        $parties = User::where('company_id', $companyId)->whereIn('role', ['party'])->get();

        return view('business.expenses.edit', compact('expense', 'categories', 'parties'));
    }

    public function update(Request $request, Expense $expense)
    {
        $this->authorizeExpense($expense);

        $validated = $request->validate([
            'date'                 => 'required|date',
            'category_id'          => 'required|exists:expense_categories,id',
            'amount'               => 'required|numeric|min:0.01',
            'payment_mode'         => 'required|in:cash,bank,upi,cheque',
            'description'          => 'nullable|string|max:500',
            'reference_no'         => 'nullable|string|max:100',
            'vendor_name'          => 'nullable|string|max:200',
            'vendor_party_id'      => 'nullable|exists:users,id',
            'notes'                => 'nullable|string',
            'is_recurring'         => 'nullable|boolean',
            'recurring_frequency'  => 'nullable|in:monthly,weekly,yearly',
            'recurring_next_date'  => 'nullable|date',
        ]);

        $expense->update(array_merge($validated, [
            'is_recurring'        => $request->boolean('is_recurring'),
            'recurring_next_date' => $request->boolean('is_recurring') ? $request->recurring_next_date : null,
            'recurring_frequency' => $request->boolean('is_recurring') ? $request->recurring_frequency : null,
        ]));

        return redirect()->route('business.expenses.index')
            ->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        $this->authorizeExpense($expense);
        $expense->delete();
        return back()->with('success', 'Expense deleted successfully.');
    }

    private function authorizeExpense(Expense $expense)
    {
        if ($expense->company_id !== Auth::user()->company_id) {
            abort(403);
        }
    }
}
