@extends('layouts.app')

@section('content')
<div class="nxl-content">
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Edit Company: {{ $company->name }}</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('superadmin.companies.index') }}">Companies</a></li>
                <li class="breadcrumb-item">Edit</li>
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
                        <form action="{{ route('superadmin.companies.update', $company->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">Business Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $company->name) }}" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address (Used for Admin Login) <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $company->email) }}" required>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="password" class="form-label">Admin Login Password (Leave blank to keep current)</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $company->phone) }}">
                                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="gstin" class="form-label">GSTIN</label>
                                    <input type="text" class="form-control @error('gstin') is-invalid @enderror" id="gstin" name="gstin" value="{{ old('gstin', $company->gstin) }}">
                                    @error('gstin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="type" class="form-label">Business Type</label>
                                    <select class="form-control @error('type') is-invalid @enderror" id="type" name="type">
                                        <option value="trader" {{ old('type', $company->type) == 'trader' ? 'selected' : '' }}>Trader</option>
                                        <option value="broker" {{ old('type', $company->type) == 'broker' ? 'selected' : '' }}>Broker</option>
                                        <option value="commission_agent" {{ old('type', $company->type) == 'commission_agent' ? 'selected' : '' }}>Commission Agent</option>
                                    </select>
                                    @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">Complete Address</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address', $company->address) }}</textarea>
                                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <hr class="my-4">
                            <h5 class="mb-3">Update Plan</h5>

                            <div class="mb-3">
                                <label for="plan_id" class="form-label">Select Subscription Plan</label>
                                <select class="form-control @error('plan_id') is-invalid @enderror" id="plan_id" name="plan_id">
                                    <option value="">-- No Plan (Trial/Restricted) --</option>
                                    @foreach($plans as $plan)
                                        <option value="{{ $plan->id }}" {{ old('plan_id', optional($subscription)->plan_id) == $plan->id ? 'selected' : '' }}>
                                            {{ $plan->name }} (₹{{ $plan->price_monthly }}/mo)
                                        </option>
                                    @endforeach
                                </select>
                                @error('plan_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3 form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $company->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active Account</label>
                            </div>

                            <div class="d-flex align-items-center gap-3 mt-4">
                                <button type="submit" class="btn btn-primary px-4">Update Company</button>
                                <a href="{{ route('superadmin.companies.index') }}" class="btn btn-light px-4">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
