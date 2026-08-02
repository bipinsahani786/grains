@extends('layouts.app')

@section('content')
<div class="nxl-content">
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Stock Adjustments</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('business.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Inventory</li>
                <li class="breadcrumb-item">Adjustments</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="page-header-right-items">
                <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newAdjustmentModal">
                        <i class="feather-plus me-2"></i>
                        <span>New Adjustment</span>
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
                                        <th>Date</th>
                                        <th>Lot No</th>
                                        <th>Grain</th>
                                        <th>Reason</th>
                                        <th>Adjustment</th>
                                        <th>Fin. Impact</th>
                                        <th>Adjusted By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($adjustments as $adj)
                                        @php
                                            $diff = $adj->quantity_after - $adj->quantity_before;
                                            $isPositive = $diff > 0;
                                            $impact = abs($diff) * ($adj->lot->rate ?? 0);
                                        @endphp
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($adj->date)->format('d M, Y') }}</td>
                                            <td><span class="fw-bold">{{ $adj->lot->lot_no ?? 'N/A' }}</span></td>
                                            <td>{{ $adj->grain->name ?? 'N/A' }}</td>
                                            <td><span class="text-capitalize">{{ str_replace('_', ' ', $adj->reason) }}</span></td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="text-muted text-decoration-line-through">{{ $adj->quantity_before }}</span>
                                                    <i class="feather-arrow-right text-muted" style="font-size: 12px;"></i>
                                                    <span class="fw-bold">{{ $adj->quantity_after }}</span>
                                                    <span class="badge {{ $isPositive ? 'bg-soft-success text-success' : 'bg-soft-danger text-danger' }} ms-2">
                                                        {{ $isPositive ? '+' : '' }}{{ $diff }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                @if($diff == 0)
                                                    <span class="text-muted">-</span>
                                                @else
                                                    <span class="{{ $isPositive ? 'text-success' : 'text-danger' }} fw-bold">
                                                        {{ $isPositive ? '+' : '-' }}₹{{ number_format($impact, 2) }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>{{ $adj->user->name ?? 'System' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4 text-muted">No stock adjustments found</td>
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

<!-- New Adjustment Modal -->
<div class="modal fade" id="newAdjustmentModal" tabindex="-1" aria-labelledby="newAdjustmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newAdjustmentModalLabel">New Stock Adjustment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('business.inventory.adjustments.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="date">Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required>
                        </div>
                        
                        <div class="col-md-8 mb-3">
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
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Current Quantity</label>
                            <input type="text" class="form-control bg-light text-muted" id="current_qty" readonly value="0">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="quantity_after">New Quantity <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control" id="quantity_after" name="quantity_after" value="{{ old('quantity_after') }}" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
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
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Adjustment</button>
                </div>
            </form>
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
        
        @if($errors->any() || session('error'))
            var myModal = new bootstrap.Modal(document.getElementById('newAdjustmentModal'));
            myModal.show();
        @endif
    });
</script>
