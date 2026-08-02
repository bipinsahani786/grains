@extends('layouts.app')

@section('content')
<div class="nxl-content">
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Edit Invoice Status: {{ $invoice->invoice_number }}</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('superadmin.invoices.index') }}">Invoices</a></li>
                <li class="breadcrumb-item">Edit</li>
            </ul>
        </div>
    </div>
    
    <div class="main-content">
        <div class="row">
            <div class="col-12">
                <div class="card stretch stretch-full">
                    <div class="card-body">
                        <form action="{{ route('superadmin.invoices.update', $invoice->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-3">
                                <label class="form-label">Invoice Number</label>
                                <input type="text" class="form-control" value="{{ $invoice->invoice_number }}" disabled>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Amount</label>
                                <input type="text" class="form-control" value="₹{{ $invoice->amount }}" disabled>
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">Payment Status</label>
                                <select class="form-control @error('status') is-invalid @enderror" id="status" name="status">
                                    <option value="paid" {{ old('status', $invoice->status) == 'paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="unpaid" {{ old('status', $invoice->status) == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                    <option value="failed" {{ old('status', $invoice->status) == 'failed' ? 'selected' : '' }}>Failed</option>
                                </select>
                                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="d-flex align-items-center gap-3 mt-4">
                                <button type="submit" class="btn btn-primary px-4">Update Status</button>
                                <a href="{{ route('superadmin.invoices.index') }}" class="btn btn-light px-4">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
