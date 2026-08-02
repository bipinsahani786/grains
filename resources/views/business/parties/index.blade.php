@extends('layouts.app')

@section('content')
<div class="nxl-content">
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Parties</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('business.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Masters</li>
                <li class="breadcrumb-item">Parties (Farmers/Traders)</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="page-header-right-items">
                <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                    <!-- Trigger Modal -->
                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addPartyModal" class="btn btn-primary">
                        <i class="feather-plus me-2"></i>
                        <span>Add Party</span>
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
                            <div class="alert alert-success m-3">
                                {{ session('success') }}
                            </div>
                        @endif
                        
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Phone</th>
                                        <th>ID Proof</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($parties as $item)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="avatar-text avatar-sm">
                                                        {{ strtoupper(substr($item->name, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $item->name }}</h6>
                                                        <span class="fs-12 text-muted">{{ $item->email }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="badge bg-soft-primary text-primary">{{ $item->partyType->name ?? 'N/A' }}</span></td>
                                            <td>{{ $item->phone ?? '-' }}</td>
                                            <td>
                                                @if($item->gst_no || $item->aadhar_no)
                                                    @if($item->aadhar_no)
                                                        <div><small class="text-muted">Aadhar:</small> {{ $item->aadhar_no }}</div>
                                                    @endif
                                                    @if($item->gst_no)
                                                        <div><small class="text-muted">GST:</small> {{ $item->gst_no }}</div>
                                                    @endif
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex justify-content-end gap-2">
                                                    <a href="{{ route('business.parties.show', $item->id) }}" class="btn btn-sm btn-icon btn-light-info" title="View Profile"><i class="feather-eye"></i></a>
                                                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#editPartyModal{{ $item->id }}" class="btn btn-sm btn-icon btn-light-primary" title="Edit"><i class="feather-edit"></i></a>
                                                    <form action="{{ route('business.parties.destroy', $item->id) }}" method="POST" class="d-inline-block m-0 p-0" onsubmit="return confirm('Are you sure you want to delete this party?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-icon btn-light-danger" title="Delete"><i class="feather-trash-2"></i></button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5">
                                                <div class="d-flex flex-column align-items-center justify-content-center my-4">
                                                    <!-- Beautiful SVG Illustration (Users) -->
                                                    <div class="mb-4 text-muted opacity-50" style="background: #f8f9fa; padding: 2rem; border-radius: 50%;">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                                            <circle cx="9" cy="7" r="4"></circle>
                                                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                                        </svg>
                                                    </div>
                                                    <h5 class="text-muted fw-bold mb-2">No Parties Found</h5>
                                                    <p class="text-muted mb-4 fs-14" style="max-width: 300px;">It looks like you haven't added any farmers, traders, or customers yet.</p>
                                                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addPartyModal" class="btn btn-primary">
                                                        <i class="feather-plus me-2"></i> Add Your First Party
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
<!-- Add Party Modal -->
<div class="modal fade" id="addPartyModal" tabindex="-1" aria-labelledby="addPartyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPartyModalLabel">Add New Party</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('business.parties.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Entity Type <span class="text-danger">*</span></label>
                            <select name="entity_type" id="entity_type" class="form-select" required>
                                <option value="Individual">Individual (e.g. Farmer)</option>
                                <option value="Company">Company (e.g. Trader, Mill)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Party Type <span class="text-danger">*</span></label>
                            <select name="party_type_id" class="form-select" required>
                                <option value="">Select Type...</option>
                                @foreach($partyTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required placeholder="Full Name or Business Name">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" placeholder="10-digit Mobile No.">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="Optional">
                        </div>
                        
                        <div class="col-md-12">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control" rows="2" placeholder="Full Address"></textarea>
                        </div>
                        
                        <div class="col-md-6" id="aadhar_wrapper">
                            <label class="form-label">Aadhar Number</label>
                            <input type="text" name="aadhar_no" class="form-control" placeholder="12-digit Aadhar">
                        </div>
                        
                        <div class="col-md-6" id="gst_wrapper">
                            <label class="form-label">GST Number</label>
                            <input type="text" name="gst_no" class="form-control" placeholder="15-digit GSTIN">
                        </div>
                        
                        <div class="col-12 mt-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="login_enabled" id="login_enabled" value="1">
                                <label class="form-check-label" for="login_enabled">Enable Login for this Party (Customer Portal)</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Party</button>
                </div>
            </form>
        </div>
</div>
    </div>
</div>

<!-- Edit Party Modals -->
@foreach($parties as $item)
<div class="modal fade" id="editPartyModal{{ $item->id }}" tabindex="-1" aria-labelledby="editPartyModalLabel{{ $item->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPartyModalLabel{{ $item->id }}">Edit Party</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('business.parties.update', $item->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        @php
                            $isCompany = !empty($item->gst_no);
                            $entityType = $isCompany ? 'Company' : 'Individual';
                        @endphp
                        <div class="col-md-6">
                            <label class="form-label">Entity Type <span class="text-danger">*</span></label>
                            <select name="entity_type" id="edit_entity_type_{{ $item->id }}" class="form-select edit-entity-type" data-id="{{ $item->id }}" required>
                                <option value="Individual" {{ $entityType === 'Individual' ? 'selected' : '' }}>Individual (e.g. Farmer)</option>
                                <option value="Company" {{ $entityType === 'Company' ? 'selected' : '' }}>Company (e.g. Trader, Mill)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Party Type <span class="text-danger">*</span></label>
                            <select name="party_type_id" class="form-select" required>
                                <option value="">Select Type...</option>
                                @foreach($partyTypes as $type)
                                    <option value="{{ $type->id }}" {{ $item->party_type_id == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ $item->name }}" required placeholder="Full Name or Business Name">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" value="{{ $item->phone }}" placeholder="10-digit Mobile No.">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ $item->email }}" placeholder="Optional">
                        </div>
                        
                        <div class="col-md-12">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control" rows="2" placeholder="Full Address">{{ $item->address }}</textarea>
                        </div>
                        
                        <div class="col-md-6" id="edit_aadhar_wrapper_{{ $item->id }}">
                            <label class="form-label">Aadhar Number</label>
                            <input type="text" name="aadhar_no" class="form-control" value="{{ $item->aadhar_no }}" placeholder="12-digit Aadhar">
                        </div>
                        
                        <div class="col-md-6" id="edit_gst_wrapper_{{ $item->id }}">
                            <label class="form-label">GST Number</label>
                            <input type="text" name="gst_no" class="form-control" value="{{ $item->gst_no }}" placeholder="15-digit GSTIN">
                        </div>
                        
                        <div class="col-12 mt-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="login_enabled" id="edit_login_enabled_{{ $item->id }}" value="1" {{ $item->login_enabled ? 'checked' : '' }}>
                                <label class="form-check-label" for="edit_login_enabled_{{ $item->id }}">Enable Login for this Party (Customer Portal)</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Party</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Entity type toggling is removed so Aadhar and GST fields remain visible at all times.
    });
</script>
@endpush
@endsection
