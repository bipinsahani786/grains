@extends('layouts.app')

@section('content')
<div class="nxl-content">
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">SaaS Invoices</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">SaaS Invoices</li>
            </ul>
        </div>
    </div>
    
    <div class="main-content">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="feather-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="feather-alert-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card stretch stretch-full">
                    <div class="card-body custom-card-action p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Invoice No.</th>
                                        <th>Company</th>
                                        <th>Plan</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($data as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td><span class="fw-bold">{{ $item->invoice_number ?? 'N/A' }}</span></td>
                                        <td>{{ $item->company->name ?? 'N/A' }}</td>
                                        <td>
                                            @if($item->subscription && $item->subscription->plan)
                                                <span class="badge bg-soft-primary text-primary">{{ $item->subscription->plan->name }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>₹{{ number_format($item->amount ?? 0, 2) }}</td>
                                        <td>
                                            @if(($item->status ?? '') === 'paid')
                                                <span class="badge bg-soft-success text-success">Paid</span>
                                            @elseif(($item->status ?? '') === 'unpaid')
                                                <span class="badge bg-soft-warning text-warning">Unpaid</span>
                                            @else
                                                <span class="badge bg-soft-danger text-danger">{{ ucfirst($item->status ?? 'Unknown') }}</span>
                                            @endif
                                        </td>
                                        <td><small class="text-muted">{{ $item->created_at->format('d M, Y') }}</small></td>
                                        <td>
                                            <div class="d-flex align-items-center justify-content-end gap-2">
                                                <a href="{{ route('superadmin.invoices.edit', $item->id) }}" class="btn btn-sm btn-icon btn-light-primary" title="Edit Status">
                                                    <i class="feather-edit"></i>
                                                </a>
                                                <form action="{{ route('superadmin.invoices.destroy', $item->id) }}" method="POST" class="d-inline-block m-0 p-0" onsubmit="return confirm('Are you sure you want to delete this invoice?');">
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
                                        <td colspan="8" class="text-center py-4 text-muted">
                                            <i class="feather-inbox d-block mb-2" style="font-size: 2rem;"></i>
                                            No invoices found yet.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if($data->hasPages())
                        <div class="card-footer py-0">
                            {{ $data->links() }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection