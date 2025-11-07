@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-tags"></i>
                    {{ $subcategory->name }}
                </h1>
                <p class="page-header__subtitle">Subcategory details and information</p>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.subcategories.edit', $subcategory) }}" class="btn btn-primary btn-icon">
                    <i class="fas fa-edit"></i>
                    <span>Edit</span>
                </a>
                <a href="{{ route('admin.subcategories.index') }}" class="btn btn-outline-secondary btn-icon">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Sub Categories</span>
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
                        Subcategory Information
                    </h3>
                </div>
                <div class="modern-card__body">
                    @if($subcategory->image)
                        <div class="category-image-large">
                            <img src="{{ $subcategory->image_url }}" 
                                 alt="{{ $subcategory->name }}" 
                                 class="category-image-large__img">
                        </div>
                    @endif
                    
                    <div class="detail-grid">
                        <div class="detail-item">
                            <div class="detail-item__label">
                                <i class="fas fa-tags"></i>
                                Name
                            </div>
                            <div class="detail-item__value">
                                {{ $subcategory->name }}
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-item__label">
                                <i class="fas fa-tag"></i>
                                Parent Category
                            </div>
                            <div class="detail-item__value">
                                <a href="{{ route('admin.categories.show', $subcategory->category) }}" class="badge badge--info">
                                    <i class="fas fa-tag"></i>
                                    {{ $subcategory->category->name }}
                                </a>
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-item__label">
                                <i class="fas fa-sitemap"></i>
                                Full Path
                            </div>
                            <div class="detail-item__value">
                                <span class="text-muted">{{ $subcategory->full_name }}</span>
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-item__label">
                                <i class="fas fa-link"></i>
                                Slug
                            </div>
                            <div class="detail-item__value">
                                <code class="code-block">{{ $subcategory->slug }}</code>
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-item__label">
                                <i class="fas fa-toggle-on"></i>
                                Status
                            </div>
                            <div class="detail-item__value">
                                @if($subcategory->status === '1')
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
                                <code class="code-block code-block--small">{{ $subcategory->uuid }}</code>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modern-card">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-sitemap"></i>
                        Category Hierarchy
                    </h3>
                </div>
                <div class="modern-card__body">
                    <div class="hierarchy-view">
                        <div class="hierarchy-item">
                            <div class="hierarchy-item__icon">
                                <i class="fas fa-tag"></i>
                            </div>
                            <div class="hierarchy-item__content">
                                <div class="hierarchy-item__label">Parent Category</div>
                                <div class="hierarchy-item__value">
                                    <a href="{{ route('admin.categories.show', $subcategory->category) }}">
                                        {{ $subcategory->category->name }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="hierarchy-arrow">
                            <i class="fas fa-arrow-down"></i>
                        </div>
                        <div class="hierarchy-item hierarchy-item--active">
                            <div class="hierarchy-item__icon">
                                <i class="fas fa-tags"></i>
                            </div>
                            <div class="hierarchy-item__content">
                                <div class="hierarchy-item__label">Subcategory</div>
                                <div class="hierarchy-item__value">{{ $subcategory->name }}</div>
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
                                    {{ $subcategory->created_at->format('M d, Y') }}
                                    <small>{{ $subcategory->created_at->format('g:i A') }}</small>
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
                                    {{ $subcategory->updated_at->format('M d, Y') }}
                                    <small>{{ $subcategory->updated_at->format('g:i A') }}</small>
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
                                    {{ $subcategory->updated_at->diffForHumans() }}
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
                        <a href="{{ route('admin.subcategories.edit', $subcategory) }}" class="action-list__item action-list__item--primary">
                            <i class="fas fa-edit"></i>
                            <span>Edit Subcategory</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        
                        <a href="{{ route('admin.categories.show', $subcategory->category) }}" class="action-list__item action-list__item--info">
                            <i class="fas fa-tag"></i>
                            <span>View Parent Category</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        
                        <form method="POST" action="{{ route('admin.subcategories.updateStatus', $subcategory) }}" class="action-list__form">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="{{ $subcategory->status === '1' ? '0' : '1' }}">
                            <button type="submit" class="action-list__item action-list__item--{{ $subcategory->status === '1' ? 'warning' : 'success' }}">
                                <i class="fas fa-{{ $subcategory->status === '1' ? 'pause' : 'play' }}"></i>
                                <span>{{ $subcategory->status === '1' ? 'Deactivate' : 'Activate' }}</span>
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </form>
                        
                        <form method="POST" 
                              action="{{ route('admin.subcategories.destroy', $subcategory) }}" 
                              class="action-list__form"
                              onsubmit="return confirm('Are you sure you want to delete this subcategory? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-list__item action-list__item--danger">
                                <i class="fas fa-trash"></i>
                                <span>Delete Subcategory</span>
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Image Card (if no image) -->
            @if(!$subcategory->image)
                <div class="modern-card modern-card--empty">
                    <div class="modern-card__body">
                        <div class="empty-state empty-state--compact">
                            <div class="empty-state__icon">
                                <i class="fas fa-image"></i>
                            </div>
                            <h4 class="empty-state__title">No Image</h4>
                            <p class="empty-state__text">Add an image to make this subcategory more visually appealing</p>
                            <a href="{{ route('admin.subcategories.edit', $subcategory) }}" class="btn btn-outline-primary btn-sm">
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
