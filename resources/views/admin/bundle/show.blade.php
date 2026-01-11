@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-boxes"></i>
                    {{ $bundle->name }}
                </h1>
                <p class="page-header__subtitle">Bundle details and information</p>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.bundles.edit', $bundle) }}" class="btn btn-primary btn-icon">
                    <i class="fas fa-edit"></i>
                    <span>Edit</span>
                </a>
                <a href="{{ route('admin.bundles.index') }}" class="btn btn-outline-secondary btn-icon">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Bundles</span>
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
                        Bundle Information
                    </h3>
                </div>
                <div class="modern-card__body">
                    @if($bundle->images && $bundle->images->count() > 0)
                        <div style="margin-bottom: 2rem;">
                            <h4 style="margin-bottom: 1rem; color: var(--text-primary); font-size: 1rem; font-weight: 600;">
                                <i class="fas fa-images"></i>
                                Bundle Images
                            </h4>
                            <div class="row g-3">
                                @foreach($bundle->images as $index => $image)
                                    <div class="col-md-4 col-sm-6">
                                        <div class="position-relative">
                                            <img src="{{ $image->image_url }}"
                                                 alt="{{ $bundle->name }} - Image {{ $index + 1 }}"
                                                 class="img-fluid rounded shadow-sm"
                                                 style="width: 100%; height: 200px; object-fit: cover; cursor: pointer;"
                                                 onclick="openImageModal('{{ $image->image_url }}')">
                                            @if($index === 0)
                                                <span class="position-absolute top-0 start-0 badge bg-primary m-2">Main</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Basic Information -->
                    <div class="detail-grid" style="margin-bottom: 2rem;">
                        <div class="detail-item">
                            <div class="detail-item__label">
                                <i class="fas fa-tag"></i>
                                Bundle Name
                            </div>
                            <div class="detail-item__value">
                                <strong>{{ $bundle->name }}</strong>
                            </div>
                        </div>

                        @if($bundle->slug)
                        <div class="detail-item">
                            <div class="detail-item__label">
                                <i class="fas fa-link"></i>
                                Slug
                            </div>
                            <div class="detail-item__value">
                                <code class="code-block">{{ $bundle->slug }}</code>
                            </div>
                        </div>
                        @endif

                        <div class="detail-item">
                            <div class="detail-item__label">
                                <i class="fas fa-sort-numeric-down"></i>
                                Sort Order
                            </div>
                            <div class="detail-item__value">
                                {{ $bundle->sort_order ?? 0 }}
                            </div>
                        </div>
                    </div>

                    @if($bundle->short_description)
                    <!-- Short Description Section -->
                    <div style="margin-bottom: 2rem; padding-top: 2rem; border-top: 1px solid var(--border-color);">
                        <h4 style="margin-bottom: 1rem; color: var(--text-primary); font-size: 1rem; font-weight: 600;">
                            <i class="fas fa-align-left"></i>
                            Short Description
                        </h4>
                        <div class="description-content" style="line-height: 1.6; color: var(--text-secondary);">
                            {!! nl2br(e($bundle->short_description)) !!}
                        </div>
                    </div>
                    @endif

                    @if($bundle->description)
                    <!-- Full Description Section -->
                    <div style="margin-bottom: 2rem; padding-top: 2rem; border-top: 1px solid var(--border-color);">
                        <h4 style="margin-bottom: 1rem; color: var(--text-primary); font-size: 1rem; font-weight: 600;">
                            <i class="fas fa-file-alt"></i>
                            Full Description
                        </h4>
                        <div class="description-content" style="line-height: 1.6; color: var(--text-secondary);">
                            {!! nl2br(e($bundle->description)) !!}
                        </div>
                    </div>
                    @endif

                    <!-- Additional Information (Accordion) -->
                    @if($bundle->accordions && $bundle->accordions->count() > 0)
                    <div style="margin-bottom: 2rem; padding-top: 2rem; border-top: 1px solid var(--border-color);">
                        <h4 style="margin-bottom: 1rem; color: var(--text-primary); font-size: 1rem; font-weight: 600;">
                            <i class="fas fa-list"></i>
                            Additional Information
                        </h4>
                        <div class="accordion-modern" id="bundleAccordion">
                            @foreach($bundle->accordions as $index => $accordion)
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
                                         data-bs-parent="#bundleAccordion">
                                        <div class="accordion-modern__body">
                                            {!! $accordion->content !!}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

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
                                <div class="pricing-card__amount" style="font-size: 1.1rem; margin-bottom: 0.25rem;">${{ number_format($bundle->bundle_price, 2) }}</div>
                                <div class="pricing-card__label" style="font-size: 0.75rem;">Bundle Price</div>
                            </div>

                            @php
                                $totalProductsPrice = $bundle->products->sum(function($product) {
                                    return ($product->discount_price ?? $product->total_price) * ($product->pivot->quantity ?? 1);
                                });
                                $savings = $totalProductsPrice - $bundle->bundle_price;
                            @endphp

                            <div class="pricing-card pricing-card--info" style="padding: 1rem;">
                                <div class="pricing-card__icon" style="font-size: 1.2rem; margin-bottom: 0.5rem;">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                                <div class="pricing-card__amount" style="font-size: 1.1rem; margin-bottom: 0.25rem;">${{ number_format($totalProductsPrice, 2) }}</div>
                                <div class="pricing-card__label" style="font-size: 0.75rem;">Total Products Value</div>
                            </div>

                            @if($bundle->discount_percentage)
                            <div class="pricing-card pricing-card--success" style="padding: 1rem;">
                                <div class="pricing-card__icon" style="font-size: 1.2rem; margin-bottom: 0.5rem;">
                                    <i class="fas fa-percent"></i>
                                </div>
                                <div class="pricing-card__amount" style="font-size: 1.1rem; margin-bottom: 0.25rem;">{{ number_format($bundle->discount_percentage, 1) }}%</div>
                                <div class="pricing-card__label" style="font-size: 0.75rem;">Discount</div>
                            </div>
                            @endif

                            @if($savings > 0)
                            <div class="pricing-card pricing-card--warning" style="padding: 1rem;">
                                <div class="pricing-card__icon" style="font-size: 1.2rem; margin-bottom: 0.5rem;">
                                    <i class="fas fa-piggy-bank"></i>
                                </div>
                                <div class="pricing-card__amount" style="font-size: 1.1rem; margin-bottom: 0.25rem;">${{ number_format($savings, 2) }}</div>
                                <div class="pricing-card__label" style="font-size: 0.75rem;">Customer Saves</div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products in Bundle -->
            @if($bundle->products && $bundle->products->count() > 0)
            <div class="modern-card" style="margin-top: 2rem;">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-box"></i>
                        Products in Bundle ({{ $bundle->products->count() }})
                    </h3>
                </div>
                <div class="modern-card__body">
                    <div class="row">
                        @foreach($bundle->products as $product)
                            <div class="col-md-4 mb-4">
                                <div class="product-card-modern">
                                    <div class="product-card-modern__image">
                                        <img src="{{ $product->main_image }}"
                                             alt="{{ $product->name }}"
                                             class="product-card-modern__img"
                                             onerror="this.src='{{ asset('assets/images/placeholder.jpg') }}'">
                                    </div>
                                    <div class="product-card-modern__body">
                                        <h4 class="product-card-modern__name">{{ $product->name }}</h4>
                                        <div class="product-card-modern__meta">
                                            <span class="badge badge--info">
                                                <i class="fas fa-layer-group"></i>
                                                Qty: {{ $product->pivot->quantity ?? 1 }}
                                            </span>
                                        </div>
                                        <div class="product-card-modern__price">
                                            <strong class="text-success">${{ number_format($product->total_price, 2) }}</strong>
                                        </div>
                                        <a href="{{ route('admin.products.show', $product) }}" class="btn btn-outline-primary btn-sm mt-2" style="width: 100%;">
                                            <i class="fas fa-eye"></i>
                                            View Product
                                        </a>
                                    </div>
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
                                    {{ $bundle->created_at->format('M d, Y') }}
                                    <small>{{ $bundle->created_at->format('g:i A') }}</small>
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
                                    {{ $bundle->updated_at->format('M d, Y') }}
                                    <small>{{ $bundle->updated_at->format('g:i A') }}</small>
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
                                    {{ $bundle->updated_at->diffForHumans() }}
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
                        <div class="action-list__item action-list__item--{{ $bundle->status ? 'success' : 'secondary' }}" style="cursor: default;">
                            <i class="fas fa-{{ $bundle->status ? 'check-circle' : 'times-circle' }}"></i>
                            <span>{{ $bundle->status ? 'Active' : 'Inactive' }}</span>
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
                        <a href="{{ route('admin.bundles.edit', $bundle) }}" class="action-list__item action-list__item--primary">
                            <i class="fas fa-edit"></i>
                            <span>Edit Bundle</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>

                        <form method="POST"
                              action="{{ route('admin.bundles.updateStatus', $bundle) }}"
                              class="action-list__form">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="{{ $bundle->status == 1 ? '0' : '1' }}">
                            <button type="submit" class="action-list__item action-list__item--{{ $bundle->status == 1 ? 'warning' : 'success' }}">
                                <i class="fas fa-{{ $bundle->status == 1 ? 'pause' : 'play' }}"></i>
                                <span>{{ $bundle->status == 1 ? 'Deactivate' : 'Activate' }} Bundle</span>
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </form>

                        <form method="POST"
                              action="{{ route('admin.bundles.destroy', $bundle) }}"
                              class="action-list__form"
                              onsubmit="return confirm('Are you sure you want to delete this bundle? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-list__item action-list__item--danger">
                                <i class="fas fa-trash"></i>
                                <span>Delete Bundle</span>
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Image Card (if no images) -->
            @if(!$bundle->images || $bundle->images->count() === 0)
                <div class="modern-card modern-card--empty">
                    <div class="modern-card__body">
                        <div class="empty-state empty-state--compact">
                            <div class="empty-state__icon">
                                <i class="fas fa-image"></i>
                            </div>
                            <h4 class="empty-state__title">No Images</h4>
                            <p class="empty-state__text">Add images to make this bundle more visually appealing</p>
                            <a href="{{ route('admin.bundles.edit', $bundle) }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-plus"></i>
                                Add Images
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
        <img src="" alt="Bundle Image" class="image-modal__img" id="modalImage">
    </div>
</div>

<style>
.product-card-modern {
    border: 1px solid var(--border-color);
    border-radius: 8px;
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
    background: white;
}

.product-card-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.product-card-modern__image {
    width: 100%;
    height: 200px;
    overflow: hidden;
    background: #f8f9fa;
}

.product-card-modern__img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-card-modern__body {
    padding: 1rem;
}

.product-card-modern__name {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: var(--text-primary);
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.product-card-modern__meta {
    margin-bottom: 0.5rem;
}

.product-card-modern__price {
    font-size: 1.1rem;
    margin-bottom: 0.5rem;
}
</style>

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
