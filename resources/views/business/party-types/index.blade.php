@extends('layouts.app')

@section('content')
<div class="nxl-content">
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Party Types</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('business.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Masters</li>
                <li class="breadcrumb-item">Party Types</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="page-header-right-items">
                <div class="d-flex d-md-none">
                    <a href="javascript:void(0)" class="page-header-right-close-toggle">
                        <i class="feather-arrow-left me-2"></i>
                        <span>Back</span>
                    </a>
                </div>
                <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                    <!-- Changed from link to modal trigger -->
                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addPartyTypeModal" class="btn btn-primary">
                        <i class="feather-plus me-2"></i>
                        <span>Add Party Type</span>
                    </a>
                </div>
            </div>
            <div class="d-md-none d-flex align-items-center">
                <a href="javascript:void(0)" class="page-header-right-open-toggle">
                    <i class="feather-align-right fs-20"></i>
                </a>
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
                                        <th>Name</th>
                                        <th>Slug</th>
                                        <th>Type</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($partyTypes as $item)
                                        <tr>
                                            <td>{{ $item->name }}</td>
                                            <td><span class="badge bg-soft-secondary text-secondary">{{ $item->slug }}</span></td>
                                            <td>
                                                @if(is_null($item->company_id))
                                                    <span class="badge bg-soft-info text-info">System Default</span>
                                                @else
                                                    <span class="badge bg-soft-success text-success">Custom</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex justify-content-end gap-2">
                                                    @if(is_null($item->company_id) || $item->company_id !== auth()->user()->company_id)
                                                        <span class="text-muted fs-12">No Actions (Global)</span>
                                                    @else
                                                        <!-- Modal trigger for Edit -->
                                                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#editPartyTypeModal{{ $item->id }}" class="btn btn-sm btn-icon btn-light-primary"><i class="feather-edit"></i></a>
                                                        <form action="{{ route('business.party-types.destroy', $item->id) }}" method="POST" class="d-inline-block m-0 p-0" onsubmit="return confirm('Are you sure you want to delete this custom Party Type?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-icon btn-light-danger"><i class="feather-trash-2"></i></button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        
                                        
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5">
                                                <div class="d-flex flex-column align-items-center justify-content-center my-4">
                                                    <!-- Beautiful SVG Illustration (Tag/Category) -->
                                                    <div class="mb-4 text-muted opacity-50" style="background: #f8f9fa; padding: 2rem; border-radius: 50%;">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                                                            <line x1="7" y1="7" x2="7.01" y2="7"></line>
                                                        </svg>
                                                    </div>
                                                    <h5 class="text-muted fw-bold mb-2">No Party Types Found</h5>
                                                    <p class="text-muted mb-4 fs-14" style="max-width: 300px;">Create categories like Retailer, Wholesaler, or Transporter to organize your parties.</p>
                                                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addPartyTypeModal" class="btn btn-primary">
                                                        <i class="feather-plus me-2"></i> Add Party Type
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
    <!-- Edit Modals for items -->
    @foreach($partyTypes as $item)
        @if(!is_null($item->company_id) && $item->company_id === auth()->user()->company_id)
        <div class="modal fade" id="editPartyTypeModal{{ $item->id }}" tabindex="-1" aria-labelledby="editPartyTypeModalLabel{{ $item->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editPartyTypeModalLabel{{ $item->id }}">Edit Party Type</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('business.party-types.update', $item->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body text-start">
                            <div class="mb-3">
                                <label class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ $item->name }}" required>
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
        @endif
    @endforeach

    <!-- Add Party Type Modal -->
    <div class="modal fade" id="addPartyTypeModal" tabindex="-1" aria-labelledby="addPartyTypeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPartyTypeModalLabel">Add Party Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('business.party-types.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required placeholder="e.g. Retailer, Wholesaler">
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
@endpush
@endsection
