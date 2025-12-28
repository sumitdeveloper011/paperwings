@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-eye"></i>
                    View Special Offers Banner
                </h1>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.special-offers-banners.edit', $specialOffersBanner) }}" class="btn btn-primary btn-icon">
                    <i class="fas fa-edit"></i>
                    <span>Edit</span>
                </a>
                <a href="{{ route('admin.special-offers-banners.index') }}" class="btn btn-outline-secondary btn-icon">
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
                    <h3 class="modern-card__title">Banner Details</h3>
                </div>
                <div class="modern-card__body">
                    @if($specialOffersBanner->image_url)
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Image:</strong>
                        </div>
                        <div class="col-md-8">
                            <img src="{{ $specialOffersBanner->image_url }}" alt="{{ $specialOffersBanner->title }}" 
                                 style="max-width: 100%; height: auto;">
                        </div>
                    </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Title:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $specialOffersBanner->title }}
                        </div>
                    </div>

                    @if($specialOffersBanner->description)
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Description:</strong>
                        </div>
                        <div class="col-md-8">
                            <p>{{ $specialOffersBanner->description }}</p>
                        </div>
                    </div>
                    @endif

                    @if($specialOffersBanner->button_text)
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Button:</strong>
                        </div>
                        <div class="col-md-8">
                            <span class="badge bg-info">{{ $specialOffersBanner->button_text }}</span>
                            @if($specialOffersBanner->button_link)
                                <br><small><a href="{{ $specialOffersBanner->button_link }}" target="_blank">{{ $specialOffersBanner->button_link }}</a></small>
                            @endif
                        </div>
                    </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Date Range:</strong>
                        </div>
                        <div class="col-md-8">
                            <small>
                                <strong>Start:</strong> 
                                @if($specialOffersBanner->start_date)
                                    {{ $specialOffersBanner->start_date->format('M d, Y h:i A') }}
                                @else
                                    <span class="text-muted">Not set</span>
                                @endif
                                <br>
                                <strong>End:</strong> 
                                @if($specialOffersBanner->end_date)
                                    {{ $specialOffersBanner->end_date->format('M d, Y h:i A') }}
                                @else
                                    <span class="text-muted">Not set</span>
                                @endif
                            </small>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Show Countdown:</strong>
                        </div>
                        <div class="col-md-8">
                            @if($specialOffersBanner->show_countdown)
                                <span class="badge bg-success">Yes</span>
                            @else
                                <span class="badge bg-secondary">No</span>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Status:</strong>
                        </div>
                        <div class="col-md-8">
                            @if($specialOffersBanner->status)
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
                            {{ $specialOffersBanner->sort_order ?? 0 }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Created At:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $specialOffersBanner->created_at->format('M d, Y h:i A') }}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <strong>Updated At:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $specialOffersBanner->updated_at->format('M d, Y h:i A') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

