@extends('layouts.app')

@section('content')
<div class="nxl-content">
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Brokers</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('business.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Masters</li>
                <li class="breadcrumb-item">Brokers</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="page-header-right-items">
                <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCommissionModal">
                        <i class="feather-plus me-2"></i>
                        <span>Fix New Commission</span>
                    </button>
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
                            <div class="alert alert-success m-3">
                                {{ session('success') }}
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger m-3">
                                {{ session('error') }}
                            </div>
                        @endif
                        @if ($errors->any())
                            <div class="alert alert-danger m-3">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Broker Name</th>
                                        <th>Applies To</th>
                                        <th>Rate</th>
                                        <th>Commission Type</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($commissions as $comm)
                                        <tr>
                                            <td>
                                                <a href="{{ route('business.financials.brokers.profile', $comm->broker->id) }}" class="fw-bold text-primary">
                                                    {{ $comm->broker->name ?? 'N/A' }}
                                                </a>
                                            </td>
                                            <td><span class="badge bg-soft-secondary text-secondary text-capitalize">{{ $comm->applies_to }}</span></td>
                                            <td class="fw-bold text-dark">
                                                @if($comm->commission_type == 'percentage')
                                                    {{ $comm->rate }}%
                                                @else
                                                    ₹{{ number_format($comm->rate, 2) }}
                                                @endif
                                            </td>
                                            <td><span class="text-capitalize">{{ str_replace('_', ' ', $comm->commission_type) }}</span></td>
                                            <td class="text-end">
                                                <div class="d-flex align-items-center justify-content-end gap-2">
                                                    <a href="{{ route('business.financials.brokers.profile', $comm->broker->id) }}" class="btn btn-sm btn-icon btn-light-info" title="View Ledger & Profile">
                                                        <i class="feather-eye"></i>
                                                    </a>
                                                    <button class="btn btn-sm btn-icon btn-light-primary" data-bs-toggle="modal" data-bs-target="#editCommissionModal{{ $comm->id }}">
                                                        <i class="feather-edit"></i>
                                                    </button>
                                                    <form action="{{ route('business.financials.commissions.destroy', $comm->id) }}" method="POST" class="m-0 p-0" onsubmit="return confirm('Are you sure you want to delete this commission rule?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-icon btn-light-danger">
                                                            <i class="feather-trash-2"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        
                                        <!-- Edit Modal -->
                                        @push('modals')
                                        <div class="modal fade" id="editCommissionModal{{ $comm->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit Commission for {{ $comm->broker->name ?? 'N/A' }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form action="{{ route('business.financials.commissions.update', $comm->id) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label class="form-label">Commission Type <span class="text-danger">*</span></label>
                                                                <select class="form-select" name="commission_type" required>
                                                                    <option value="per_quintal" {{ $comm->commission_type == 'per_quintal' ? 'selected' : '' }}>Per Quintal</option>
                                                                    <option value="per_kg" {{ $comm->commission_type == 'per_kg' ? 'selected' : '' }}>Per Kg</option>
                                                                    <option value="percentage" {{ $comm->commission_type == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                                                    <option value="fixed" {{ $comm->commission_type == 'fixed' ? 'selected' : '' }}>Fixed Amount per deal</option>
                                                                </select>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Rate / Value <span class="text-danger">*</span></label>
                                                                <input type="number" step="0.01" class="form-control" name="rate" value="{{ $comm->rate }}" required>
                                                                <small class="text-muted">Enter Rs amount or %</small>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Applies To <span class="text-danger">*</span></label>
                                                                <select class="form-select" name="applies_to" required>
                                                                    <option value="purchase" {{ $comm->applies_to == 'purchase' ? 'selected' : '' }}>Purchases Only</option>
                                                                    <option value="sale" {{ $comm->applies_to == 'sale' ? 'selected' : '' }}>Sales Only</option>
                                                                    <option value="both" {{ $comm->applies_to == 'both' ? 'selected' : '' }}>Both</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-primary">Update</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        @endpush
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">No broker commissions fixed yet</td>
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

<!-- Add Modal -->
@push('modals')
<div class="modal fade" id="addCommissionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Fix New Broker Commission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('business.financials.commissions.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Broker Name <span class="text-danger">*</span></label>
                        <input type="text" list="brokersList" class="form-control" name="broker_name" required placeholder="Type new or select existing">
                        <datalist id="brokersList">
                            @foreach($brokers as $broker)
                                <option value="{{ $broker->name }}">
                            @endforeach
                        </datalist>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Commission Type <span class="text-danger">*</span></label>
                        <select class="form-select" name="commission_type" required>
                            <option value="">Select Type</option>
                            <option value="per_quintal">Per Quintal</option>
                            <option value="per_kg">Per Kg</option>
                            <option value="percentage">Percentage (%)</option>
                            <option value="fixed">Fixed Amount per deal</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rate / Value <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control" name="rate" required placeholder="e.g. 15">
                        <small class="text-muted">Enter Rs amount (e.g. 15 for 15 Rs/Quintal) or % (e.g. 2 for 2%)</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Applies To <span class="text-danger">*</span></label>
                        <select class="form-select" name="applies_to" required>
                            <option value="both">Both</option>
                            <option value="purchase">Purchases Only</option>
                            <option value="sale">Sales Only</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Rule</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endpush
@endsection
