@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="content-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="content-title">Sub Categories</h1>
                <p class="content-subtitle">Manage your sub categories</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.subcategories.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Sub Category
                </a>
            </div>
        </div>
    </div>

    <div class="content-body">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="card-title mb-0">All Sub Categories</h5>
                    </div>
                    <div class="col-auto">
                        <form method="GET" class="d-flex gap-2">
                            <select name="category_id" class="form-select" onchange="this.form.submit()">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ $categoryId == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="text" name="search" class="form-control" placeholder="Search subcategories..." value="{{ $search }}">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fas fa-search"></i>
                            </button>
                            @if($search || $categoryId)
                                <a href="{{ route('admin.subcategories.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i>
                                </a>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($subCategories->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Slug</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($subCategories as $subCategory)
                                    <tr>
                                        <td>
                                            <img src="{{ $subCategory->image_url }}" alt="{{ $subCategory->name }}" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                        </td>
                                        <td>{{ $subCategory->name }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $subCategory->category->name }}</span>
                                        </td>
                                        <td>{{ $subCategory->slug }}</td>
                                        <td>
                                            <form method="POST" action="{{ route('admin.subcategories.updateStatus', $subCategory) }}" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                                    <option value="1" {{ $subCategory->status === '1' ? 'selected' : '' }}>Active</option>
                                                    <option value="0" {{ $subCategory->status === '0' ? 'selected' : '' }}>Inactive</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td>{{ $subCategory->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.subcategories.show', $subCategory) }}" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.subcategories.edit', $subCategory) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="POST" action="{{ route('admin.subcategories.destroy', $subCategory) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this subcategory?')">
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

                    {{ $subCategories->links() }}
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Sub Categories Found</h5>
                        @if($search)
                            <p class="text-muted">No subcategories found matching "{{ $search }}"</p>
                            <a href="{{ route('admin.subcategories.index') }}" class="btn btn-outline-primary">View All Sub Categories</a>
                        @elseif($categoryId)
                            <p class="text-muted">No subcategories found in selected category</p>
                            <a href="{{ route('admin.subcategories.index') }}" class="btn btn-outline-primary">View All Sub Categories</a>
                        @else
                            <p class="text-muted">Start by creating your first subcategory</p>
                            <a href="{{ route('admin.subcategories.create') }}" class="btn btn-primary">Add Sub Category</a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
