@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="content-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="content-title">{{ $subcategory->name }}</h1>
                <p class="content-subtitle">Sub category details</p>
            </div>
            <div class="col-auto">
                <div class="btn-group" role="group">
                    <a href="{{ route('admin.subcategories.edit', $subcategory) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('admin.subcategories.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Sub Categories
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
                        <h5 class="card-title mb-0">Sub Category Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if($subcategory->image)
                                <div class="col-md-4 mb-3">
                                    <img src="{{ $subcategory->image_url }}" alt="{{ $subcategory->name }}" 
                                         class="img-fluid rounded shadow-sm">
                                </div>
                            @endif
                            <div class="{{ $subcategory->image ? 'col-md-8' : 'col-md-12' }}">
                                <dl class="row">
                                    <dt class="col-sm-4">Name:</dt>
                                    <dd class="col-sm-8">{{ $subcategory->name }}</dd>
                                    
                                    <dt class="col-sm-4">Parent Category:</dt>
                                    <dd class="col-sm-8">
                                        <a href="{{ route('admin.categories.show', $subcategory->category) }}" class="badge bg-info text-decoration-none">
                                            {{ $subcategory->category->name }}
                                        </a>
                                    </dd>
                                    
                                    <dt class="col-sm-4">Full Path:</dt>
                                    <dd class="col-sm-8">
                                        <span class="text-muted">{{ $subcategory->full_name }}</span>
                                    </dd>
                                    
                                    <dt class="col-sm-4">Slug:</dt>
                                    <dd class="col-sm-8"><code>{{ $subcategory->slug }}</code></dd>
                                    
                                    <dt class="col-sm-4">Status:</dt>
                                    <dd class="col-sm-8">
                                        @if($subcategory->status === 'active')
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </dd>
                                    
                                    <dt class="col-sm-4">UUID:</dt>
                                    <dd class="col-sm-8">
                                        <small class="text-muted font-monospace">{{ $subcategory->uuid }}</small>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Category Hierarchy</h5>
                    </div>
                    <div class="card-body">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.categories.show', $subcategory->category) }}">
                                        <i class="fas fa-tag"></i> {{ $subcategory->category->name }}
                                    </a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    <i class="fas fa-tags"></i> {{ $subcategory->name }}
                                </li>
                            </ol>
                        </nav>
                        
                        <div class="d-flex align-items-center gap-3">
                            <div class="text-center">
                                <div class="border rounded p-3 bg-light">
                                    <i class="fas fa-tag fa-2x text-primary mb-2"></i>
                                    <h6 class="mb-0">{{ $subcategory->category->name }}</h6>
                                    <small class="text-muted">Parent Category</small>
                                </div>
                            </div>
                            <div class="text-center">
                                <i class="fas fa-arrow-right fa-2x text-muted"></i>
                            </div>
                            <div class="text-center">
                                <div class="border rounded p-3 bg-primary text-white">
                                    <i class="fas fa-tags fa-2x mb-2"></i>
                                    <h6 class="mb-0">{{ $subcategory->name }}</h6>
                                    <small>Sub Category</small>
                                </div>
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
                                {{ $subcategory->created_at->format('M d, Y') }}<br>
                                <small class="text-muted">{{ $subcategory->created_at->format('g:i A') }}</small>
                            </dd>
                            
                            <dt class="col-sm-5">Last Updated:</dt>
                            <dd class="col-sm-7">
                                {{ $subcategory->updated_at->format('M d, Y') }}<br>
                                <small class="text-muted">{{ $subcategory->updated_at->format('g:i A') }}</small>
                            </dd>
                            
                            <dt class="col-sm-5">Time Ago:</dt>
                            <dd class="col-sm-7">
                                <small class="text-muted">{{ $subcategory->updated_at->diffForHumans() }}</small>
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
                            <a href="{{ route('admin.subcategories.edit', $subcategory) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Edit Sub Category
                            </a>
                            
                            <a href="{{ route('admin.categories.show', $subcategory->category) }}" class="btn btn-outline-info">
                                <i class="fas fa-tag"></i> View Parent Category
                            </a>
                            
                            <form method="POST" action="{{ route('admin.subcategories.updateStatus', $subcategory) }}" class="mb-2">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="{{ $subcategory->status === 'active' ? 'inactive' : 'active' }}">
                                <button type="submit" class="btn btn-outline-{{ $subcategory->status === 'active' ? 'warning' : 'success' }} w-100">
                                    <i class="fas fa-{{ $subcategory->status === 'active' ? 'pause' : 'play' }}"></i>
                                    {{ $subcategory->status === 'active' ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                            
                            <form method="POST" action="{{ route('admin.subcategories.destroy', $subcategory) }}" 
                                  onsubmit="return confirm('Are you sure you want to delete this subcategory? This action cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    <i class="fas fa-trash"></i> Delete Sub Category
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                @if(!$subcategory->image)
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-image fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">No Image</h6>
                            <p class="text-muted small">Add an image to make this subcategory more visually appealing</p>
                            <a href="{{ route('admin.subcategories.edit', $subcategory) }}" class="btn btn-sm btn-outline-primary">
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
