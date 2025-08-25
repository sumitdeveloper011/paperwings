@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="content-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="content-title">Brands</h1>
                <p class="content-subtitle">Manage your brands</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.brands.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Brand
                </a>
            </div>
        </div>
    </div>

    <div class="content-body">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="card-title mb-0">All Brands</h5>
                    </div>
                    <div class="col-auto">
                        <form method="GET" class="d-flex">
                            <input type="text" name="search" class="form-control" placeholder="Search brands..." value="{{ $search }}">
                            <button type="submit" class="btn btn-outline-primary ms-2">
                                <i class="fas fa-search"></i>
                            </button>
                            @if($search)
                                <a href="{{ route('admin.brands.index') }}" class="btn btn-outline-secondary ms-2">
                                    <i class="fas fa-times"></i>
                                </a>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($brands->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Slug</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($brands as $brand)
                                    <tr>
                                        <td>
                                            <img src="{{ $brand->image_url }}" alt="{{ $brand->name }}" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                        </td>
                                        <td>{{ $brand->name }}</td>
                                        <td>{{ $brand->slug }}</td>
                                        <td>{{ $brand->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.brands.show', $brand) }}" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.brands.edit', $brand) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="POST" action="{{ route('admin.brands.destroy', $brand) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this brand?')">
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

                    {{ $brands->links() }}
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-award fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Brands Found</h5>
                        @if($search)
                            <p class="text-muted">No brands found matching "{{ $search }}"</p>
                            <a href="{{ route('admin.brands.index') }}" class="btn btn-outline-primary">View All Brands</a>
                        @else
                            <p class="text-muted">Start by creating your first brand</p>
                            <a href="{{ route('admin.brands.create') }}" class="btn btn-primary">Add Brand</a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
