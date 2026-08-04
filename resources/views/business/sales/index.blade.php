@extends('layouts.app')

@section('content')
<div class="nxl-content">
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Sales List</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('business.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Sales List</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="page-header-right-items">
                <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                    <a href="{{ route('business.sales.create') }}" class="btn btn-primary">
                        <i class="feather-plus me-2"></i>
                        <span>New Sale</span>
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
                                        <th>Sale No</th>
                                        <th>Date & Time</th>
                                        <th>Customer</th>
                                        <th>Grain</th>
                                        <th>Quantity (@unitLabel)</th>
                                        <th>Rate</th>
                                        <th>Total Amount</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($sales as $item)
                                        <tr data-bs-toggle="collapse" data-bs-target="#details-{{ $item->id }}" style="cursor: pointer;">
                                            <td>
                                                <div class="fw-bold text-success">{{ $item->sale_no ?? 'N/A' }}</div>
                                            </td>
                                            <td>
                                                <div class="fw-bold">{{ \Carbon\Carbon::parse($item->date)->format('d M, Y') }}</div>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($item->sale_time)->format('h:i A') }}</small>
                                            </td>
                                            <td>
                                                <h6 class="mb-0">{{ $item->party->name ?? 'N/A' }}</h6>
                                            </td>
                                            <td><span class="badge bg-soft-primary text-primary">{{ $item->grain->name ?? 'N/A' }}</span></td>
                                            <td>@qtyRaw($item->quantity) @unitLabel</td>
                                            <td>₹@rateRaw($item->rate) / @unitLabel</td>
                                            <td>
                                                <span class="text-success fw-bold d-block">₹{{ number_format($item->total_amount, 2) }}</span>
                                                @if($item->payment_mode === 'regular' && $item->remaining_outstanding > 0)
                                                    <span class="badge bg-soft-danger text-danger mt-1" style="font-size:0.7rem;">
                                                        Pending: ₹{{ number_format($item->remaining_outstanding, 2) }}
                                                    </span>
                                                @elseif($item->payment_mode === 'regular' && $item->remaining_outstanding <= 0)
                                                    <span class="badge bg-soft-success text-success mt-1" style="font-size:0.7rem;">Fully Paid</span>
                                                @endif
                                            </td>
                                            <td class="text-end" onclick="event.stopPropagation();">
                                                <div class="d-flex align-items-center justify-content-end gap-2">
                                                    <button class="btn btn-sm btn-icon btn-light-primary" data-bs-toggle="collapse" data-bs-target="#details-{{ $item->id }}">
                                                        <i class="feather-chevron-down"></i>
                                                    </button>
                                                    <a href="{{ route('business.sales.print', $item->id) }}" class="btn btn-sm btn-icon btn-light-info" target="_blank" title="Print Bill">
                                                        <i class="feather-printer"></i>
                                                    </a>
                                                    <a href="{{ route('business.sales.edit', $item->id) }}" class="btn btn-sm btn-icon btn-light-secondary" title="Edit">
                                                        <i class="feather-edit"></i>
                                                    </a>
                                                    <form action="{{ route('business.sales.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this sale? This will revert all stock and ledgers.');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-icon btn-light-danger" title="Cancel Sale">
                                                            <i class="feather-trash-2"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="8" class="p-0 border-0">
                                                <div class="collapse" id="details-{{ $item->id }}">
                                                    <div class="p-4 bg-light">
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <small class="text-muted d-block mb-1">Lot Allocated</small>
                                                                @if($item->saleLotAllocations->isNotEmpty())
                                                                    <div class="fw-bold text-dark">{{ $item->saleLotAllocations->first()->lot->lot_no ?? 'N/A' }}</div>
                                                                @else
                                                                    <div class="fw-bold text-dark">N/A</div>
                                                                @endif
                                                            </div>
                                                            <div class="col-md-3">
                                                                <small class="text-muted d-block mb-1">Profit</small>
                                                                @php
                                                                    $profit = 0;
                                                                    if($item->saleLotAllocations->isNotEmpty()) {
                                                                        $allocation = $item->saleLotAllocations->first();
                                                                        $profit = ($item->rate - $allocation->cost_rate) * $allocation->quantity_taken;
                                                                    }
                                                                @endphp
                                                                <div class="fw-bold {{ $profit >= 0 ? 'text-success' : 'text-danger' }}">
                                                                    ₹{{ number_format($profit, 2) }}
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <small class="text-muted d-block mb-1">Ledger Update</small>
                                                                <div class="fw-bold text-danger"><i class="feather-check-circle me-1"></i> Debited</div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <small class="text-muted d-block mb-1">Stock Update</small>
                                                                <div class="fw-bold text-danger"><i class="feather-check-circle me-1"></i> Decreased</div>
                                                            </div>
                                                        </div>
                                                        @if($item->notes)
                                                        <div class="row mt-3">
                                                            <div class="col-12">
                                                                <small class="text-muted d-block mb-1">Notes</small>
                                                                <div>{{ $item->notes }}</div>
                                                            </div>
                                                        </div>
                                                        @endif

                                                        {{-- Recovery Collections Area --}}
                                                        @if($item->payment_mode === 'regular')
                                                        <hr class="my-3">
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <h6 class="mb-0 text-primary"><i class="feather-dollar-sign me-1"></i> Payment Collections</h6>
                                                            @if($item->remaining_outstanding > 0)
                                                            <button class="btn btn-sm btn-primary py-1 px-2" onclick="openCollectModal({{ $item->id }}, {{ $item->remaining_outstanding }})">
                                                                <i class="feather-plus me-1"></i> Collect Payment
                                                            </button>
                                                            @endif
                                                        </div>
                                                        @if($item->collections->count() > 0)
                                                            <table class="table table-sm table-bordered mt-2" style="font-size:0.8rem;">
                                                                <thead class="bg-light">
                                                                    <tr>
                                                                        <th>Date</th>
                                                                        <th>Amount</th>
                                                                        <th>Mode</th>
                                                                        <th>Notes</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($item->collections as $col)
                                                                    <tr>
                                                                        <td>{{ $col->collected_at->format('d M Y') }}</td>
                                                                        <td class="fw-bold text-success">₹{{ number_format($col->amount, 2) }}</td>
                                                                        <td>{{ $col->mode }} <small class="text-muted">{{ $col->reference_no }}</small></td>
                                                                        <td>{{ $col->notes }}</td>
                                                                    </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        @else
                                                            <div class="text-muted small">No payments collected yet.</div>
                                                        @endif
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4 text-muted">No sales found</td>
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

{{-- Collect Payment Modal --}}
<div class="modal fade" id="collectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Record Sale Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="collectForm">
                    <input type="hidden" id="sale_id" name="sale_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Amount Received (₹) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control" id="collect_amount" name="amount" required>
                        <small class="text-muted">Max pending: ₹<span id="max_collect_display">0.00</span></small>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="collected_at" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Payment Mode <span class="text-danger">*</span></label>
                            <select class="form-select" name="mode" required>
                                <option value="Cash">Cash</option>
                                <option value="Cheque">Cheque</option>
                                <option value="Online">Online / Transfer</option>
                                <option value="NEFT">NEFT / RTGS</option>
                                <option value="UPI">UPI</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Reference No</label>
                        <input type="text" class="form-control" name="reference_no" placeholder="Cheque number, UTR, etc.">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" name="notes" rows="2"></textarea>
                    </div>

                    <div class="text-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="btnCollectSubmit">Save Collection</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let maxCollect = 0;

    function openCollectModal(saleId, pendingAmt) {
        maxCollect = pendingAmt;
        document.getElementById('sale_id').value = saleId;
        document.getElementById('collect_amount').value = pendingAmt;
        document.getElementById('collect_amount').max = pendingAmt;
        document.getElementById('max_collect_display').innerText = pendingAmt.toFixed(2);
        
        var modalEl = document.getElementById('collectModal');
        if (modalEl.parentNode !== document.body) {
            document.body.appendChild(modalEl);
        }
        var myModal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        myModal.show();
    }

    document.getElementById('collectForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        let saleId = document.getElementById('sale_id').value;
        let amt = parseFloat(document.getElementById('collect_amount').value);
        
        if(amt > maxCollect) {
            toastr.error("Cannot collect more than outstanding amount!");
            return;
        }

        let formData = new FormData(this);
        let btn = document.getElementById('btnCollectSubmit');
        let originalText = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
        btn.disabled = true;

        fetch(`{{ url('business/sales') }}/${saleId}/collect`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                toastr.success(data.message);
                setTimeout(() => window.location.reload(), 800);
            } else {
                toastr.error(data.message || 'Error saving collection.');
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        })
        .catch(err => {
            console.error(err);
            toastr.error("Server error. Please try again.");
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    });
</script>
@endpush
