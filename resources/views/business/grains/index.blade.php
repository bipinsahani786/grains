@extends('layouts.app')

@section('content')
<div class="nxl-content">
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Grains Master</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('business.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Masters</li>
                <li class="breadcrumb-item">Grains</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="page-header-right-items">
                <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addGrainModal">
                        <i class="feather-plus me-2"></i>
                        <span>Add Grain</span>
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
                                        <th>Grain Name</th>
                                        <th>Unit</th>
                                        <th>Opening Stock</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($grains as $item)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="avatar-text avatar-sm bg-soft-primary text-primary">
                                                        {{ strtoupper(substr($item->name, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $item->name }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="badge bg-soft-success text-success">{{ $item->unit }}</span></td>
                                            <td>{{ $item->opening_stock }} {{ $item->unit }}</td>
                                            <td class="text-end">
                                                <div class="d-flex justify-content-end gap-2">
                                                    <button type="button" class="btn btn-sm btn-icon btn-light-primary" data-bs-toggle="modal" data-bs-target="#editGrainModal{{ $item->id }}">
                                                        <i class="feather-edit"></i>
                                                    </button>
                                                    <form action="{{ route('business.grains.destroy', $item->id) }}" method="POST" class="d-inline-block m-0 p-0" onsubmit="return confirm('Are you sure you want to delete this grain?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-icon btn-light-danger"><i class="feather-trash-2"></i></button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">No grains found. Add your first grain!</td>
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

@push('modals')
<!-- Edit Modals -->
@foreach($grains as $item)
<div class="modal fade" id="editGrainModal{{ $item->id }}" tabindex="-1" aria-labelledby="editGrainModalLabel{{ $item->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('business.grains.update', $item->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editGrainModalLabel{{ $item->id }}">Edit Grain</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-start">
                    <div class="mb-3">
                        <label class="form-label">Grain Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ $item->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Measurement Unit <span class="text-danger">*</span></label>
                        <select name="unit" class="form-select" required>
                            <option value="QTL" {{ $item->unit == 'QTL' ? 'selected' : '' }}>Quintal (QTL)</option>
                            <option value="KG" {{ $item->unit == 'KG' ? 'selected' : '' }}>Kilogram (KG)</option>
                            <option value="TON" {{ $item->unit == 'TON' ? 'selected' : '' }}>Tonne (TON)</option>
                            <option value="BAG" {{ $item->unit == 'BAG' ? 'selected' : '' }}>Bag/Sack (BAG)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Opening Stock</label>
                        <input type="number" step="0.01" name="opening_stock" class="form-control" value="{{ $item->opening_stock }}" placeholder="0.00">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- Add Modal -->
<div class="modal fade" id="addGrainModal" tabindex="-1" aria-labelledby="addGrainModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('business.grains.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addGrainModalLabel">Add Grain</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-start">
                    <div class="mb-3">
                        <label class="form-label">Grain Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g. Wheat, Rice, Mustard" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Measurement Unit <span class="text-danger">*</span></label>
                        <select name="unit" class="form-select" required>
                            <option value="QTL" {{ old('unit') == 'QTL' ? 'selected' : '' }}>Quintal (QTL)</option>
                            <option value="KG" {{ old('unit') == 'KG' ? 'selected' : '' }}>Kilogram (KG)</option>
                            <option value="TON" {{ old('unit') == 'TON' ? 'selected' : '' }}>Tonne (TON)</option>
                            <option value="BAG" {{ old('unit') == 'BAG' ? 'selected' : '' }}>Bag/Sack (BAG)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Opening Stock</label>
                        <input type="number" step="0.01" name="opening_stock" class="form-control" value="{{ old('opening_stock', 0) }}" placeholder="0.00">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Grain</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endpush
