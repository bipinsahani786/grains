@extends('layouts.app')

@section('content')
<div class="nxl-content">
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Expenses</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('business.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Expenses</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="page-header-right-items">
                <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                    <a href="{{ route('business.expenses.create') }}" class="btn btn-primary">
                        <i class="feather-plus me-1"></i> Add Expense
                    </a>
                    <a href="{{ route('business.expenses.categories.index') }}" class="btn btn-light">
                        <i class="feather-tag me-1"></i> Manage Categories
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="main-content">
        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="feather-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="feather-alert-circle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Summary Cards --}}
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-soft-danger text-danger" style="width:52px;height:52px;flex-shrink:0;">
                            <i class="feather-trending-down fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small">This Month</div>
                            <div class="fs-4 fw-bold text-dark">₹{{ number_format($monthTotal, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-soft-warning text-warning" style="width:52px;height:52px;flex-shrink:0;">
                            <i class="feather-calendar fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small">This Year</div>
                            <div class="fs-4 fw-bold text-dark">₹{{ number_format($yearTotal, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-soft-info text-info" style="width:52px;height:52px;flex-shrink:0;">
                            <i class="feather-bar-chart-2 fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Top Category (Month)</div>
                            <div class="fs-5 fw-bold text-dark">
                                @if($topCategory && $topCategory->category)
                                    {{ $topCategory->category->name }}
                                    <small class="text-muted fs-6"> — ₹{{ number_format($topCategory->total, 2) }}</small>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small text-muted mb-1">From Date</label>
                        <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted mb-1">To Date</label>
                        <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted mb-1">Category</label>
                        <select name="category_id" class="form-select form-select-sm">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted mb-1">Payment Mode</label>
                        <select name="payment_mode" class="form-select form-select-sm">
                            <option value="">All Modes</option>
                            <option value="cash" {{ request('payment_mode') == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="bank" {{ request('payment_mode') == 'bank' ? 'selected' : '' }}>Bank</option>
                            <option value="upi" {{ request('payment_mode') == 'upi' ? 'selected' : '' }}>UPI</option>
                            <option value="cheque" {{ request('payment_mode') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm flex-fill">Filter</button>
                        <a href="{{ route('business.expenses.index') }}" class="btn btn-light btn-sm flex-fill">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Table --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Expense No</th>
                                <th>Date</th>
                                <th>Category</th>
                                <th>Description / Vendor</th>
                                <th>Mode</th>
                                <th>Recurring</th>
                                <th class="text-end">Amount</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($expenses as $expense)
                                <tr>
                                    <td>
                                        <span class="fw-bold text-primary">{{ $expense->expense_no ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ \Carbon\Carbon::parse($expense->date)->format('d M, Y') }}</div>
                                    </td>
                                    <td>
                                        @if($expense->category)
                                            <span class="badge rounded-pill" style="background-color: {{ $expense->category->color }}20; color: {{ $expense->category->color }}; border: 1px solid {{ $expense->category->color }}40;">
                                                <i class="{{ $expense->category->icon }} me-1" style="font-size:0.7rem;"></i>
                                                {{ $expense->category->name }}
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ $expense->description ?? '—' }}</div>
                                        @if($expense->vendor_name || $expense->vendorParty)
                                            <small class="text-muted">
                                                <i class="feather-user me-1"></i>
                                                {{ $expense->vendorParty ? $expense->vendorParty->name : $expense->vendor_name }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $modeColors = ['cash'=>'success','bank'=>'primary','upi'=>'info','cheque'=>'warning'];
                                            $modeColor = $modeColors[$expense->payment_mode] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-soft-{{ $modeColor }} text-{{ $modeColor }} text-capitalize">
                                            {{ strtoupper($expense->payment_mode) }}
                                        </span>
                                        @if($expense->reference_no)
                                            <div><small class="text-muted">Ref: {{ $expense->reference_no }}</small></div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($expense->is_recurring)
                                            <span class="badge bg-soft-purple text-purple" style="background:#f3e8ff;color:#7c3aed;">
                                                <i class="feather-refresh-cw me-1"></i>{{ ucfirst($expense->recurring_frequency) }}
                                            </span>
                                            @if($expense->recurring_next_date)
                                                <div><small class="text-muted">Next: {{ \Carbon\Carbon::parse($expense->recurring_next_date)->format('d M, Y') }}</small></div>
                                            @endif
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <span class="fw-bold text-danger fs-6">₹{{ number_format($expense->amount, 2) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex align-items-center justify-content-end gap-2">
                                            <a href="{{ route('business.expenses.edit', $expense->id) }}" class="btn btn-sm btn-icon btn-light-primary" title="Edit">
                                                <i class="feather-edit"></i>
                                            </a>
                                            <form action="{{ route('business.expenses.destroy', $expense->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this expense?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-icon btn-light-danger" title="Delete">
                                                    <i class="feather-trash-2"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="feather-inbox d-block mb-2" style="font-size:2rem;"></i>
                                        No expenses found. <a href="{{ route('business.expenses.create') }}">Add your first expense</a>.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($expenses->hasPages())
                <div class="card-footer d-flex justify-content-end">
                    {{ $expenses->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
