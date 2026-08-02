@extends('layouts.app')

@section('content')
<div class="nxl-content">
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Subscription Plans</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Subscription Plans</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="page-header-right-items">
                <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                    <a href="{{ route('superadmin.plans.create') }}" class="btn btn-primary">
                        <i class="feather-plus me-2"></i>
                        <span>Create New</span>
                    </a>
                </div>
            </div>
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
                                        <th>Plan Name</th>
                                        <th>Monthly Price</th>
                                        <th>Yearly Price</th>
                                        <th>Limits</th>
                                        <th>Status</th>
                                        <th>Subscribers</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($data as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>
                                            <span class="fw-bold">{{ $item->name }}</span>
                                        </td>
                                        <td>₹{{ number_format($item->price_monthly, 2) }}</td>
                                        <td>₹{{ number_format($item->price_yearly, 2) }}</td>
                                        <td>
                                            <small class="text-muted">
                                                <i class="feather-users me-1"></i>{{ $item->max_staff_users == -1 ? '∞' : $item->max_staff_users }} Staff
                                                · <i class="feather-briefcase me-1"></i>{{ $item->max_parties == -1 ? '∞' : $item->max_parties }} Parties
                                            </small>
                                        </td>
                                        <td>
                                            @if($item->is_active)
                                                <span class="badge bg-soft-success text-success">Active</span>
                                            @else
                                                <span class="badge bg-soft-danger text-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-soft-primary text-primary">{{ $item->subscriptions_count ?? 0 }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center justify-content-end gap-2">
                                                <a href="{{ route('superadmin.plans.edit', $item->id) }}" class="btn btn-sm btn-icon btn-light-primary" title="Edit">
                                                    <i class="feather-edit"></i>
                                                </a>
                                                <form action="{{ route('superadmin.plans.destroy', $item->id) }}" method="POST" class="d-inline-block m-0 p-0" onsubmit="return confirm('Are you sure you want to delete this plan?');">
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
                                            No plans found. Create your first plan!
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