@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="content-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="content-title">{{ $category->name }}</h1>
                <p class="content-subtitle">Category details</p>
            </div>
            <div class="col-auto">
                <div class="btn-group" role="group">
                    <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Categories
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Category Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if($category->image)
                                <div class="col-md-4 mb-3">
                                    <img src="{{ $category->image_url }}" alt="{{ $category->name }}" 
                                         class="img-fluid rounded shadow-sm">
                                </div>
                            @endif
                            <div class="{{ $category->image ? 'col-md-8' : 'col-md-12' }}">
                                <dl class="row">
                                    <dt class="col-sm-3">Name:</dt>
                                    <dd class="col-sm-9">{{ $category->name }}</dd>
                                    
                                    <dt class="col-sm-3">Slug:</dt>
                                    <dd class="col-sm-9"><code>{{ $category->slug }}</code></dd>
                                    
                                    <dt class="col-sm-3">Status:</dt>
                                    <dd class="col-sm-9">
                                        @if($category->status === 'active')
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </dd>
                                    
                                    <dt class="col-sm-3">UUID:</dt>
                                    <dd class="col-sm-9">
                                        <small class="text-muted font-monospace">{{ $category->uuid }}</small>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Timestamps</h5>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-5">Created:</dt>
                            <dd class="col-sm-7">
                                {{ $category->created_at->format('M d, Y') }}<br>
                                <small class="text-muted">{{ $category->created_at->format('g:i A') }}</small>
                            </dd>
                            
                            <dt class="col-sm-5">Last Updated:</dt>
                            <dd class="col-sm-7">
                                {{ $category->updated_at->format('M d, Y') }}<br>
                                <small class="text-muted">{{ $category->updated_at->format('g:i A') }}</small>
                            </dd>
                            
                            <dt class="col-sm-5">Time Ago:</dt>
                            <dd class="col-sm-7">
                                <small class="text-muted">{{ $category->updated_at->diffForHumans() }}</small>
                            </dd>
                        </dl>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Edit Category
                            </a>
                            
                            <form method="POST" action="{{ route('admin.categories.updateStatus', $category) }}" class="mb-2">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="{{ $category->status === 'active' ? 'inactive' : 'active' }}">
                                <button type="submit" class="btn btn-outline-{{ $category->status === 'active' ? 'warning' : 'success' }} w-100">
                                    <i class="fas fa-{{ $category->status === 'active' ? 'pause' : 'play' }}"></i>
                                    {{ $category->status === 'active' ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                            
                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" 
                                  onsubmit="return confirm('Are you sure you want to delete this category? This action cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    <i class="fas fa-trash"></i> Delete Category
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                @if(!$category->image)
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-image fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">No Image</h6>
                            <p class="text-muted small">Add an image to make this category more visually appealing</p>
                            <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline-primary">
                                Add Image
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
