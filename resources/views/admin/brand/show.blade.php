@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-award"></i>
                    {{ $brand->name }}
                </h1>
                <p class="page-header__subtitle">Brand details and information</p>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.brands.edit', $brand) }}" class="btn btn-primary btn-icon">
                    <i class="fas fa-edit"></i>
                    <span>Edit</span>
                </a>
                <a href="{{ route('admin.brands.index') }}" class="btn btn-outline-secondary btn-icon">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Brands</span>
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
                        Brand Information
                    </h3>
                </div>
                <div class="modern-card__body">
                    @if($brand->image)
                        <div class="category-image-large">
                            <img src="{{ $brand->image_url }}" 
                                 alt="{{ $brand->name }}" 
                                 class="category-image-large__img">
                        </div>
                    @endif
                    
                    <div class="detail-grid">
                        <div class="detail-item">
                            <div class="detail-item__label">
                                <i class="fas fa-award"></i>
                                Name
                            </div>
                            <div class="detail-item__value">
                                {{ $brand->name }}
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-item__label">
                                <i class="fas fa-link"></i>
                                Slug
                            </div>
                            <div class="detail-item__value">
                                <code class="code-block">{{ $brand->slug }}</code>
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-item__label">
                                <i class="fas fa-fingerprint"></i>
                                UUID
                            </div>
                            <div class="detail-item__value">
                                <code class="code-block code-block--small">{{ $brand->uuid }}</code>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
                                    {{ $brand->created_at->format('M d, Y') }}
                                    <small>{{ $brand->created_at->format('g:i A') }}</small>
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
                                    {{ $brand->updated_at->format('M d, Y') }}
                                    <small>{{ $brand->updated_at->format('g:i A') }}</small>
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
                                    {{ $brand->updated_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions Card -->
            <div class="modern-card">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-bolt"></i>
                        Quick Actions
                    </h3>
                </div>
                <div class="modern-card__body">
                    <div class="action-list">
                        <a href="{{ route('admin.brands.edit', $brand) }}" class="action-list__item action-list__item--primary">
                            <i class="fas fa-edit"></i>
                            <span>Edit Brand</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        
                        <form method="POST" 
                              action="{{ route('admin.brands.destroy', $brand) }}" 
                              class="action-list__form"
                              onsubmit="return confirm('Are you sure you want to delete this brand? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-list__item action-list__item--danger">
                                <i class="fas fa-trash"></i>
                                <span>Delete Brand</span>
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Image Card (if no image) -->
            @if(!$brand->image)
                <div class="modern-card modern-card--empty">
                    <div class="modern-card__body">
                        <div class="empty-state empty-state--compact">
                            <div class="empty-state__icon">
                                <i class="fas fa-image"></i>
                            </div>
                            <h4 class="empty-state__title">No Logo</h4>
                            <p class="empty-state__text">Add a logo to make this brand more recognizable</p>
                            <a href="{{ route('admin.brands.edit', $brand) }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-plus"></i>
                                Add Logo
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
