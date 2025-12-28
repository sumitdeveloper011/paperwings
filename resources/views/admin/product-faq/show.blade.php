@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-question"></i>
                    Product FAQ Details
                </h1>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.product-faqs.edit', $productFaq) }}" class="btn btn-primary btn-icon">
                    <i class="fas fa-edit"></i>
                    <span>Edit</span>
                </a>
                <a href="{{ route('admin.product-faqs.index') }}" class="btn btn-outline-secondary btn-icon">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back</span>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="modern-card">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">FAQ Information</h3>
                </div>
                <div class="modern-card__body">
                    <div class="info-row">
                        <strong>Product:</strong>
                        <span>{{ $productFaq->product->name ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <strong>Question:</strong>
                        <span>{{ $productFaq->question }}</span>
                    </div>
                    <div class="info-row">
                        <strong>Answer:</strong>
                        <div class="mt-2">{!! nl2br(e($productFaq->answer)) !!}</div>
                    </div>
                    <div class="info-row">
                        <strong>Status:</strong>
                        <span class="badge {{ $productFaq->status ? 'bg-success' : 'bg-secondary' }}">
                            {{ $productFaq->status ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div class="info-row">
                        <strong>Sort Order:</strong>
                        <span>{{ $productFaq->sort_order ?? 0 }}</span>
                    </div>
                    <div class="info-row">
                        <strong>Created:</strong>
                        <span>{{ $productFaq->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                    <div class="info-row">
                        <strong>Updated:</strong>
                        <span>{{ $productFaq->updated_at->format('M d, Y h:i A') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

