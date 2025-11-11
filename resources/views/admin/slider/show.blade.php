@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-images"></i>
                    {{ $slider->heading }}
                </h1>
                <p class="page-header__subtitle">Slider details and information</p>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.sliders.edit', $slider) }}" class="btn btn-primary btn-icon">
                    <i class="fas fa-edit"></i>
                    <span>Edit</span>
                </a>
                <a href="{{ route('admin.sliders.index') }}" class="btn btn-outline-secondary btn-icon">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Sliders</span>
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
                        Slider Image
                    </h3>
                </div>
                <div class="modern-card__body">
                    <div class="category-image-large category-image-large--enhanced">
                        <img src="{{ $slider->image_url }}" 
                             alt="{{ $slider->heading }}" 
                             class="category-image-large__img">
                        <div class="category-image-large__overlay">
                            <a href="{{ $slider->image_url }}" target="_blank" class="category-image-large__zoom">
                                <i class="fas fa-search-plus"></i>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Preview with Content Overlay -->
                    <div class="position-relative" style="margin-top: 1rem; border-radius: 0.5rem; overflow: hidden;">
                        <div style="background-image: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url({{ $slider->image_url }}); background-size: cover; background-position: center; min-height: 200px; display: flex; align-items: center; justify-content: center; padding: 2rem;">
                            <div class="text-center text-white">
                                <h2 style="text-shadow: 2px 2px 4px rgba(0,0,0,0.5); margin-bottom: 0.5rem;">{{ $slider->heading }}</h2>
                                @if($slider->sub_heading)
                                    <p style="text-shadow: 1px 1px 2px rgba(0,0,0,0.5); margin-bottom: 1rem;">{{ $slider->sub_heading }}</p>
                                @endif
                                @if($slider->has_buttons)
                                    <div class="d-flex gap-2 justify-content-center flex-wrap">
                                        @foreach($slider->buttons as $index => $button)
                                            <a href="{{ $button['url'] }}" target="_blank" 
                                               class="btn {{ $index === 0 ? 'btn-primary' : 'btn-outline-light' }}">
                                                {{ $button['name'] }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modern-card modern-card--glass" style="margin-top: 1.5rem;">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-info-circle"></i>
                        Slider Information
                    </h3>
                </div>
                <div class="modern-card__body">
                    <div class="detail-grid detail-grid--enhanced">
                        <div class="detail-item detail-item--animated" style="animation-delay: 0.1s;">
                            <div class="detail-item__label">
                                <i class="fas fa-heading"></i>
                                Heading
                            </div>
                            <div class="detail-item__value">
                                {{ $slider->heading }}
                            </div>
                        </div>
                        
                        <div class="detail-item detail-item--animated" style="animation-delay: 0.2s;">
                            <div class="detail-item__label">
                                <i class="fas fa-text-height"></i>
                                Sub Heading
                            </div>
                            <div class="detail-item__value">
                                {{ $slider->sub_heading ?? 'Not provided' }}
                            </div>
                        </div>
                        
                        <div class="detail-item detail-item--animated" style="animation-delay: 0.3s;">
                            <div class="detail-item__label">
                                <i class="fas fa-toggle-on"></i>
                                Status
                            </div>
                            <div class="detail-item__value">
                                @if($slider->status == 1)
                                    <span class="badge badge--success badge--pulse">
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
                        
                        <div class="detail-item detail-item--animated" style="animation-delay: 0.4s;">
                            <div class="detail-item__label">
                                <i class="fas fa-sort-numeric-down"></i>
                                Sort Order
                            </div>
                            <div class="detail-item__value">
                                <span class="badge badge--secondary">{{ $slider->sort_order }}</span>
                            </div>
                        </div>
                        
                        <div class="detail-item detail-item--animated" style="animation-delay: 0.5s;">
                            <div class="detail-item__label">
                                <i class="fas fa-mouse-pointer"></i>
                                Buttons
                            </div>
                            <div class="detail-item__value">
                                @if($slider->has_buttons)
                                    <span class="badge badge--info">{{ $slider->button_count }} button(s)</span>
                                @else
                                    <span class="text-muted">No buttons</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="detail-item detail-item--animated" style="animation-delay: 0.6s;">
                            <div class="detail-item__label">
                                <i class="fas fa-fingerprint"></i>
                                UUID
                            </div>
                            <div class="detail-item__value">
                                <code class="code-block code-block--small">{{ $slider->uuid }}</code>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($slider->has_buttons)
            <div class="modern-card modern-card--glass" style="margin-top: 1.5rem;">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-mouse-pointer"></i>
                        Buttons ({{ $slider->button_count }})
                    </h3>
                </div>
                <div class="modern-card__body">
                    <div class="row">
                        @foreach($slider->buttons as $index => $button)
                            <div class="col-md-6" style="margin-bottom: 1rem;">
                                <div class="modern-card" style="padding: 1rem; background: linear-gradient(135deg, rgba(55, 78, 148, 0.05) 0%, rgba(128, 188, 192, 0.05) 100%);">
                                    <h6 style="margin-bottom: 0.75rem; color: var(--text-primary);">
                                        <i class="fas fa-{{ $index + 1 }}"></i> Button {{ $index + 1 }}
                                    </h6>
                                    <div style="margin-bottom: 0.5rem;">
                                        <strong style="color: var(--text-secondary); font-size: 0.875rem;">Name:</strong>
                                        <div>
                                            <span class="badge badge--info">{{ $button['name'] }}</span>
                                        </div>
                                    </div>
                                    <div style="margin-bottom: 0.75rem;">
                                        <strong style="color: var(--text-secondary); font-size: 0.875rem;">URL:</strong>
                                        <div>
                                            <a href="{{ $button['url'] }}" target="_blank" style="color: var(--primary-color); text-decoration: none; word-break: break-all;">
                                                {{ $button['url'] }} <i class="fas fa-external-link-alt" style="font-size: 0.75rem;"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div>
                                        <a href="{{ $button['url'] }}" target="_blank" 
                                           class="btn btn-sm {{ $index === 0 ? 'btn-primary' : 'btn-outline-primary' }} btn-ripple">
                                            <i class="fas fa-external-link-alt"></i>
                                            Test Button
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
                                    {{ $slider->created_at->format('M d, Y') }}
                                    <small>{{ $slider->created_at->format('g:i A') }}</small>
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
                                    {{ $slider->updated_at->format('M d, Y') }}
                                    <small>{{ $slider->updated_at->format('g:i A') }}</small>
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
                                    {{ $slider->updated_at->diffForHumans() }}
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
                        <a href="{{ route('admin.sliders.edit', $slider) }}" class="action-list__item action-list__item--primary action-list__item--ripple">
                            <i class="fas fa-edit"></i>
                            <span>Edit Slider</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        
                        <form method="POST" action="{{ route('admin.sliders.updateStatus', $slider) }}" class="action-list__form">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="{{ $slider->status == 1 ? '0' : '1' }}">
                            <button type="submit" class="action-list__item action-list__item--{{ $slider->status == 1 ? 'warning' : 'success' }} action-list__item--ripple">
                                <i class="fas fa-{{ $slider->status == 1 ? 'pause' : 'play' }}"></i>
                                <span>{{ $slider->status == 1 ? 'Deactivate' : 'Activate' }}</span>
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admin.sliders.moveUp', $slider) }}" class="action-list__form">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="action-list__item action-list__item--success action-list__item--ripple">
                                <i class="fas fa-arrow-up"></i>
                                <span>Move Up</span>
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admin.sliders.moveDown', $slider) }}" class="action-list__form">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="action-list__item action-list__item--warning action-list__item--ripple">
                                <i class="fas fa-arrow-down"></i>
                                <span>Move Down</span>
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </form>
                        
                        <form method="POST" action="{{ route('admin.sliders.duplicate', $slider) }}" class="action-list__form">
                            @csrf
                            <button type="submit" class="action-list__item action-list__item--info action-list__item--ripple">
                                <i class="fas fa-copy"></i>
                                <span>Duplicate Slider</span>
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </form>
                        
                        <form method="POST" 
                              action="{{ route('admin.sliders.destroy', $slider) }}" 
                              class="action-list__form"
                              onsubmit="return confirm('Are you sure you want to delete this slider? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-list__item action-list__item--danger action-list__item--ripple">
                                <i class="fas fa-trash"></i>
                                <span>Delete Slider</span>
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
