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
            <div class="modern-card">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-info-circle"></i>
                        Product Information
                    </h3>
                </div>
                <div class="modern-card__body">
                    @if($product->images && $product->images->count() > 0)
                        <div class="category-image-large">
                            <img src="{{ $product->images->first()->image_url }}"
                                 alt="{{ $product->name }}"
                                 class="category-image-large__img"
                                 onclick="openImageModal('{{ $product->images->first()->image_url }}')">
                        </div>
                    @endif

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

                        @if($product->short_description)
                        <div class="detail-item detail-item--full">
                            <div class="detail-item__label">
                                <i class="fas fa-align-left"></i>
                                Short Description
                            </div>
                            <div class="detail-item__value">
                                <div class="description-content">
                                    {!! $product->short_description !!}
                                </div>
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
                                    {!! $product->description !!}
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="detail-item">
                            <div class="detail-item__label">
                                <i class="fas fa-fingerprint"></i>
                                UUID
                            </div>
                            <div class="detail-item__value">
                                <code class="code-block code-block--small">{{ $product->uuid }}</code>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing Information -->
                    <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border-color);">
                        <h4 style="margin-bottom: 1rem; color: var(--text-primary);">
                            <i class="fas fa-dollar-sign"></i>
                            Pricing Details
                        </h4>
                        <div class="pricing-grid" style="grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 1rem;">
                            <div class="pricing-card pricing-card--primary" style="padding: 1rem;">
                                <div class="pricing-card__icon" style="font-size: 1.2rem; margin-bottom: 0.5rem;">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                                <div class="pricing-card__amount" style="font-size: 1.1rem; margin-bottom: 0.25rem;">${{ number_format($product->total_price, 2) }}</div>
                                <div class="pricing-card__label" style="font-size: 0.75rem;">Total Price (Inc. Tax)</div>
                            </div>
                            @if($product->discount_price)
                            <div class="pricing-card pricing-card--warning" style="padding: 1rem;">
                                <div class="pricing-card__icon" style="font-size: 1.2rem; margin-bottom: 0.5rem;">
                                    <i class="fas fa-tag"></i>
                                </div>
                                <div class="pricing-card__amount" style="font-size: 1.1rem; margin-bottom: 0.25rem;">${{ number_format($product->discount_price, 2) }}</div>
                                <div class="pricing-card__label" style="font-size: 0.75rem;">Discount Price</div>
                            </div>
                            @endif
                            <div class="pricing-card pricing-card--success" style="padding: 1rem;">
                                <div class="pricing-card__icon" style="font-size: 1.2rem; margin-bottom: 0.5rem;">
                                    <i class="fas fa-receipt"></i>
                                </div>
                                <div class="pricing-card__amount" style="font-size: 1.1rem; margin-bottom: 0.25rem;">
                                    ${{ number_format($product->discount_price ? round($product->discount_price / 1.15, 2) : $product->price_without_tax, 2) }}
                                </div>
                                <div class="pricing-card__label" style="font-size: 0.75rem;">Price Without Tax</div>
                            </div>
                            <div class="pricing-card pricing-card--info" style="padding: 1rem;">
                                <div class="pricing-card__icon" style="font-size: 1.2rem; margin-bottom: 0.5rem;">
                                    <i class="fas fa-percentage"></i>
                                </div>
                                <div class="pricing-card__amount" style="font-size: 1.1rem; margin-bottom: 0.25rem;">
                                    ${{ number_format($product->discount_price ? round($product->discount_price - ($product->discount_price / 1.15), 2) : $product->tax_amount, 2) }}
                                </div>
                                <div class="pricing-card__label" style="font-size: 0.75rem;">Tax Amount ({{ $product->tax_percentage }}%)</div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information (Accordion) -->
                    @if($product->accordions && $product->accordions->count() > 0)
                    <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border-color);">
                        <h4 style="margin-bottom: 1rem; color: var(--text-primary);">
                            <i class="fas fa-list"></i>
                            Additional Information
                        </h4>
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
                                            {!! $accordion->content !!}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Product Images Gallery -->
                    @if($product->images && $product->images->count() > 1)
                    <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border-color);">
                        <h4 style="margin-bottom: 1rem; color: var(--text-primary);">
                            <i class="fas fa-images"></i>
                            Product Images ({{ $product->images->count() }})
                        </h4>
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
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Categories & Product Type -->
            <div class="modern-card">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-tags"></i>
                        Categories & Type
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

                        @if($product->product_type)
                        <div class="detail-item">
                            <div class="detail-item__label">
                                <i class="fas fa-star"></i>
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
                    </div>
                </div>
            </div>

            <!-- Timestamps Card -->
            <div class="modern-card">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-clock"></i>
                        Timestamps
                    </h3>
                </div>
                <div class="modern-card__body">
                    <div class="timestamp-list">
                        <div class="timestamp-item">
                            <div class="timestamp-item__icon">
                                <i class="fas fa-plus-circle"></i>
                            </div>
                            <div class="timestamp-item__content">
                                <div class="timestamp-item__label">Created</div>
                                <div class="timestamp-item__value">
                                    {{ $product->created_at->format('M d, Y') }}
                                    <small>{{ $product->created_at->format('g:i A') }}</small>
                                </div>
                            </div>
                        </div>

                        <div class="timestamp-item">
                            <div class="timestamp-item__icon">
                                <i class="fas fa-edit"></i>
                            </div>
                            <div class="timestamp-item__content">
                                <div class="timestamp-item__label">Last Updated</div>
                                <div class="timestamp-item__value">
                                    {{ $product->updated_at->format('M d, Y') }}
                                    <small>{{ $product->updated_at->format('g:i A') }}</small>
                                </div>
                            </div>
                        </div>

                        <div class="timestamp-item">
                            <div class="timestamp-item__icon">
                                <i class="fas fa-history"></i>
                            </div>
                            <div class="timestamp-item__content">
                                <div class="timestamp-item__label">Time Ago</div>
                                <div class="timestamp-item__value">
                                    {{ $product->updated_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Card -->
            <div class="modern-card">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-toggle-on"></i>
                        Status
                    </h3>
                </div>
                <div class="modern-card__body">
                    <div class="action-list">
                        <div class="action-list__item action-list__item--{{ (int)$product->status === 1 ? 'success' : 'secondary' }}" style="cursor: default;">
                            <i class="fas fa-{{ (int)$product->status === 1 ? 'check-circle' : 'times-circle' }}"></i>
                            <span>{{ (int)$product->status === 1 ? 'Active' : 'Inactive' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="modern-card">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-bolt"></i>
                        Quick Actions
                    </h3>
                </div>
                <div class="modern-card__body">
                    <div class="action-list">
                        <a href="{{ route('admin.products.edit', $product) }}" class="action-list__item action-list__item--primary">
                            <i class="fas fa-edit"></i>
                            <span>Edit Product</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>

                        <form method="POST"
                              action="{{ route('admin.products.updateStatus', $product) }}"
                              class="action-list__form">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="{{ $product->status == 1 ? '0' : '1' }}">
                            <button type="submit" class="action-list__item action-list__item--{{ $product->status == 1 ? 'warning' : 'success' }}">
                                <i class="fas fa-{{ $product->status == 1 ? 'pause' : 'play' }}"></i>
                                <span>{{ $product->status == 1 ? 'Deactivate' : 'Activate' }} Product</span>
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </form>

                        <form method="POST"
                              action="{{ route('admin.products.destroy', $product) }}"
                              class="action-list__form"
                              onsubmit="return confirm('Are you sure you want to delete this product? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-list__item action-list__item--danger">
                                <i class="fas fa-trash"></i>
                                <span>Delete Product</span>
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Image Card (if no image) -->
            @if(!$product->images || $product->images->count() === 0)
                <div class="modern-card modern-card--empty">
                    <div class="modern-card__body">
                        <div class="empty-state empty-state--compact">
                            <div class="empty-state__icon">
                                <i class="fas fa-image"></i>
                            </div>
                            <h4 class="empty-state__title">No Image</h4>
                            <p class="empty-state__text">Add an image to make this product more visually appealing</p>
                            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-plus"></i>
                                Add Image
                            </a>
                        </div>
                    </div>
                </div>
            @endif
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

<script>
function openImageModal(imageUrl) {
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    if (modal && modalImage) {
        modalImage.src = imageUrl;
        modal.style.display = 'flex';
    }
}

function closeImageModal() {
    const modal = document.getElementById('imageModal');
    if (modal) {
        modal.style.display = 'none';
    }
}
</script>
@endsection
