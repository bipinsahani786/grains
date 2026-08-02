@extends('layouts.app')

@section('content')
<div class="nxl-content">
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Platform Users</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Platform Users</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="page-header-right-items">
                <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                    <a href="{{ route('superadmin.users.create') }}" class="btn btn-primary">
                        <i class="feather-plus me-2"></i>
                        <span>Create New</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="main-content">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="feather-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="feather-alert-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($data as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->email }}</td>
                                        <td>
                                            @if($item->role === 'super_admin')
                                                <span class="badge bg-soft-danger text-danger">Platform Super Admin</span>
                                            @else
                                                <span class="badge bg-soft-primary text-primary">Admin</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->login_enabled)
                                                <span class="badge bg-success">Login Enabled</span>
                                            @else
                                                <span class="badge bg-secondary">Login Disabled</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                @if($item->role !== 'super_admin')
                                                    <a href="{{ route('superadmin.impersonate', $item->id) }}" class="btn btn-sm btn-icon btn-light-success" title="Login As this Admin">
                                                        <i class="feather-eye"></i>
                                                    </a>
                                                @endif
                                                <a href="{{ route('superadmin.users.edit', $item->id) }}" class="btn btn-sm btn-icon btn-light-primary"><i class="feather-edit"></i></a>
                                                
                                                @if(auth()->id() !== $item->id)
                                                    <form action="{{ route('superadmin.users.destroy', $item->id) }}" method="POST" class="d-inline-block m-0 p-0" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-icon btn-light-danger"><i class="feather-trash-2"></i></button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No records found.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $data->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection