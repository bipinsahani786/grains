@extends('layouts.app')

@section('content')
<div class="nxl-content">
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Godowns (Warehouses)</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('business.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Masters</li>
                <li class="breadcrumb-item">Godowns</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="page-header-right-items">
                <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addGodownModal" class="btn btn-primary">
                        <i class="feather-plus me-2"></i>
                        <span>Add Godown</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body p-0">
                        @if (session('success'))
                            <div class="alert alert-success m-3">{{ session('success') }}</div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger m-3">{{ session('error') }}</div>
                        @endif
                        
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Godown Name</th>
                                        <th>Location</th>
                                        <th>Capacity (Qtl)</th>
                                        <th>Filled (Qtl)</th>
                                        <th>Available (Qtl)</th>
                                        <th>Utilization</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($godowns as $item)
                                        @php
                                            $percent = $item->capacity_in_quintals > 0 ? ($item->current_stock_in_quintals / $item->capacity_in_quintals) * 100 : 0;
                                            $color = $percent > 90 ? 'danger' : ($percent > 75 ? 'warning' : 'success');
                                            $available = $item->capacity_in_quintals - $item->current_stock_in_quintals;
                                        @endphp
                                        <tr>
                                            <td class="fw-bold text-dark">
                                                <div class="d-flex align-items-center">
                                                    <i class="feather-home text-primary me-2"></i>
                                                    {{ $item->name }}
                                                </div>
                                            </td>
                                            <td>{{ $item->location ?? '-' }}</td>
                                            <td>{{ number_format($item->capacity_in_quintals, 2) }}</td>
                                            <td>{{ number_format($item->current_stock_in_quintals, 2) }}</td>
                                            <td class="text-{{ $available < 0 ? 'danger' : 'success' }} fw-bold">{{ number_format($available, 2) }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="progress flex-grow-1" style="height: 6px;">
                                                        <div class="progress-bar bg-{{ $color }}" role="progressbar" style="width: {{ min(100, $percent) }}%"></div>
                                                    </div>
                                                    <span class="ms-2 fs-12">{{ number_format($percent, 1) }}%</span>
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex justify-content-end gap-2">
                                                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#editGodownModal{{ $item->id }}" class="btn btn-sm btn-icon btn-light-primary"><i class="feather-edit"></i></a>
                                                    <form action="{{ route('business.godowns.destroy', $item->id) }}" method="POST" class="d-inline-block m-0 p-0" onsubmit="return confirm('Are you sure you want to delete this Godown?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-icon btn-light-danger"><i class="feather-trash-2"></i></button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-5">
                                                <div class="d-flex flex-column align-items-center justify-content-center my-4">
                                                    <div class="mb-4 text-muted opacity-50" style="background: #f8f9fa; padding: 2rem; border-radius: 50%;">
                                                        <i class="feather-home" style="font-size: 3rem;"></i>
                                                    </div>
                                                    <h5 class="text-muted fw-bold mb-2">No Godowns Added</h5>
                                                    <p class="text-muted mb-4 fs-14" style="max-width: 300px;">Start by setting up your warehouses and storage spaces.</p>
                                                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addGodownModal" class="btn btn-primary">
                                                        <i class="feather-plus me-2"></i> Add First Godown
                                                    </a>
                                                </div>
                                            </td>
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

@push('modals')
<!-- Add Modal -->
<div class="modal fade" id="addGodownModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Godown</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('business.godowns.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g. Godown A, Main Warehouse">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Location</label>
                        <input type="text" name="location" class="form-control" placeholder="e.g. Sector 5, Industrial Area">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Total Capacity (Quintals) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="capacity_in_quintals" class="form-control" required placeholder="e.g. 5000">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modals -->
@foreach($godowns as $item)
<div class="modal fade" id="editGodownModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Godown</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('business.godowns.update', $item->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ $item->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Location</label>
                        <input type="text" name="location" class="form-control" value="{{ $item->location }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Total Capacity (Quintals) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="capacity_in_quintals" class="form-control" value="{{ $item->capacity_in_quintals }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endpush
@endsection
