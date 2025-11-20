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

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Product Hero Image -->
            @if($product->images && $product->images->count() > 0)
            <div class="modern-card mb-4">
                <div class="modern-card__body">
                    <div class="product-hero-image">
                        <img src="{{ $product->images->first()->image_url }}"
                             alt="{{ $product->name }}"
                             class="product-hero-image__img"
                             onclick="openImageModal('{{ $product->images->first()->image_url }}')">
                        <div class="product-hero-image__badge">
                            <i class="fas fa-image"></i>
                            Main Image
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Product Information -->
            <div class="modern-card mb-4">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-info-circle"></i>
                        Product Information
                    </h3>
                </div>
                <div class="modern-card__body">
                    <div class="detail-grid">
                        <div class="detail-item">
                            <div class="detail-item__label">
                                <i class="fas fa-tag"></i>
                                Product Name
                            </div>
                            <div class="detail-item__value">
                                <strong>{{ $product->name }}</strong>
                            </div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-item__label">
                                <i class="fas fa-link"></i>
                                Slug
                            </div>
                            <div class="detail-item__value">
                                <code class="code-block">{{ $product->slug }}</code>
                            </div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-item__label">
                                <i class="fas fa-toggle-on"></i>
                                Status
                            </div>
                            <div class="detail-item__value">
                                {!! $product->status_badge !!}
                            </div>
                        </div>

                        @if($product->short_description)
                        <div class="detail-item detail-item--full">
                            <div class="detail-item__label">
                                <i class="fas fa-align-left"></i>
                                Short Description
                            </div>
                            <div class="detail-item__value">
                                <p>{{ $product->short_description }}</p>
                            </div>
                        </div>
                        @endif

                        @if($product->description)
                        <div class="detail-item detail-item--full">
                            <div class="detail-item__label">
                                <i class="fas fa-file-alt"></i>
                                Full Description
                            </div>
                            <div class="detail-item__value">
                                <div class="description-content">
                                    {!! nl2br(e($product->description)) !!}
                                </div>
                            </div>
                        </div>
                        @endif
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
                    <div class="pricing-grid">
                        <div class="pricing-card pricing-card--primary">
                            <div class="pricing-card__icon">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <div class="pricing-card__amount">${{ number_format($product->total_price, 2) }}</div>
                            <div class="pricing-card__label">Total Price (Inc. Tax)</div>
                        </div>
                        @if($product->discount_price)
                        <div class="pricing-card pricing-card--warning">
                            <div class="pricing-card__icon">
                                <i class="fas fa-tag"></i>
                            </div>
                            <div class="pricing-card__amount">${{ number_format($product->discount_price, 2) }}</div>
                            <div class="pricing-card__label">Discount Price</div>
                        </div>
                        @endif
                        <div class="pricing-card pricing-card--success">
                            <div class="pricing-card__icon">
                                <i class="fas fa-receipt"></i>
                            </div>
                            <div class="pricing-card__amount">${{ number_format($product->price_without_tax, 2) }}</div>
                            <div class="pricing-card__label">Price Without Tax</div>
                        </div>
                        <div class="pricing-card pricing-card--info">
                            <div class="pricing-card__icon">
                                <i class="fas fa-percentage"></i>
                            </div>
                            <div class="pricing-card__amount">${{ number_format($product->tax_amount, 2) }}</div>
                            <div class="pricing-card__label">Tax Amount ({{ $product->tax_percentage }}%)</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Type & Additional Info -->
            <div class="modern-card mb-4">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-star"></i>
                        Product Classification
                    </h3>
                </div>
                <div class="modern-card__body">
                    <div class="detail-grid">
                        @if($product->product_type)
                        <div class="detail-item">
                            <div class="detail-item__label">
                                <i class="fas fa-tags"></i>
                                Product Type
                            </div>
                            <div class="detail-item__value">
                                @if($product->product_type == 1)
                                    <span class="badge badge--primary">
                                        <i class="fas fa-star"></i>
                                        Featured
                                    </span>
                                @elseif($product->product_type == 2)
                                    <span class="badge badge--warning">
                                        <i class="fas fa-fire"></i>
                                        On Sale
                                    </span>
                                @elseif($product->product_type == 3)
                                    <span class="badge badge--success">
                                        <i class="fas fa-thumbs-up"></i>
                                        Top Rated
                                    </span>
                                @endif
                            </div>
                        </div>
                        @endif

                        @if($product->discount_price)
                        <div class="detail-item">
                            <div class="detail-item__label">
                                <i class="fas fa-percent"></i>
                                Discount
                            </div>
                            <div class="detail-item__value">
                                <strong class="text-success">${{ number_format($product->discount_price, 2) }}</strong>
                                @php
                                    $discountPercent = $product->total_price > 0 ? round((($product->total_price - $product->discount_price) / $product->total_price) * 100, 1) : 0;
                                @endphp
                                <span class="text-muted">({{ $discountPercent }}% off)</span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Additional Information (Accordion) -->
            @if($product->accordions && $product->accordions->count() > 0)
            <div class="modern-card mb-4">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-list"></i>
                        Additional Information
                    </h3>
                </div>
                <div class="modern-card__body">
                    <div class="accordion-modern" id="productAccordion">
                        @foreach($product->accordions as $index => $accordion)
                            <div class="accordion-modern__item">
                                <div class="accordion-modern__header" id="heading{{ $index }}">
                                    <button class="accordion-modern__button {{ $index > 0 ? 'collapsed' : '' }}"
                                            type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#collapse{{ $index }}"
                                            aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"
                                            aria-controls="collapse{{ $index }}">
                                        <i class="fas fa-chevron-down accordion-modern__icon"></i>
                                        <span>{{ $accordion->heading }}</span>
                                    </button>
                                </div>
                                <div id="collapse{{ $index }}"
                                     class="accordion-modern__collapse collapse {{ $index === 0 ? 'show' : '' }}"
                                     aria-labelledby="heading{{ $index }}"
                                     data-bs-parent="#productAccordion">
                                    <div class="accordion-modern__body">
                                        {!! nl2br(e($accordion->content)) !!}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Product Images Gallery -->
            @if($product->images && $product->images->count() > 0)
            <div class="modern-card mb-4">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-images"></i>
                        Product Images ({{ $product->images->count() }})
                    </h3>
                </div>
                <div class="modern-card__body">
                    <div class="image-gallery">
                        @foreach($product->images as $index => $image)
                            <div class="image-gallery__item" onclick="openImageModal('{{ $image->image_url }}')">
                                <img src="{{ $image->image_url }}"
                                     alt="{{ $product->name }} - Image {{ $index + 1 }}"
                                     class="image-gallery__img">
                                @if($index === 0)
                                    <div class="image-gallery__badge">
                                        <i class="fas fa-star"></i>
                                        Main
                                    </div>
                                @endif
                                <div class="image-gallery__overlay">
                                    <i class="fas fa-search-plus"></i>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
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
                    <div class="detail-grid">
                        <div class="detail-item">
                            <div class="detail-item__label">
                                <i class="fas fa-folder"></i>
                                Category
                            </div>
                            <div class="detail-item__value">
                                <span class="badge badge--primary">
                                    <i class="fas fa-tag"></i>
                                    {{ $product->category->name }}
                                </span>
                            </div>
                        </div>

                        @if($product->subCategory)
                        <div class="detail-item">
                            <div class="detail-item__label">
                                <i class="fas fa-folder-open"></i>
                                Sub Category
                            </div>
                            <div class="detail-item__value">
                                <span class="badge badge--info">
                                    {{ $product->subCategory->name }}
                                </span>
                            </div>
                        </div>
                        @endif

                        @if($product->brand)
                        <div class="detail-item">
                            <div class="detail-item__label">
                                <i class="fas fa-certificate"></i>
                                Brand
                            </div>
                            <div class="detail-item__value">
                                <span class="badge badge--secondary">
                                    {{ $product->brand->name }}
                                </span>
                            </div>
                        </div>
                        @endif

                        <div class="detail-item detail-item--full">
                            <div class="detail-item__label">
                                <i class="fas fa-sitemap"></i>
                                Category Path
                            </div>
                            <div class="detail-item__value">
                                <p class="text-muted mb-0">{{ $product->category_path }}</p>
                            </div>
                        </div>
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
                    <div class="action-buttons-vertical">
                        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary btn-block">
                            <i class="fas fa-edit"></i>
                            <span>Edit Product</span>
                        </a>

                        <form action="{{ route('admin.products.updateStatus', $product) }}" method="POST" class="action-form">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="{{ $product->status == 1 ? '0' : '1' }}">
                            <button type="submit" class="btn btn-{{ $product->status == 1 ? 'warning' : 'success' }} btn-block">
                                <i class="fas fa-{{ $product->status == 1 ? 'pause' : 'play' }}"></i>
                                <span>{{ $product->status == 1 ? 'Deactivate' : 'Activate' }} Product</span>
                            </button>
                        </form>

                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                              onsubmit="return confirm('Are you sure you want to delete this product? This action cannot be undone.')"
                              class="action-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-block">
                                <i class="fas fa-trash"></i>
                                <span>Delete Product</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Product Statistics -->
            <div class="modern-card mb-4">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-chart-bar"></i>
                        Statistics
                    </h3>
                </div>
                <div class="modern-card__body">
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-card__icon stat-card__icon--primary">
                                <i class="fas fa-images"></i>
                            </div>
                            <div class="stat-card__content">
                                <div class="stat-card__value">{{ $product->images ? $product->images->count() : 0 }}</div>
                                <div class="stat-card__label">Images</div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-card__icon stat-card__icon--info">
                                <i class="fas fa-list"></i>
                            </div>
                            <div class="stat-card__content">
                                <div class="stat-card__value">{{ $product->accordions ? $product->accordions->count() : 0 }}</div>
                                <div class="stat-card__label">Info Sections</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timestamp Cards -->
            <div class="modern-card">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-clock"></i>
                        Timestamps
                    </h3>
                </div>
                <div class="modern-card__body">
                    <div class="timestamp-card">
                        <div class="timestamp-card__icon">
                            <i class="fas fa-plus-circle"></i>
                        </div>
                        <div class="timestamp-card__content">
                            <div class="timestamp-card__label">Created</div>
                            <div class="timestamp-card__value">{{ $product->created_at->format('M d, Y') }}</div>
                            <div class="timestamp-card__time">{{ $product->created_at->format('g:i A') }}</div>
                        </div>
                    </div>
                    <div class="timestamp-card">
                        <div class="timestamp-card__icon">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div class="timestamp-card__content">
                            <div class="timestamp-card__label">Updated</div>
                            <div class="timestamp-card__value">{{ $product->updated_at->format('M d, Y') }}</div>
                            <div class="timestamp-card__time">{{ $product->updated_at->format('g:i A') }}</div>
                        </div>
                    </div>
                    <div class="timestamp-card">
                        <div class="timestamp-card__icon">
                            <i class="fas fa-fingerprint"></i>
                        </div>
                        <div class="timestamp-card__content">
                            <div class="timestamp-card__label">UUID</div>
                            <div class="timestamp-card__value">
                                <code class="code-block code-block--small">{{ $product->uuid }}</code>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="image-modal" id="imageModal" onclick="closeImageModal()">
    <div class="image-modal__content" onclick="event.stopPropagation()">
        <button class="image-modal__close" onclick="closeImageModal()">
            <i class="fas fa-times"></i>
        </button>
        <img src="" alt="Product Image" class="image-modal__img" id="modalImage">
    </div>
</div>
@endsection
