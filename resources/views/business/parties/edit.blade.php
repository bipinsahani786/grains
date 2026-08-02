@extends('layouts.app')

@section('content')
<div class="nxl-content">
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Edit Party</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('business.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('business.parties.index') }}">Parties</a></li>
                <li class="breadcrumb-item">Edit Party</li>
            </ul>
        </div>
    </div>
    
    <div class="main-content">
        <div class="row">
            <div class="col-lg-8 col-md-10 col-12 mx-auto">
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

                        <form action="{{ route('business.parties.update', $party->id) }}" method="POST" id="partyForm">
                            @csrf
                            @method('PUT')

                            @php
                                $entityType = $party->gst_no ? 'Company' : 'Individual';
                            @endphp

                            <h5 class="mb-4">Party Entity Type</h5>
                            <div class="mb-4">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input entity-type-radio" type="radio" name="entity_type" id="typeCompany" value="Company" {{ old('entity_type', $entityType) == 'Company' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="typeCompany">Company (Business)</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input entity-type-radio" type="radio" name="entity_type" id="typeIndividual" value="Individual" {{ old('entity_type', $entityType) == 'Individual' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="typeIndividual">Individual (Person)</label>
                                </div>
                            </div>

                            <h5 class="mb-4">Basic Details</h5>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', $party->name) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Party Type <span class="text-danger">*</span></label>
                                    <select name="party_type_id" id="party_type_select" class="form-select" required>
                                        <option value="">Select Party Type</option>
                                        @foreach($partyTypes as $pt)
                                            <option value="{{ $pt->id }}" {{ old('party_type_id', $party->party_type_id) == $pt->id ? 'selected' : '' }}>{{ $pt->name }}</option>
                                        @endforeach
                                        <option value="other" {{ old('party_type_id') == 'other' ? 'selected' : '' }}>Other (Add New)</option>
                                    </select>
                                    <input type="text" id="party_type_manual" class="form-control mt-2" placeholder="Type new party type (e.g. Retailer)" style="display:none;">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Phone Number</label>
                                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $party->phone) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Aadhar No</label>
                                    <input type="text" name="aadhar_no" class="form-control" value="{{ old('aadhar_no', $party->aadhar_no) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">GST No</label>
                                    <input type="text" name="gst_no" class="form-control" value="{{ old('gst_no', $party->gst_no) }}">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label class="form-label">Address</label>
                                    <textarea name="address" class="form-control" rows="2">{{ old('address', $party->address) }}</textarea>
                                </div>
                            </div>

                            <h5 class="mb-4 mt-5">Login Access (Optional)</h5>
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="login_enabled" id="login_enabled" {{ old('login_enabled', $party->login_enabled) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="login_enabled">Enable Login Portal Access</label>
                                </div>
                            </div>

                            <div id="loginFieldsWrapper" style="display: none;">
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label class="form-label">Email Address</label>
                                        <input type="email" name="email" class="form-control" value="{{ old('email', $party->email) }}">
                                        <small class="text-muted">Required if login is enabled.</small>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Password</label>
                                        <input type="password" name="password" class="form-control" minlength="8">
                                        <small class="text-muted">Leave blank to keep current</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Confirm Password</label>
                                        <input type="password" name="password_confirmation" class="form-control" minlength="8">
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="{{ route('business.parties.index') }}" class="btn btn-light">Cancel</a>
                                <button type="submit" class="btn btn-primary">Update Party</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const typeRadios = document.querySelectorAll('.entity-type-radio');
        const idProofLabel = document.getElementById('idProofLabel');
        const idProofInput = document.getElementById('idProofInput');

        const partyTypeSelect = document.getElementById('party_type_select');
        const partyTypeManual = document.getElementById('party_type_manual');

        const loginEnabledCheckbox = document.getElementById('login_enabled');
        const loginFieldsWrapper = document.getElementById('loginFieldsWrapper');

        // Entity type changes don't toggle fields anymore, we take both Aadhar and GST.

        // Party Type Select
        function updatePartyType() {
            if (partyTypeSelect.value === 'other') {
                partyTypeManual.style.display = 'block';
                partyTypeManual.setAttribute('name', 'party_type_id');
                partyTypeSelect.removeAttribute('name');
                partyTypeManual.setAttribute('required', 'required');
            } else {
                partyTypeManual.style.display = 'none';
                partyTypeSelect.setAttribute('name', 'party_type_id');
                partyTypeManual.removeAttribute('name');
                partyTypeManual.removeAttribute('required');
            }
        }
        partyTypeSelect.addEventListener('change', updatePartyType);
        
        // Initial setup
        updatePartyType();

        // Login Enabled Change
        function updateLoginFields() {
            if (loginEnabledCheckbox.checked) {
                loginFieldsWrapper.style.display = 'block';
            } else {
                loginFieldsWrapper.style.display = 'none';
            }
        }
        loginEnabledCheckbox.addEventListener('change', updateLoginFields);
        updateLoginFields();
    });
</script>
@endpush
