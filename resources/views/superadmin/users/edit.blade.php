@extends('layouts.app')

@section('content')
<div class="nxl-content">
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Edit User</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('superadmin.users.index') }}">Platform Users</a></li>
                <li class="breadcrumb-item">Edit User</li>
            </ul>
        </div>
    </div>
    
    <div class="main-content">
        <div class="row">
            <div class="col-12">
                <div class="card stretch stretch-full">
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

                        <form action="{{ route('superadmin.users.update', $user->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <h5 class="mb-4">User Details</h5>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone Number</label>
                                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Password <small class="text-muted fs-12">(Leave blank to keep current)</small></label>
                                    <input type="password" name="password" class="form-control" minlength="8">
                                </div>
                            </div>
                            <div class="row mb-3 mt-4">
                                <div class="col-md-6">
                                    <label class="form-label">Role</label>
                                    <input type="text" class="form-control" value="Admin" readonly>
                                </div>
                            </div>

                            <div class="row mb-3 mt-4">
                                <div class="col-md-6">
                                    <label class="form-label">Address</label>
                                    <textarea name="address" class="form-control" rows="2">{{ old('address', $user->address) }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">ID Proof (Aadhar No)</label>
                                    <input type="text" name="aadhar_no" class="form-control" value="{{ old('aadhar_no', $user->aadhar_no) }}">
                                </div>
                            </div>

                            <div class="row mb-4 mt-4">
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="login_enabled" id="login_enabled" {{ old('login_enabled', $user->login_enabled) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="login_enabled">Enable Login</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">Is Active Account</label>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="{{ route('superadmin.users.index') }}" class="btn btn-light">Cancel</a>
                                <button type="submit" class="btn btn-primary">Update User</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
