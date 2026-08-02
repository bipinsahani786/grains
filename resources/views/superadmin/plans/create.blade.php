@extends('layouts.app')

@section('content')
<div class="nxl-content">
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Create Subscription Plan</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('superadmin.plans.index') }}">Plans</a></li>
                <li class="breadcrumb-item">Create</li>
            </ul>
        </div>
    </div>
    
    <div class="main-content">

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card stretch stretch-full">
                    <div class="card-body">
                        <form action="{{ route('superadmin.plans.store') }}" method="POST">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">Plan Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="e.g., Basic, Professional, Enterprise" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="price_monthly" class="form-label">Monthly Price (₹) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control @error('price_monthly') is-invalid @enderror" id="price_monthly" name="price_monthly" value="{{ old('price_monthly') }}" placeholder="0.00" required>
                                    @error('price_monthly') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="price_yearly" class="form-label">Yearly Price (₹) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control @error('price_yearly') is-invalid @enderror" id="price_yearly" name="price_yearly" value="{{ old('price_yearly') }}" placeholder="0.00" required>
                                    @error('price_yearly') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <h6 class="mt-4 mb-3">Limits & Quotas <small class="text-muted fw-normal">(-1 for unlimited)</small></h6>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="max_staff_users" class="form-label">Max Staff Users <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('max_staff_users') is-invalid @enderror" id="max_staff_users" name="max_staff_users" value="{{ old('max_staff_users') }}" placeholder="e.g., 5 or -1" required>
                                    @error('max_staff_users') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="max_parties" class="form-label">Max Parties/Clients <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('max_parties') is-invalid @enderror" id="max_parties" name="max_parties" value="{{ old('max_parties') }}" placeholder="e.g., 100 or -1" required>
                                    @error('max_parties') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="max_transactions_month" class="form-label">Transactions / Month <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('max_transactions_month') is-invalid @enderror" id="max_transactions_month" name="max_transactions_month" value="{{ old('max_transactions_month') }}" placeholder="e.g., 500 or -1" required>
                                    @error('max_transactions_month') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <h6 class="mt-4 mb-3">Plan Features</h6>
                            <p class="text-muted fs-12 mb-3">Select the features included in this plan:</p>
                            @php
                                $allFeatures = [
                                    'Invoicing',
                                    'Inventory Management',
                                    'SMS Alerts',
                                    'Party Management',
                                    'Purchase & Sales',
                                    'Reports & Analytics',
                                    'Ledger & Accounting',
                                    'Broker Commissions',
                                    'Multi-User Access',
                                    'Stock Adjustments',
                                    'Data Export (Excel/PDF)',
                                    'WhatsApp Notifications',
                                ];
                                $oldFeatures = old('features', []);
                            @endphp
                            <div class="row">
                                @foreach($allFeatures as $feature)
                                <div class="col-md-4 col-sm-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="features[]" value="{{ $feature }}" id="feature_{{ Str::slug($feature) }}"
                                            {{ in_array($feature, $oldFeatures) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="feature_{{ Str::slug($feature) }}">{{ $feature }}</label>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <hr class="my-4">

                            <div class="mb-3 form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                                <label class="form-check-label" for="is_active">Active Plan</label>
                            </div>

                            <div class="d-flex align-items-center gap-3 mt-4">
                                <button type="submit" class="btn btn-primary px-4">Create Plan</button>
                                <a href="{{ route('superadmin.plans.index') }}" class="btn btn-light px-4">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
