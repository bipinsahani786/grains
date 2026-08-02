@extends('layouts.app')

@section('content')
<div class="nxl-content">
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Inventory Logs</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('business.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Inventory</li>
                <li class="breadcrumb-item">Inventory Logs</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <form action="{{ route('business.inventory.logs.index') }}" method="GET" class="d-flex align-items-center gap-2 flex-wrap">
                <div class="input-group input-group-sm" style="width: auto;">
                    <span class="input-group-text bg-light"><i class="feather-search"></i></span>
                    <input type="text" class="form-control form-control-sm" name="search" placeholder="Search grain, godown, or type..." value="{{ request('search') }}">
                </div>
                <div class="input-group input-group-sm" style="width: auto;">
                    <span class="input-group-text bg-light"><i class="feather-calendar"></i></span>
                    <input type="date" class="form-control form-control-sm" name="start_date" value="{{ request('start_date') }}">
                    <span class="input-group-text bg-light px-2 border-start-0 border-end-0">to</span>
                    <input type="date" class="form-control form-control-sm" name="end_date" value="{{ request('end_date') }}">
                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                    @if(request('start_date') || request('end_date') || request('search'))
                        <a href="{{ route('business.inventory.logs.index') }}" class="btn btn-light btn-sm text-danger border"><i class="feather-x"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>
    
    <div class="main-content">
        <div class="row">
            <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                <div class="card stretch stretch-full">
                    <div class="card-body p-3 hover-effect">
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <div class="d-flex gap-3 align-items-center w-100 overflow-hidden">
                                <div class="avatar-text bg-soft-primary text-primary">
                                    <i class="feather-activity"></i>
                                </div>
                                <div class="overflow-hidden w-100">
                                    <div class="fs-5 fw-bold text-dark text-truncate">{{ $totalTransactions }}</div>
                                    <h3 class="fs-12 fw-semibold text-truncate-1-line mb-0">Total Transactions</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                <div class="card stretch stretch-full">
                    <div class="card-body p-3 hover-effect">
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <div class="d-flex gap-3 align-items-center w-100 overflow-hidden">
                                <div class="avatar-text bg-soft-success text-success">
                                    <i class="feather-arrow-down-left"></i>
                                </div>
                                <div class="overflow-hidden w-100">
                                    <div class="fs-5 fw-bold text-dark text-truncate">{{ number_format($totalIn, 2) }}</div>
                                    <h3 class="fs-12 fw-semibold text-truncate-1-line mb-0">Total Stock In</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                <div class="card stretch stretch-full">
                    <div class="card-body p-3 hover-effect">
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <div class="d-flex gap-3 align-items-center w-100 overflow-hidden">
                                <div class="avatar-text bg-soft-danger text-danger">
                                    <i class="feather-arrow-up-right"></i>
                                </div>
                                <div class="overflow-hidden w-100">
                                    <div class="fs-5 fw-bold text-dark text-truncate">{{ number_format($totalOut, 2) }}</div>
                                    <h3 class="fs-12 fw-semibold text-truncate-1-line mb-0">Total Stock Out</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Date & Time</th>
                                        <th>Grain</th>
                                        <th>Godown</th>
                                        <th>Lot No</th>
                                        <th>Type</th>
                                        <th>Qty Changed</th>
                                        <th>Running Balance</th>
                                        <th>User</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($logs as $log)
                                        <tr>
                                            <td>{{ $log->created_at->format('d M Y, h:i A') }}</td>
                                            <td class="fw-bold">{{ $log->grain->name ?? '-' }}</td>
                                            <td>{{ $log->godown->name ?? '-' }}</td>
                                            <td><span class="badge bg-soft-secondary text-dark">{{ $log->lot->lot_no ?? '-' }}</span></td>
                                            <td>
                                                @if($log->transaction_type == 'purchase')
                                                    <span class="badge bg-soft-success text-success">Purchase (In)</span>
                                                @elseif($log->transaction_type == 'sale')
                                                    <span class="badge bg-soft-danger text-danger">Sale (Out)</span>
                                                @elseif($log->transaction_type == 'adjustment')
                                                    <span class="badge bg-soft-warning text-warning">Adjustment</span>
                                                @else
                                                    <span class="badge bg-soft-secondary text-secondary">{{ ucfirst($log->transaction_type) }}</span>
                                                @endif
                                            </td>
                                            <td class="{{ $log->quantity_changed > 0 ? 'text-success' : 'text-danger' }} fw-bold">
                                                {{ $log->quantity_changed > 0 ? '+' : '' }}{{ number_format($log->quantity_changed, 2) }}
                                            </td>
                                            <td class="fw-bold text-primary">{{ number_format($log->balance_after, 2) }}</td>
                                            <td>{{ $log->user->name ?? 'System' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-5 text-muted">No inventory logs found yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
