@extends('layouts.app')

@section('content')
<div class="nxl-content">
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">New Stock Adjustment</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('business.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('business.inventory.adjustments.index') }}">Adjustments</a></li>
                <li class="breadcrumb-item">New</li>
            </ul>
        </div>
    </div>
    
    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <form action="{{ route('business.inventory.adjustments.store') }}" method="POST">
                            @csrf
                            
                            <h5 class="mb-4">Adjustment Details</h5>
                            <div class="row mb-4">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label" for="date">Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required>
                                </div>
                                
                                <div class="col-md-9 mb-3">
                                    <label class="form-label" for="lot_id">Select Lot to Adjust <span class="text-danger">*</span></label>
                                    <select class="form-select" id="lot_id" name="lot_id" required onchange="updateCurrentQty()">
                                        <option value="">Select Lot</option>
                                        @foreach($lots as $lot)
                                            <option value="{{ $lot->id }}" data-qty="{{ $lot->remaining_quantity }}" {{ old('lot_id') == $lot->id ? 'selected' : '' }}>
                                                {{ $lot->lot_no }} - {{ $lot->grain->name ?? 'Unknown' }} (Current: {{ $lot->remaining_quantity }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="col-md-3 mb-3">
                                    <label class="form-label text-muted">Current Quantity</label>
                                    <input type="text" class="form-control bg-light text-muted" id="current_qty" readonly value="0">
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label" for="quantity_after">New Quantity <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control" id="quantity_after" name="quantity_after" value="{{ old('quantity_after') }}" required>
                                    <small class="text-muted">Enter the actual count. The difference will be automatically logged.</small>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="reason">Reason <span class="text-danger">*</span></label>
                                    <select class="form-select" id="reason" name="reason" required>
                                        <option value="">Select Reason</option>
                                        <option value="damage" {{ old('reason') == 'damage' ? 'selected' : '' }}>Damaged/Spoiled</option>
                                        <option value="quality_decrement" {{ old('reason') == 'quality_decrement' ? 'selected' : '' }}>Quality Decrement (Weight loss)</option>
                                        <option value="recount" {{ old('reason') == 'recount' ? 'selected' : '' }}>Recounting Error</option>
                                        <option value="other" {{ old('reason') == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-12 mb-3">
                                    <label class="form-label" for="notes">Notes</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                                </div>
                            </div>

                            <div class="text-end">
                                <a href="{{ route('business.inventory.adjustments.index') }}" class="btn btn-light me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary">Save Adjustment</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function updateCurrentQty() {
        const lotSelect = document.getElementById('lot_id');
        const currentQtyInput = document.getElementById('current_qty');
        const selectedOption = lotSelect.options[lotSelect.selectedIndex];
        
        if (selectedOption && selectedOption.value !== '') {
            currentQtyInput.value = selectedOption.getAttribute('data-qty');
        } else {
            currentQtyInput.value = '0';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        updateCurrentQty();
    });
</script>
@endsection
