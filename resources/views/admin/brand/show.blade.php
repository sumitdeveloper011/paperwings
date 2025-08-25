@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="content-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="content-title">{{ $brand->name }}</h1>
                <p class="content-subtitle">Brand details</p>
            </div>
            <div class="col-auto">
                <div class="btn-group" role="group">
                    <a href="{{ route('admin.brands.edit', $brand) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('admin.brands.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Brands
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
                        <h5 class="card-title mb-0">Brand Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if($brand->image)
                                <div class="col-md-4 mb-3">
                                    <img src="{{ $brand->image_url }}" alt="{{ $brand->name }}" 
                                         class="img-fluid rounded shadow-sm">
                                </div>
                            @endif
                            <div class="{{ $brand->image ? 'col-md-8' : 'col-md-12' }}">
                                <dl class="row">
                                    <dt class="col-sm-3">Name:</dt>
                                    <dd class="col-sm-9">{{ $brand->name }}</dd>
                                    
                                    <dt class="col-sm-3">Slug:</dt>
                                    <dd class="col-sm-9"><code>{{ $brand->slug }}</code></dd>
                                    
                                    <dt class="col-sm-3">UUID:</dt>
                                    <dd class="col-sm-9">
                                        <small class="text-muted font-monospace">{{ $brand->uuid }}</small>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                @if($brand->image)
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Brand Logo</h5>
                        </div>
                        <div class="card-body text-center">
                            <img src="{{ $brand->image_url }}" alt="{{ $brand->name }}" 
                                 class="img-fluid rounded shadow" style="max-height: 300px;">
                        </div>
                    </div>
                @endif
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
                                {{ $brand->created_at->format('M d, Y') }}<br>
                                <small class="text-muted">{{ $brand->created_at->format('g:i A') }}</small>
                            </dd>
                            
                            <dt class="col-sm-5">Last Updated:</dt>
                            <dd class="col-sm-7">
                                {{ $brand->updated_at->format('M d, Y') }}<br>
                                <small class="text-muted">{{ $brand->updated_at->format('g:i A') }}</small>
                            </dd>
                            
                            <dt class="col-sm-5">Time Ago:</dt>
                            <dd class="col-sm-7">
                                <small class="text-muted">{{ $brand->updated_at->diffForHumans() }}</small>
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
                            <a href="{{ route('admin.brands.edit', $brand) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Edit Brand
                            </a>
                            
                            <form method="POST" action="{{ route('admin.brands.destroy', $brand) }}" 
                                  onsubmit="return confirm('Are you sure you want to delete this brand? This action cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    <i class="fas fa-trash"></i> Delete Brand
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                @if(!$brand->image)
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-image fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">No Logo</h6>
                            <p class="text-muted small">Add a logo to make this brand more recognizable</p>
                            <a href="{{ route('admin.brands.edit', $brand) }}" class="btn btn-sm btn-outline-primary">
                                Add Logo
                            </a>
                        </div>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Brand Stats</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border-end">
                                    <h5 class="text-primary mb-1">{{ \Str::length($brand->name) }}</h5>
                                    <small class="text-muted">Characters</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <h5 class="text-success mb-1">{{ \Str::wordCount($brand->name) }}</h5>
                                <small class="text-muted">Words</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
