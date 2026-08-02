@extends('layouts.app')

@section('content')
<div class="nxl-content">
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Platform Settings</h5>
            </div>
        </div>
    </div>
    
    <div class="main-content">
        <div class="row">
            <div class="col-12">
                <div class="card stretch stretch-full">
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('superadmin.settings.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="brand_name" class="form-label">Brand Name</label>
                                <input type="text" class="form-control" id="brand_name" name="brand_name" value="{{ old('brand_name', $brandName) }}">
                            </div>

                            <div class="mb-3">
                                <label for="logo" class="form-label">Platform Logo</label>
                                @if($logoPath)
                                    <div class="mb-2">
                                        <img src="{{ Storage::url($logoPath) }}" alt="Current Logo" style="max-height: 80px;" class="img-fluid rounded">
                                    </div>
                                @endif
                                <input class="form-control" type="file" id="logo" name="logo" accept="image/*">
                                <div class="form-text">Recommended size: 200x50 pixels. Uploading a new logo will replace the old one.</div>
                            </div>

                            <div class="mb-4">
                                <label for="favicon" class="form-label">Platform Favicon</label>
                                @if(isset($faviconPath) && $faviconPath)
                                    <div class="mb-2">
                                        <img src="{{ Storage::url($faviconPath) }}" alt="Current Favicon" style="max-height: 32px;" class="img-fluid rounded">
                                    </div>
                                @endif
                                <input class="form-control" type="file" id="favicon" name="favicon" accept="image/*,.ico">
                                <div class="form-text">Recommended size: 32x32 or 64x64 pixels (PNG or ICO).</div>
                            </div>

                            <button type="submit" class="btn btn-primary">Save Settings</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
