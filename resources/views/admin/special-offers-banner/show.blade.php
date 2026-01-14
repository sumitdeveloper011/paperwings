@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-tag"></i>
                    {{ $specialOffersBanner->title ?? 'Special Offers Banner' }}
                </h1>
                <p class="page-header__subtitle">Banner details and information</p>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.special-offers-banners.edit', $specialOffersBanner) }}" class="btn btn-primary btn-icon">
                    <i class="fas fa-edit"></i>
                    <span>Edit</span>
                </a>
                <a href="{{ route('admin.special-offers-banners.index') }}" class="btn btn-outline-secondary btn-icon">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Banners</span>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            @if($specialOffersBanner->image_url)
            <div class="modern-card modern-card--glass">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-image"></i>
                        Banner Image
                    </h3>
                </div>
                <div class="modern-card__body">
                    <div class="category-image-large category-image-large--enhanced">
                        @php
                            $mediumImageUrl = $specialOffersBanner->medium_url ?? $specialOffersBanner->image_url ?? asset('assets/images/placeholder.jpg');
                            $originalImageUrl = $specialOffersBanner->image_url ?? asset('assets/images/placeholder.jpg');
                        @endphp
                        <img src="{{ $mediumImageUrl }}" 
                             alt="{{ $specialOffersBanner->title }}" 
                             class="category-image-large__img"
                             onerror="this.src='{{ asset('assets/images/placeholder.jpg') }}'">
                        <div class="category-image-large__overlay">
                            <a href="{{ $originalImageUrl }}" target="_blank" class="category-image-large__zoom">
                                <i class="fas fa-search-plus"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="modern-card modern-card--glass" style="margin-top: 1.5rem;">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-info-circle"></i>
                        Banner Information
                    </h3>
                </div>
                <div class="modern-card__body">
                    <div class="detail-grid detail-grid--enhanced">
                        <div class="detail-item detail-item--animated" style="animation-delay: 0.1s;">
                            <div class="detail-item__label">
                                <i class="fas fa-heading"></i>
                                Title
                            </div>
                            <div class="detail-item__value">
                                {{ $specialOffersBanner->title }}
                            </div>
                        </div>
                        
                        @if($specialOffersBanner->description)
                        <div class="detail-item detail-item--animated" style="animation-delay: 0.2s;">
                            <div class="detail-item__label">
                                <i class="fas fa-align-left"></i>
                                Description
                            </div>
                            <div class="detail-item__value">
                                <div class="page-content" style="line-height: 1.8; color: var(--text-primary);">
                                    {!! $specialOffersBanner->description !!}
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($specialOffersBanner->button_text)
                        <div class="detail-item detail-item--animated" style="animation-delay: 0.3s;">
                            <div class="detail-item__label">
                                <i class="fas fa-mouse-pointer"></i>
                                Button
                            </div>
                            <div class="detail-item__value">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="badge bg-primary" style="font-size: 0.9rem; padding: 0.5rem 1rem;">
                                        {{ $specialOffersBanner->button_text }}
                                    </span>
                                    @if($specialOffersBanner->button_link)
                                        <a href="{{ $specialOffersBanner->button_link }}" 
                                           target="_blank" 
                                           class="btn btn-sm btn-outline-primary"
                                           rel="noopener noreferrer">
                                            <i class="fas fa-external-link-alt"></i>
                                            View Link
                                        </a>
                                    @endif
                                </div>
                                @if($specialOffersBanner->button_link)
                                <small class="text-muted d-block mt-2">
                                    <code class="code-block code-block--small">{{ $specialOffersBanner->button_link }}</code>
                                </small>
                                @endif
                            </div>
                        </div>
                        @endif

                        <div class="detail-item detail-item--animated" style="animation-delay: 0.4s;">
                            <div class="detail-item__label">
                                <i class="fas fa-calendar-alt"></i>
                                Date Range
                            </div>
                            <div class="detail-item__value">
                                <div class="d-flex flex-column gap-2">
                                    <div>
                                        <strong>Start:</strong> 
                                        @if($specialOffersBanner->start_date)
                                            <span class="text-success">{{ $specialOffersBanner->start_date->format('d-m-Y') }}</span>
                                        @else
                                            <span class="text-muted">Not set</span>
                                        @endif
                                    </div>
                                    <div>
                                        <strong>End:</strong> 
                                        @if($specialOffersBanner->end_date)
                                            <span class="text-danger">{{ $specialOffersBanner->end_date->format('d-m-Y') }}</span>
                                        @else
                                            <span class="text-muted">Not set</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="detail-item detail-item--animated" style="animation-delay: 0.5s;">
                            <div class="detail-item__label">
                                <i class="fas fa-fingerprint"></i>
                                UUID
                            </div>
                            <div class="detail-item__value">
                                <code class="code-block code-block--small">{{ $specialOffersBanner->uuid }}</code>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Status & Settings Card -->
            <div class="modern-card modern-card--glass">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-cog"></i>
                        Status & Settings
                    </h3>
                </div>
                <div class="modern-card__body">
                    <div class="detail-grid detail-grid--enhanced">
                        <div class="detail-item detail-item--animated" style="animation-delay: 0.1s;">
                            <div class="detail-item__label">
                                <i class="fas fa-toggle-on"></i>
                                Status
                            </div>
                            <div class="detail-item__value">
                                @if($specialOffersBanner->status)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </div>
                        </div>

                        <div class="detail-item detail-item--animated" style="animation-delay: 0.2s;">
                            <div class="detail-item__label">
                                <i class="fas fa-sort-numeric-down"></i>
                                Sort Order
                            </div>
                            <div class="detail-item__value">
                                {{ $specialOffersBanner->sort_order ?? 0 }}
                            </div>
                        </div>

                        <div class="detail-item detail-item--animated" style="animation-delay: 0.3s;">
                            <div class="detail-item__label">
                                <i class="fas fa-hourglass-half"></i>
                                Show Countdown
                            </div>
                            <div class="detail-item__value">
                                @if($specialOffersBanner->show_countdown)
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-secondary">No</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timestamps Card -->
            <div class="modern-card modern-card--glass" style="margin-top: 1.5rem;">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-clock"></i>
                        Timestamps
                    </h3>
                </div>
                <div class="modern-card__body">
                    <div class="timestamp-list timestamp-list--enhanced">
                        <div class="timestamp-item timestamp-item--animated" style="animation-delay: 0.1s;">
                            <div class="timestamp-item__icon">
                                <i class="fas fa-plus-circle"></i>
                            </div>
                            <div class="timestamp-item__content">
                                <div class="timestamp-item__label">Created</div>
                                <div class="timestamp-item__value">
                                    {{ $specialOffersBanner->created_at->format('M d, Y') }}
                                    <small>{{ $specialOffersBanner->created_at->format('g:i A') }}</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="timestamp-item timestamp-item--animated" style="animation-delay: 0.2s;">
                            <div class="timestamp-item__icon">
                                <i class="fas fa-edit"></i>
                            </div>
                            <div class="timestamp-item__content">
                                <div class="timestamp-item__label">Last Updated</div>
                                <div class="timestamp-item__value">
                                    {{ $specialOffersBanner->updated_at->format('M d, Y') }}
                                    <small>{{ $specialOffersBanner->updated_at->format('g:i A') }}</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="timestamp-item timestamp-item--animated" style="animation-delay: 0.3s;">
                            <div class="timestamp-item__icon">
                                <i class="fas fa-history"></i>
                            </div>
                            <div class="timestamp-item__content">
                                <div class="timestamp-item__label">Time Ago</div>
                                <div class="timestamp-item__value">
                                    {{ $specialOffersBanner->updated_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions Card -->
            <div class="modern-card modern-card--glass" style="margin-top: 1.5rem;">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-bolt"></i>
                        Quick Actions
                    </h3>
                </div>
                <div class="modern-card__body">
                    <div class="action-list action-list--enhanced">
                        <a href="{{ route('admin.special-offers-banners.edit', $specialOffersBanner) }}" class="action-list__item action-list__item--primary action-list__item--ripple">
                            <i class="fas fa-edit"></i>
                            <span>Edit Banner</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        
                        <form method="POST" 
                              action="{{ route('admin.special-offers-banners.destroy', $specialOffersBanner) }}" 
                              class="action-list__form"
                              onsubmit="return confirm('Are you sure you want to delete this banner? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-list__item action-list__item--danger action-list__item--ripple">
                                <i class="fas fa-trash"></i>
                                <span>Delete Banner</span>
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

