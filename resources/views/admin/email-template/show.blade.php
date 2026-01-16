@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-envelope"></i>
                    {{ $emailTemplate->name }}
                </h1>
                <p class="page-header__subtitle">Email template details</p>
            </div>
            <div class="page-header__actions">
                @can('email-templates.edit')
                <a href="{{ route('admin.email-templates.edit', $emailTemplate) }}" class="btn btn-primary btn-icon">
                    <i class="fas fa-edit"></i>
                    <span>Edit</span>
                </a>
                @endcan
                <a href="{{ route('admin.email-templates.index') }}" class="btn btn-outline-secondary btn-icon">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Templates</span>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="modern-card modern-card--glass">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-info-circle"></i>
                        Template Information
                    </h3>
                </div>
                <div class="modern-card__body">
                    <div class="detail-grid detail-grid--enhanced">
                        <div class="detail-item detail-item--animated" style="animation-delay: 0.1s;">
                            <div class="detail-item__label">
                                <i class="fas fa-heading"></i>
                                Name
                            </div>
                            <div class="detail-item__value">
                                {{ $emailTemplate->name }}
                            </div>
                        </div>
                        
                        <div class="detail-item detail-item--animated" style="animation-delay: 0.2s;">
                            <div class="detail-item__label">
                                <i class="fas fa-link"></i>
                                Slug
                            </div>
                            <div class="detail-item__value">
                                <code class="code-block">{{ $emailTemplate->slug }}</code>
                            </div>
                        </div>

                        <div class="detail-item detail-item--animated" style="animation-delay: 0.3s;">
                            <div class="detail-item__label">
                                <i class="fas fa-folder"></i>
                                Category
                            </div>
                            <div class="detail-item__value">
                                <span class="badge badge-{{ $emailTemplate->category === 'system' ? 'danger' : ($emailTemplate->category === 'order' ? 'primary' : ($emailTemplate->category === 'user' ? 'info' : 'warning')) }}">
                                    {{ ucfirst($emailTemplate->category) }}
                                </span>
                            </div>
                        </div>

                        <div class="detail-item detail-item--animated" style="animation-delay: 0.4s;">
                            <div class="detail-item__label">
                                <i class="fas fa-envelope"></i>
                                Subject
                            </div>
                            <div class="detail-item__value">
                                {{ $emailTemplate->subject }}
                            </div>
                        </div>

                        @if($emailTemplate->description)
                        <div class="detail-item detail-item--animated" style="animation-delay: 0.5s;">
                            <div class="detail-item__label">
                                <i class="fas fa-align-left"></i>
                                Description
                            </div>
                            <div class="detail-item__value">
                                {{ $emailTemplate->description }}
                            </div>
                        </div>
                        @endif

                        <div class="detail-item detail-item--animated" style="animation-delay: 0.6s;">
                            <div class="detail-item__label">
                                <i class="fas fa-toggle-on"></i>
                                Status
                            </div>
                            <div class="detail-item__value">
                                <span class="badge badge-{{ $emailTemplate->is_active ? 'success' : 'secondary' }}">
                                    {{ $emailTemplate->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modern-card modern-card--glass" style="margin-top: 1.5rem;">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-file-alt"></i>
                        Email Body Preview
                    </h3>
                </div>
                <div class="modern-card__body">
                    <div class="email-preview" style="border: 1px solid #e0e0e0; padding: 20px; background: #fff;">
                        {!! $emailTemplate->body !!}
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
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
                                    {{ $emailTemplate->created_at->format('M d, Y') }}
                                    <small>{{ $emailTemplate->created_at->format('g:i A') }}</small>
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
                                    {{ $emailTemplate->updated_at->format('M d, Y') }}
                                    <small>{{ $emailTemplate->updated_at->format('g:i A') }}</small>
                                </div>
                            </div>
                        </div>

                        <div class="timestamp-item timestamp-item--animated" style="animation-delay: 0.3s;">
                            <div class="timestamp-item__icon">
                                <i class="fas fa-code-branch"></i>
                            </div>
                            <div class="timestamp-item__content">
                                <div class="timestamp-item__label">Version</div>
                                <div class="timestamp-item__value">
                                    v{{ $emailTemplate->version }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @can('email-templates.edit')
            <div class="modern-card modern-card--glass" style="margin-top: 1.5rem;">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-bolt"></i>
                        Quick Actions
                    </h3>
                </div>
                <div class="modern-card__body">
                    <div class="action-list action-list--enhanced">
                        <a href="{{ route('admin.email-templates.edit', $emailTemplate) }}" class="action-list__item action-list__item--primary action-list__item--ripple">
                            <i class="fas fa-edit"></i>
                            <span>Edit Template</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        
                        <form method="POST" action="{{ route('admin.email-templates.duplicate', $emailTemplate) }}" class="action-list__form">
                            @csrf
                            <button type="submit" class="action-list__item action-list__item--info action-list__item--ripple">
                                <i class="fas fa-copy"></i>
                                <span>Duplicate Template</span>
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </form>

                        @if($emailTemplate->category !== 'system')
                        <form method="POST" 
                              action="{{ route('admin.email-templates.destroy', $emailTemplate) }}" 
                              class="action-list__form"
                              onsubmit="return confirm('Are you sure you want to delete this template?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-list__item action-list__item--danger action-list__item--ripple">
                                <i class="fas fa-trash"></i>
                                <span>Delete Template</span>
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            @endcan
        </div>
    </div>
</div>
@endsection
