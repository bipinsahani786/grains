@extends('layouts.app')

@section('content')
<div class="nxl-content">
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Tenants (Companies)</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Tenants (Companies)</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="page-header-right-items">
                <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                    <a href="{{ route('superadmin.companies.create') }}" class="btn btn-primary">
                        <i class="feather-plus me-2"></i>
                        <span>Add Company</span>
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
                                        <th>Company Name</th>
                                        <th>Email</th>
                                        <th>Type</th>
                                        <th>Plan</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($data as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>
                                            <span class="fw-bold">{{ $item->name }}</span>
                                            @if($item->gstin)
                                                <br><small class="text-muted">GSTIN: {{ $item->gstin }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $item->email }}</td>
                                        <td>
                                            <span class="badge bg-soft-primary text-primary text-capitalize">{{ str_replace('_', ' ', $item->type ?? 'N/A') }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $activeSub = $item->subscriptions->first();
                                            @endphp
                                            @if($activeSub && $activeSub->plan)
                                                <span class="badge bg-soft-success text-success">{{ $activeSub->plan->name }}</span>
                                            @else
                                                <span class="badge bg-soft-warning text-warning">No Plan</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->is_active)
                                                <span class="badge bg-soft-success text-success">Active</span>
                                            @else
                                                <span class="badge bg-soft-danger text-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td><small class="text-muted">{{ $item->created_at->format('d M, Y') }}</small></td>
                                        <td>
                                            <div class="d-flex align-items-center justify-content-end gap-2">
                                                <a href="{{ route('superadmin.companies.edit', $item->id) }}" class="btn btn-sm btn-icon btn-light-primary" title="Edit">
                                                    <i class="feather-edit"></i>
                                                </a>
                                                <form action="{{ route('superadmin.companies.destroy', $item->id) }}" method="POST" class="d-inline-block m-0 p-0" onsubmit="return confirm('Are you sure you want to delete this company?');">
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
                                            No companies found. Add your first company!
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