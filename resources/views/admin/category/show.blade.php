@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-tag"></i>
                    {{ $category->name }}
                </h1>
                <p class="page-header__subtitle">Category details and information</p>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-primary btn-icon">
                    <i class="fas fa-edit"></i>
                    <span>Edit</span>
                </a>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary btn-icon">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Categories</span>
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
                        Category Information
                    </h3>
                </div>
                <div class="modern-card__body">
                    @if($category->image)
                        <div class="category-image-large">
                            <img src="{{ $category->image_url }}" 
                                 alt="{{ $category->name }}" 
                                 class="category-image-large__img">
                        </div>
                    @endif
                    
                    <div class="detail-grid">
                        <div class="detail-item">
                            <div class="detail-item__label">
                                <i class="fas fa-tag"></i>
                                Name
                            </div>
                            <div class="detail-item__value">
                                {{ $category->name }}
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-item__label">
                                <i class="fas fa-link"></i>
                                Slug
                            </div>
                            <div class="detail-item__value">
                                <code class="code-block">{{ $category->slug }}</code>
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-item__label">
                                <i class="fas fa-toggle-on"></i>
                                Status
                            </div>
                            <div class="detail-item__value">
                                @if((int)$category->status === 1)
                                    <span class="badge badge--success">
                                        <i class="fas fa-check-circle"></i>
                                        Active
                                    </span>
                                @else
                                    <span class="badge badge--danger">
                                        <i class="fas fa-times-circle"></i>
                                        Inactive
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-item__label">
                                <i class="fas fa-fingerprint"></i>
                                UUID
                            </div>
                            <div class="detail-item__value">
                                <code class="code-block code-block--small">{{ $category->uuid }}</code>
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
                                    {{ $category->created_at->format('M d, Y') }}
                                    <small>{{ $category->created_at->format('g:i A') }}</small>
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
                                    {{ $category->updated_at->format('M d, Y') }}
                                    <small>{{ $category->updated_at->format('g:i A') }}</small>
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
                                    {{ $category->updated_at->diffForHumans() }}
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
                        <div class="action-list__item action-list__item--{{ (int)$category->status === 1 ? 'success' : 'secondary' }}" style="cursor: default;">
                            <i class="fas fa-{{ (int)$category->status === 1 ? 'check-circle' : 'times-circle' }}"></i>
                            <span>{{ (int)$category->status === 1 ? 'Active' : 'Inactive' }}</span>
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
                        <a href="{{ route('admin.categories.edit', $category) }}" class="action-list__item action-list__item--primary">
                            <i class="fas fa-edit"></i>
                            <span>Edit Category</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        
                        <form method="POST" 
                              action="{{ route('admin.categories.destroy', $category) }}" 
                              class="action-list__form"
                              onsubmit="return confirm('Are you sure you want to delete this category? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-list__item action-list__item--danger">
                                <i class="fas fa-trash"></i>
                                <span>Delete Category</span>
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Image Card (if no image) -->
            @if(!$category->image)
                <div class="modern-card modern-card--empty">
                    <div class="modern-card__body">
                        <div class="empty-state empty-state--compact">
                            <div class="empty-state__icon">
                                <i class="fas fa-image"></i>
                            </div>
                            <h4 class="empty-state__title">No Image</h4>
                            <p class="empty-state__text">Add an image to make this category more visually appealing</p>
                            <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-outline-primary btn-sm">
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
@endsection
