@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-eye"></i>
                    View FAQ
                </h1>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.faqs.edit', $faq) }}" class="btn btn-primary btn-icon">
                    <i class="fas fa-edit"></i>
                    <span>Edit</span>
                </a>
                <a href="{{ route('admin.faqs.index') }}" class="btn btn-outline-secondary btn-icon">
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
                    <h3 class="modern-card__title">FAQ Details</h3>
                </div>
                <div class="modern-card__body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Question:</strong>
                        </div>
                        <div class="col-md-8">
                            <h4>{{ $faq->question }}</h4>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Answer:</strong>
                        </div>
                        <div class="col-md-8">
                            <p>{{ $faq->answer }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Category:</strong>
                        </div>
                        <div class="col-md-8">
                            @if($faq->category)
                                <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $faq->category)) }}</span>
                            @else
                                <span class="text-muted">General</span>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Status:</strong>
                        </div>
                        <div class="col-md-8">
                            @if($faq->status)
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
                            {{ $faq->sort_order ?? 0 }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Created At:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $faq->created_at->format('M d, Y h:i A') }}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <strong>Updated At:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $faq->updated_at->format('M d, Y h:i A') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

