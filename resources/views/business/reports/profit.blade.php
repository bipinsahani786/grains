@extends('layouts.app')

@section('content')
<div class="nxl-content">
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Profit Report</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('business.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Reports</li>
                <li class="breadcrumb-item">Profit Report</li>
            </ul>
        </div>
    </div>
    
    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" action="{{ route('business.reports.profit') }}" class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">Start Date</label>
                                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">End Date</label>
                                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">Filter</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Sale ID</th>
                                        <th>Grain & Qty</th>
                                        <th class="text-end">Sale Revenue</th>
                                        <th class="text-end">COGS (Purchase Cost)</th>
                                        <th class="text-end">Gross Profit</th>
                                        <th class="text-end">Broker Comm.</th>
                                        <th class="text-end">Net Profit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalRevenue = 0;
                                        $totalCogs = 0;
                                        $totalGrossProfit = 0;
                                        $totalComm = 0;
                                        $totalNetProfit = 0;
                                    @endphp
                                    @forelse($profitData as $data)
                                        @php
                                            $totalRevenue += $data->revenue;
                                            $totalCogs += $data->cogs;
                                            $totalGrossProfit += $data->gross_profit;
                                            $totalComm += $data->broker_commission;
                                            $totalNetProfit += $data->net_profit;
                                        @endphp
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($data->sale->date)->format('d M, Y') }}</td>
                                            <td>SALE-#{{ str_pad($data->sale->id, 4, '0', STR_PAD_LEFT) }}</td>
                                            <td>
                                                {{ $data->sale->grain->name ?? 'N/A' }} <br>
                                                <small class="text-muted">@qtyRaw($data->sale->quantity) @unitLabel</small>
                                            </td>
                                            <td class="text-end fw-bold">₹{{ number_format($data->revenue, 2) }}</td>
                                            <td class="text-end text-danger">₹{{ number_format($data->cogs, 2) }}</td>
                                            <td class="text-end fw-bold {{ $data->gross_profit >= 0 ? 'text-success' : 'text-danger' }}">
                                                ₹{{ number_format($data->gross_profit, 2) }}
                                            </td>
                                            <td class="text-end text-warning">₹{{ number_format($data->broker_commission, 2) }}</td>
                                            <td class="text-end fw-bold {{ $data->net_profit >= 0 ? 'text-success' : 'text-danger' }} fs-14">
                                                ₹{{ number_format($data->net_profit, 2) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">No sales found for the selected criteria</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr class="bg-light">
                                        <th colspan="3" class="text-end">Total:</th>
                                        <th class="text-end fs-6">₹{{ number_format($totalRevenue, 2) }}</th>
                                        <th class="text-end text-danger fs-6">₹{{ number_format($totalCogs, 2) }}</th>
                                        <th class="text-end text-success fs-6">₹{{ number_format($totalGrossProfit, 2) }}</th>
                                        <th class="text-end text-warning fs-6">₹{{ number_format($totalComm, 2) }}</th>
                                        <th class="text-end text-success fs-5">₹{{ number_format($totalNetProfit, 2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Expense Deduction Box --}}
@php
    $totalNetProfitFromSales = collect($profitData)->sum('net_profit');
    $finalNetProfit = $totalNetProfitFromSales - ($totalExpenses ?? 0);
@endphp

@if(isset($totalExpenses) && $totalExpenses > 0)
<div class="nxl-content pt-0">
    <div class="main-content pt-0">
        <div class="row">
            <div class="col-lg-6 offset-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-bold">Profit Summary (After Expenses)</h6>
                    </div>
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            <tr>
                                <td class="text-muted">Net Profit from Sales</td>
                                <td class="text-end fw-bold text-success">₹{{ number_format($totalNetProfitFromSales, 2) }}</td>
                            </tr>
                            @foreach($expensesByCategory ?? [] as $expRow)
                                <tr>
                                    <td class="text-muted ps-4">
                                        <small>— {{ $expRow->category->name ?? 'Other' }}</small>
                                    </td>
                                    <td class="text-end text-danger small">- ₹{{ number_format($expRow->total, 2) }}</td>
                                </tr>
                            @endforeach
                            <tr class="table-light">
                                <th>Total Expenses</th>
                                <th class="text-end text-danger">- ₹{{ number_format($totalExpenses, 2) }}</th>
                            </tr>
                            <tr class="table-{{ $finalNetProfit >= 0 ? 'success' : 'danger' }} bg-opacity-10">
                                <th class="fs-6">Final Net Profit</th>
                                <th class="text-end fs-5 {{ $finalNetProfit >= 0 ? 'text-success' : 'text-danger' }}">
                                    ₹{{ number_format($finalNetProfit, 2) }}
                                </th>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@endsection
