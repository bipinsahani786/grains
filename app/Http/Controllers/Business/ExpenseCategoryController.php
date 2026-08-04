<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Business\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseCategoryController extends Controller
{
    public function index()
    {
        $companyId = Auth::user()->company_id;
        $categories = ExpenseCategory::where(function ($q) use ($companyId) {
            $q->whereNull('company_id')->orWhere('company_id', $companyId);
        })->withCount('expenses')->orderBy('name')->get();

        return view('business.expenses.categories', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:100',
            'color' => 'nullable|string|max:20',
            'icon'  => 'nullable|string|max:50',
        ]);

        ExpenseCategory::create([
            'company_id' => Auth::user()->company_id,
            'name'       => $request->name,
            'color'      => $request->color ?? '#6c757d',
            'icon'       => $request->icon ?? 'feather-tag',
            'is_system'  => false,
        ]);

        return back()->with('success', "Category '{$request->name}' created successfully.");
    }

    public function update(Request $request, ExpenseCategory $expenseCategory)
    {
        if ($expenseCategory->is_system) {
            return back()->with('error', 'Cannot edit a system default category.');
        }

        $request->validate([
            'name'  => 'required|string|max:100',
            'color' => 'nullable|string|max:20',
            'icon'  => 'nullable|string|max:50',
        ]);

        $expenseCategory->update([
            'name'  => $request->name,
            'color' => $request->color ?? $expenseCategory->color,
            'icon'  => $request->icon ?? $expenseCategory->icon,
        ]);

        return back()->with('success', 'Category updated.');
    }

    public function destroy(ExpenseCategory $expenseCategory)
    {
        if ($expenseCategory->is_system) {
            return back()->with('error', 'Cannot delete a system default category.');
        }
        if ($expenseCategory->expenses()->count() > 0) {
            return back()->with('error', 'Cannot delete category: it has expenses linked to it. Please reassign those expenses first.');
        }

        $expenseCategory->delete();
        return back()->with('success', 'Category deleted.');
    }
}
