@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="content-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="content-title">Categories</h1>
                <p class="content-subtitle">Manage your categories</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Category
                </a>
            </div>
        </div>
    </div>

    <div class="content-body">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="card-title mb-0">All Categories</h5>
                    </div>
                    <div class="col-auto">
                        <form method="GET" class="d-flex">
                            <input type="text" name="search" class="form-control" placeholder="Search categories..." value="{{ $search }}">
                            <button type="submit" class="btn btn-outline-primary ms-2">
                                <i class="fas fa-search"></i>
                            </button>
                            @if($search)
                                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary ms-2">
                                    <i class="fas fa-times"></i>
                                </a>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($categories->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Slug</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categories as $category)
                                    <tr>
                                        <td>
                                            <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                        </td>
                                        <td>{{ $category->name }}</td>
                                        <td>{{ $category->slug }}</td>
                                        <td>
                                            <form method="POST" action="{{ route('admin.categories.updateStatus', $category) }}" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                                    <option value="1" {{ $category->status === '1' ? 'selected' : '' }}>Active</option>
                                                    <option value="0" {{ $category->status === '0' ? 'selected' : '' }}>Inactive</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td>{{ $category->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.categories.show', $category) }}" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this category?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{ $categories->links() }}
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Categories Found</h5>
                        @if($search)
                            <p class="text-muted">No categories found matching "{{ $search }}"</p>
                            <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-primary">View All Categories</a>
                        @else
                            <p class="text-muted">Start by creating your first category</p>
                            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">Add Category</a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
