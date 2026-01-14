@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-file-alt"></i>
                    {{ $page->title }}
                </h1>
                <p class="page-header__subtitle">Page details and information</p>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-primary btn-icon">
                    <i class="fas fa-edit"></i>
                    <span>Edit</span>
                </a>
                <a href="{{ route('admin.pages.index') }}" class="btn btn-outline-secondary btn-icon">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Pages</span>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
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
                            $mediumImageUrl = $page->medium_url ?? asset('assets/images/placeholder.jpg');
                            $originalImageUrl = $page->image_url ?? asset('assets/images/placeholder.jpg');
                        @endphp
                        <img src="{{ $mediumImageUrl }}" 
                             alt="{{ $page->title }}" 
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

            <div class="modern-card modern-card--glass" style="margin-top: 1.5rem;">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-info-circle"></i>
                        Page Information
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
                                {{ $page->title }}
                            </div>
                        </div>
                        
                        @if($page->sub_title)
                        <div class="detail-item detail-item--animated" style="animation-delay: 0.2s;">
                            <div class="detail-item__label">
                                <i class="fas fa-text-height"></i>
                                Sub Title
                            </div>
                            <div class="detail-item__value">
                                {{ $page->sub_title }}
                            </div>
                        </div>
                        @endif
                        
                        <div class="detail-item detail-item--animated" style="animation-delay: 0.3s;">
                            <div class="detail-item__label">
                                <i class="fas fa-link"></i>
                                Slug
                            </div>
                            <div class="detail-item__value">
                                <code class="code-block">{{ $page->slug }}</code>
                            </div>
                        </div>
                        
                        <div class="detail-item detail-item--animated" style="animation-delay: 0.4s;">
                            <div class="detail-item__label">
                                <i class="fas fa-fingerprint"></i>
                                UUID
                            </div>
                            <div class="detail-item__value">
                                <code class="code-block code-block--small">{{ $page->uuid }}</code>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($page->content)
            <div class="modern-card modern-card--glass" style="margin-top: 1.5rem;">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-file-alt"></i>
                        Content
                    </h3>
                </div>
                <div class="modern-card__body">
                    <div class="page-content" style="line-height: 1.8; color: var(--text-primary);">
                        {!! $page->content !!}
                    </div>
                </div>
            </div>
            @endif
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Timestamps Card -->
            <div class="modern-card modern-card--glass">
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
                                    {{ $page->created_at->format('M d, Y') }}
                                    <small>{{ $page->created_at->format('g:i A') }}</small>
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
                                    {{ $page->updated_at->format('M d, Y') }}
                                    <small>{{ $page->updated_at->format('g:i A') }}</small>
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
                                    {{ $page->updated_at->diffForHumans() }}
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
                        <a href="{{ route('admin.pages.edit', $page) }}" class="action-list__item action-list__item--primary action-list__item--ripple">
                            <i class="fas fa-edit"></i>
                            <span>Edit Page</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        
                        <form method="POST" 
                              action="{{ route('admin.pages.destroy', $page) }}" 
                              class="action-list__form"
                              onsubmit="return confirm('Are you sure you want to delete this page? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-list__item action-list__item--danger action-list__item--ripple">
                                <i class="fas fa-trash"></i>
                                <span>Delete Page</span>
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

