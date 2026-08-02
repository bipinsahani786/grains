@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex flex-column align-items-start">
            <div class="page-header-title">
                <h5 class="m-b-10 text-uppercase fw-bolder">MY PROFILE</h5>
            </div>
            <span class="text-muted text-uppercase mt-1" style="font-size: 11px; letter-spacing: 0.5px;">MANAGE YOUR PERSONAL DETAILS AND SECURITY SETTINGS.</span>
        </div>
        <div class="page-header-right ms-auto">
            <ul class="breadcrumb text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">
                <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}" class="text-muted text-decoration-none">HOME</a></li>
                <li class="breadcrumb-item text-primary fw-semibold">ACCOUNT SETTINGS</li>
            </ul>
        </div>
    </div>
    <!-- [ page-header ] end -->

    <!-- [ Main Content ] start -->
    <div class="main-content">
        
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row g-4">
                <!-- Left Card: Personal Information -->
                <div class="col-xl-7 col-lg-7">
                    <div class="card stretch stretch-full h-100">
                        <div class="card-body p-4 p-xl-5">
                            
                            <!-- Header -->
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5">
                                <div class="d-flex align-items-center mb-2 mb-md-0">
                                    <i class="feather-user text-primary me-2" style="font-size: 20px;"></i>
                                    <span class="fw-bolder text-uppercase text-dark" style="letter-spacing: 1px;">PERSONAL INFORMATION</span>
                                </div>
                                <span class="text-muted text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">UPDATE YOUR PRIMARY CONTACT AND IDENTIFICATION DETAILS</span>
                            </div>

                            <!-- Profile Photo Section -->
                            <div class="mb-5">
                                <label class="form-label text-uppercase text-muted fw-bold mb-3" style="font-size: 12px; letter-spacing: 1px;">PROFILE PHOTO</label>
                                <div class="d-flex align-items-center">
                                    <div class="position-relative me-4">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center border-primary" style="width: 100px; height: 100px; border: 2px dashed; padding: 4px;">
                                            @if(auth()->user()->avatar)
                                                <img src="{{ asset('storage/' . auth()->user()->avatar) }}" class="rounded-circle w-100 h-100 object-fit-cover" alt="Avatar">
                                            @else
                                                <div class="bg-light rounded-circle w-100 h-100 d-flex flex-column align-items-center justify-content-center text-dark">
                                                    <h3 class="mb-0 fw-bolder">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</h3>
                                                    <span class="fw-bolder" style="font-size: 10px;">{{ explode(' ', auth()->user()->name)[0] }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        <label class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 28px; height: 28px; cursor: pointer; transform: translate(10%, 10%);">
                                            <i class="feather-camera" style="font-size: 12px;"></i>
                                            <input type="file" name="avatar" class="d-none" accept="image/*">
                                        </label>
                                    </div>
                                    <div>
                                        <h6 class="fw-bolder text-uppercase text-dark mb-1" style="letter-spacing: 0.5px;">YOUR AVATAR</h6>
                                        <span class="text-muted text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">CLEAR PHOTOS WITH 1:1 ASPECT RATIO WORK BEST.</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Full Name -->
                            <div class="mb-4">
                                <label class="form-label text-uppercase text-muted fw-bold" style="font-size: 12px; letter-spacing: 1px;">FULL NAME</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0 px-3 text-muted" style="border-radius: 8px 0 0 8px;"><i class="feather-user"></i></span>
                                    <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" class="form-control bg-light border-0 shadow-none text-uppercase fw-semibold" style="border-radius: 0 8px 8px 0; padding-left: 0;" required>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Email -->
                                <div class="col-md-6 mb-4">
                                    <label class="form-label text-uppercase text-muted fw-bold" style="font-size: 12px; letter-spacing: 1px;">EMAIL ADDRESS</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0 px-3 text-muted" style="border-radius: 8px 0 0 8px;"><i class="feather-mail"></i></span>
                                        <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" class="form-control bg-light border-0 shadow-none text-uppercase fw-semibold" style="border-radius: 0 8px 8px 0; padding-left: 0;" required>
                                    </div>
                                </div>
                                <!-- Mobile -->
                                <div class="col-md-6 mb-4">
                                    <label class="form-label text-uppercase text-muted fw-bold" style="font-size: 12px; letter-spacing: 1px;">MOBILE NUMBER</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0 px-3 text-muted" style="border-radius: 8px 0 0 8px;"><i class="feather-phone-call"></i></span>
                                        <input type="text" name="phone" value="{{ old('phone', auth()->user()->phone) }}" class="form-control bg-light border-0 shadow-none fw-semibold" style="border-radius: 0 8px 8px 0; padding-left: 0;">
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary text-uppercase fw-bolder px-4 py-2" style="border-radius: 20px; font-size: 12px; letter-spacing: 1px;">
                                    <i class="feather-save me-2"></i> SAVE CHANGES
                                </button>
                            </div>
                            
                        </div>
                    </div>
                </div>

                <!-- Right Card: Security -->
                <div class="col-xl-5 col-lg-5">
                    <div class="card stretch stretch-full h-100">
                        <div class="card-body p-4 p-xl-5">
                            
                            <!-- Header -->
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5">
                                <div class="d-flex align-items-center mb-2 mb-md-0">
                                    <i class="feather-lock text-primary me-2" style="font-size: 20px;"></i>
                                    <span class="fw-bolder text-uppercase text-dark" style="letter-spacing: 1px;">SECURITY</span>
                                </div>
                                <span class="text-muted text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">SECURE YOUR ACCOUNT</span>
                            </div>

                            <!-- New Password -->
                            <div class="mb-4 mt-2">
                                <label class="form-label text-uppercase text-muted fw-bold" style="font-size: 12px; letter-spacing: 1px;">NEW PASSWORD</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0 px-3 text-muted" style="border-radius: 8px 0 0 8px;"><i class="feather-key"></i></span>
                                    <input type="password" name="password" minlength="8" class="form-control bg-light border-0 shadow-none text-uppercase fw-semibold text-muted" placeholder="MINIMUM 8 CHARACTERS" style="border-radius: 0 8px 8px 0; padding-left: 0; font-size: 11px; letter-spacing: 1px;">
                                </div>
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-5">
                                <label class="form-label text-uppercase text-muted fw-bold" style="font-size: 12px; letter-spacing: 1px;">CONFIRM PASSWORD</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0 px-3 text-muted" style="border-radius: 8px 0 0 8px;"><i class="feather-shield"></i></span>
                                    <input type="password" name="password_confirmation" minlength="8" class="form-control bg-light border-0 shadow-none text-uppercase fw-semibold text-muted" placeholder="REPEAT NEW PASSWORD" style="border-radius: 0 8px 8px 0; padding-left: 0; font-size: 11px; letter-spacing: 1px;">
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary text-uppercase fw-bolder px-4 py-2" style="border-radius: 20px; font-size: 12px; letter-spacing: 1px;">
                                    <i class="feather-refresh-cw me-2"></i> UPDATE PASSWORD
                                </button>
                            </div>
                            
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>
    <!-- [ Main Content ] end -->
</div>
@endsection
