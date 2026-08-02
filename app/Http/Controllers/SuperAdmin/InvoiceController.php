<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\System\SubscriptionInvoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        $data = SubscriptionInvoice::with(['company', 'subscription'])->latest()->paginate(10);
        return view('superadmin.invoices.index', compact('data'));
    }

    public function create()
    {
        // Typically invoices are generated automatically, but we can provide a basic form if needed
        return view('superadmin.invoices.create');
    }

    public function store(Request $request)
    {
        // For now, this is a placeholder
        return redirect()->route('superadmin.invoices.index')->with('success', 'Invoice created successfully.');
    }

    public function show($id)
    {
        $invoice = SubscriptionInvoice::with(['company', 'subscription.plan'])->findOrFail($id);
        return view('superadmin.invoices.show', compact('invoice'));
    }

    public function edit($id)
    {
        $invoice = SubscriptionInvoice::findOrFail($id);
        return view('superadmin.invoices.edit', compact('invoice'));
    }

    public function update(Request $request, $id)
    {
        $invoice = SubscriptionInvoice::findOrFail($id);
        
        $request->validate([
            'status' => 'required|string|in:paid,unpaid,failed',
        ]);

        $invoice->update(['status' => $request->status]);

        return redirect()->route('superadmin.invoices.index')->with('success', 'Invoice status updated.');
    }

    public function destroy($id)
    {
        $invoice = SubscriptionInvoice::findOrFail($id);
        $invoice->delete();
        return redirect()->route('superadmin.invoices.index')->with('success', 'Invoice deleted successfully.');
    }
}