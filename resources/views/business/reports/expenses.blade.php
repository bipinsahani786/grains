@extends('layouts.app')

@section('content')
<div class="nxl-content">
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Expense Report</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('business.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Reports</li>
                <li class="breadcrumb-item">Expenses</li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        {{-- Filters --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small text-muted mb-1">From Date</label>
                        <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted mb-1">To Date</label>
                        <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date', now()->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100">Generate Report</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm text-center p-4">
                    <div class="text-muted small mb-1">Total Expenses</div>
                    <div class="fs-3 fw-bold text-danger">₹{{ number_format($totalExpenses, 2) }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm text-center p-4">
                    <div class="text-muted small mb-1">No. of Transactions</div>
                    <div class="fs-3 fw-bold text-dark">{{ $expenseCount }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm text-center p-4">
                    <div class="text-muted small mb-1">Avg per Transaction</div>
                    <div class="fs-3 fw-bold text-primary">₹{{ $expenseCount > 0 ? number_format($totalExpenses / $expenseCount, 2) : '0.00' }}</div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Category Breakdown --}}
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-bold">By Category</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Category</th>
                                    <th class="text-center">Count</th>
                                    <th class="text-end">Amount</th>
                                    <th class="text-end">%</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categoryBreakdown as $row)
                                    <tr>
                                        <td>
                                            @if($row->category)
                                                <span class="badge rounded-pill" style="background-color: {{ $row->category->color }}20; color: {{ $row->category->color }}; border: 1px solid {{ $row->category->color }}40;">
                                                    {{ $row->category->name }}
                                                </span>
                                            @else
                                                <span class="text-muted">Unknown</span>
                                            @endif
                                        </td>
                                        <td class="text-center text-muted">{{ $row->count }}</td>
                                        <td class="text-end fw-bold">₹{{ number_format($row->total, 2) }}</td>
                                        <td class="text-end text-muted">
                                            {{ $totalExpenses > 0 ? number_format(($row->total / $totalExpenses) * 100, 1) : 0 }}%
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center py-4 text-muted">No data</td></tr>
                                @endforelse
                            </tbody>
                            @if($totalExpenses > 0)
                                <tfoot>
                                    <tr>
                                        <th colspan="2" class="text-end">Total:</th>
                                        <th class="text-end text-danger">₹{{ number_format($totalExpenses, 2) }}</th>
                                        <th class="text-end">100%</th>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>

            {{-- Monthly Trend --}}
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-bold">Payment Mode Breakdown</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Mode</th>
                                    <th class="text-center">Count</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($modeBreakdown as $row)
                                    <tr>
                                        <td>
                                            @php
                                                $modeColors = ['cash'=>'success','bank'=>'primary','upi'=>'info','cheque'=>'warning'];
                                                $mc = $modeColors[$row->payment_mode] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-soft-{{ $mc }} text-{{ $mc }} text-uppercase">{{ $row->payment_mode }}</span>
                                        </td>
                                        <td class="text-center text-muted">{{ $row->count }}</td>
                                        <td class="text-end fw-bold">₹{{ number_format($row->total, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Expense List in Range --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-bold">All Expenses in Period</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Date</th>
                                    <th>No.</th>
                                    <th>Category</th>
                                    <th>Description</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($expenses as $exp)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($exp->date)->format('d M') }}</td>
                                        <td class="text-primary fw-bold">{{ $exp->expense_no }}</td>
                                        <td>
                                            @if($exp->category)
                                                <span class="badge rounded-pill" style="background-color: {{ $exp->category->color }}20; color: {{ $exp->category->color }};">{{ $exp->category->name }}</span>
                                            @endif
                                        </td>
                                        <td class="text-muted small">{{ $exp->description ?? '—' }}</td>
                                        <td class="text-end fw-bold text-danger">₹{{ number_format($exp->amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center py-3 text-muted">No expenses in this period</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
