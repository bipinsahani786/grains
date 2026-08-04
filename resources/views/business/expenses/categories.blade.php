@extends('layouts.app')

@section('content')
<div class="nxl-content">
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Expense Categories</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('business.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('business.expenses.index') }}">Expenses</a></li>
                <li class="breadcrumb-item">Categories</li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show"><i class="feather-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show"><i class="feather-alert-circle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        <div class="row">
            {{-- Add New Category --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-bold">Add New Category</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('business.expenses.categories.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. Water Charges" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Color</label>
                                <div class="d-flex gap-2 align-items-center">
                                    <input type="color" name="color" class="form-control form-control-color" value="#6c757d" style="width:50px;height:38px;">
                                    <span class="text-muted small">Pick a badge color</span>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="feather-plus me-1"></i>Add Category
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Category List --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-bold">All Categories ({{ $categories->count() }})</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Category</th>
                                    <th class="text-center">Expenses</th>
                                    <th>Type</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $cat)
                                    <tr>
                                        <td>
                                            <span class="badge rounded-pill me-2" style="background-color: {{ $cat->color }}20; color: {{ $cat->color }}; border: 1px solid {{ $cat->color }}40; padding: 6px 12px;">
                                                <i class="{{ $cat->icon }} me-1"></i>{{ $cat->name }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-soft-secondary text-secondary">{{ $cat->expenses_count }}</span>
                                        </td>
                                        <td>
                                            @if($cat->is_system)
                                                <span class="badge bg-soft-info text-info">System Default</span>
                                            @else
                                                <span class="badge bg-soft-success text-success">Custom</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if(!$cat->is_system)
                                                <button class="btn btn-sm btn-icon btn-light-primary" onclick="editCategory({{ $cat->id }}, '{{ addslashes($cat->name) }}', '{{ $cat->color }}')" title="Edit">
                                                    <i class="feather-edit"></i>
                                                </button>
                                                <form action="{{ route('business.expenses.categories.destroy', $cat->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this category?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-icon btn-light-danger" title="Delete">
                                                        <i class="feather-trash-2"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center py-4 text-muted">No categories found</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Edit Category Modal --}}
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-bold">Edit Category</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCategoryForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" id="editCatName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Color</label>
                        <input type="color" name="color" id="editCatColor" class="form-control form-control-color" style="width:100%;height:38px;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editCategory(id, name, color) {
    document.getElementById('editCatName').value = name;
    document.getElementById('editCatColor').value = color;
    document.getElementById('editCategoryForm').action = '/business/expenses/categories/' + id;
    new bootstrap.Modal(document.getElementById('editCategoryModal')).show();
}
</script>
@endsection
