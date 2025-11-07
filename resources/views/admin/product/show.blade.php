@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-box"></i>
                    {{ $product->name }}
                </h1>
                <p class="page-header__subtitle">Product details and information</p>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary btn-icon">
                    <i class="fas fa-edit"></i>
                    <span>Edit</span>
                </a>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-icon">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Products</span>
                </a>
            </div>
        </div>
    </div>

    <div class="content-body">
        <div class="row">
            <!-- Left Column - Product Details -->
            <div class="col-lg-8">
                <!-- Basic Information -->
                <div class="modern-card mb-4">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-info-circle"></i>
                            Product Information
                        </h3>
                    </div>
                    <div class="modern-card__body">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Name:</strong>
                                <p>{{ $product->name }}</p>
                            </div>
                            <div class="col-md-3">
                                <strong>Status:</strong>
                                <p>{!! $product->status_badge !!}</p>
                            </div>
                            <div class="col-md-3">
                                <strong>Created:</strong>
                                <p>{{ $product->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <strong>Slug:</strong>
                                <p><code>{{ $product->slug }}</code></p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <strong>Short Description:</strong>
                                <p>{{ $product->short_description }}</p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <strong>Description:</strong>
                                <div class="border rounded p-3 bg-light">
                                    {!! nl2br(e($product->description)) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pricing Information -->
                <div class="modern-card mb-4">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-dollar-sign"></i>
                            Pricing Details
                        </h3>
                    </div>
                    <div class="modern-card__body">
                        <div class="row text-center">
                            <div class="col-md-4">
                                <div class="border rounded p-3">
                                    <h3 class="text-primary mb-0">${{ number_format($product->total_price, 2) }}</h3>
                                    <small class="text-muted">Total Price (Inc. Tax)</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-3">
                                    <h3 class="text-success mb-0">${{ number_format($product->price_without_tax, 2) }}</h3>
                                    <small class="text-muted">Price Without Tax</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-3">
                                    <h3 class="text-info mb-0">${{ number_format($product->tax_amount, 2) }}</h3>
                                    <small class="text-muted">Tax Amount ({{ $product->tax_percentage }}%)</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Information (Accordion) -->
                @if($product->accordion_data && count($product->accordion_data) > 0)
                <div class="modern-card mb-4">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-list"></i>
                            Additional Information
                        </h3>
                    </div>
                    <div class="modern-card__body">
                        <div class="accordion" id="productAccordion">
                            @foreach($product->accordion_data as $index => $item)
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading{{ $index }}">
                                        <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" type="button" 
                                                data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}" 
                                                aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="collapse{{ $index }}">
                                            {{ $item['heading'] }}
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $index }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" 
                                         aria-labelledby="heading{{ $index }}" data-bs-parent="#productAccordion">
                                        <div class="accordion-body">
                                            {!! nl2br(e($item['content'])) !!}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Product Images -->
                @if($product->images && count($product->images) > 0)
                <div class="modern-card mb-4">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-images"></i>
                            Product Images ({{ count($product->images) }})
                        </h3>
                    </div>
                    <div class="modern-card__body">
                        <div class="row g-3">
                            @foreach($product->images as $index => $image)
                                <div class="col-md-4 col-sm-6">
                                    <div class="position-relative">
                                        <img src="{{ asset('storage/' . $image) }}" alt="{{ $product->name }} - Image {{ $index + 1 }}" 
                                             class="img-fluid rounded shadow-sm" style="width: 100%; height: 200px; object-fit: cover;">
                                        @if($index === 0)
                                            <span class="position-absolute top-0 start-0 badge bg-primary m-2">Main Image</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Right Column - Categories & Metadata -->
            <div class="col-lg-4">
                <!-- Categories & Brand -->
                <div class="modern-card mb-4">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-tags"></i>
                            Categories & Brand
                        </h3>
                    </div>
                    <div class="modern-card__body">
                        <div class="mb-3">
                            <strong>Category:</strong>
                            <div class="mt-1">
                                <span class="badge bg-primary">{{ $product->category->name }}</span>
                            </div>
                        </div>

                        @if($product->subCategory)
                        <div class="mb-3">
                            <strong>Sub Category:</strong>
                            <div class="mt-1">
                                <span class="badge bg-info">{{ $product->subCategory->name }}</span>
                            </div>
                        </div>
                        @endif

                        @if($product->brand)
                        <div class="mb-3">
                            <strong>Brand:</strong>
                            <div class="mt-1">
                                <span class="badge bg-secondary">{{ $product->brand->name }}</span>
                            </div>
                        </div>
                        @endif

                        <div class="mb-0">
                            <strong>Category Path:</strong>
                            <p class="text-muted mb-0">{{ $product->category_path }}</p>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="modern-card mb-4">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-bolt"></i>
                            Quick Actions
                        </h3>
                    </div>
                    <div class="modern-card__body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary">
                                <i class="fas fa-edit me-2"></i>Edit Product
                            </a>
                            
                            <form action="{{ route('admin.products.updateStatus', $product) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="{{ $product->status === 'active' ? 'inactive' : 'active' }}">
                                <button type="submit" class="btn btn-outline-{{ $product->status === 'active' ? 'warning' : 'success' }} w-100">
                                    <i class="fas fa-{{ $product->status === 'active' ? 'pause' : 'play' }} me-2"></i>
                                    {{ $product->status === 'active' ? 'Deactivate' : 'Activate' }} Product
                                </button>
                            </form>

                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" 
                                  onsubmit="return confirm('Are you sure you want to delete this product? This action cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    <i class="fas fa-trash me-2"></i>Delete Product
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Product Statistics -->
                <div class="modern-card">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-chart-bar"></i>
                            Product Statistics
                        </h3>
                    </div>
                    <div class="modern-card__body">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border-end">
                                    <h4 class="text-primary mb-0">{{ $product->images ? count($product->images) : 0 }}</h4>
                                    <small class="text-muted">Images</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <h4 class="text-info mb-0">{{ $product->accordion_data ? count($product->accordion_data) : 0 }}</h4>
                                <small class="text-muted">Info Sections</small>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="small text-muted">
                            <div class="d-flex justify-content-between">
                                <span>Created:</span>
                                <span>{{ $product->created_at->format('M d, Y g:i A') }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Updated:</span>
                                <span>{{ $product->updated_at->format('M d, Y g:i A') }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>UUID:</span>
                                <span><code class="small">{{ Str::limit($product->uuid, 8) }}...</code></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection