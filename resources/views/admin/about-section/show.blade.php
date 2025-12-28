@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-eye"></i>
                    View About Section
                </h1>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.about-sections.edit', $aboutSection) }}" class="btn btn-primary btn-icon">
                    <i class="fas fa-edit"></i>
                    <span>Edit</span>
                </a>
                <a href="{{ route('admin.about-sections.index') }}" class="btn btn-outline-secondary btn-icon">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back</span>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="modern-card modern-card--compact">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">About Section Details</h3>
                </div>
                <div class="modern-card__body">
                    @if($aboutSection->image_url)
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Image:</strong>
                        </div>
                        <div class="col-md-8">
                            <img src="{{ $aboutSection->image_url }}" alt="{{ $aboutSection->title }}" 
                                 style="max-width: 100%; height: auto;">
                        </div>
                    </div>
                    @endif

                    @if($aboutSection->badge)
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Badge:</strong>
                        </div>
                        <div class="col-md-8">
                            <span class="badge bg-secondary">{{ $aboutSection->badge }}</span>
                        </div>
                    </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Title:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $aboutSection->title }}
                        </div>
                    </div>

                    @if($aboutSection->description)
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Description:</strong>
                        </div>
                        <div class="col-md-8">
                            <p>{{ $aboutSection->description }}</p>
                        </div>
                    </div>
                    @endif

                    @if($aboutSection->button_text)
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Button:</strong>
                        </div>
                        <div class="col-md-8">
                            <span class="badge bg-info">{{ $aboutSection->button_text }}</span>
                            @if($aboutSection->button_link)
                                <br><small><a href="{{ $aboutSection->button_link }}" target="_blank">{{ $aboutSection->button_link }}</a></small>
                            @endif
                        </div>
                    </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Status:</strong>
                        </div>
                        <div class="col-md-8">
                            @if($aboutSection->status)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Sort Order:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $aboutSection->sort_order ?? 0 }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Created At:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $aboutSection->created_at->format('M d, Y h:i A') }}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <strong>Updated At:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $aboutSection->updated_at->format('M d, Y h:i A') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

