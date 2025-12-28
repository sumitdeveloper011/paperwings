@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-tag"></i>
                    Tag Details
                </h1>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.tags.edit', $tag) }}" class="btn btn-primary btn-icon">
                    <i class="fas fa-edit"></i>
                    <span>Edit</span>
                </a>
                <a href="{{ route('admin.tags.index') }}" class="btn btn-outline-secondary btn-icon">
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
                    <h3 class="modern-card__title">Tag Information</h3>
                </div>
                <div class="modern-card__body">
                    <div class="info-row">
                        <strong>Name:</strong>
                        <span>{{ $tag->name }}</span>
                    </div>
                    <div class="info-row">
                        <strong>Slug:</strong>
                        <code>{{ $tag->slug }}</code>
                    </div>
                    <div class="info-row">
                        <strong>Products Count:</strong>
                        <span class="badge bg-primary">{{ $tag->products_count }}</span>
                    </div>
                    <div class="info-row">
                        <strong>Created:</strong>
                        <span>{{ $tag->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                    <div class="info-row">
                        <strong>Updated:</strong>
                        <span>{{ $tag->updated_at->format('M d, Y h:i A') }}</span>
                    </div>
                </div>
            </div>

            @if($tag->products->count() > 0)
            <div class="modern-card mt-4">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">Products with this Tag</h3>
                </div>
                <div class="modern-card__body">
                    <div class="row">
                        @foreach($tag->products->take(20) as $product)
                            <div class="col-md-3 mb-3">
                                <div class="product-card">
                                    <img src="{{ $product->main_image }}" alt="{{ $product->name }}" 
                                         class="product-card__img" 
                                         onerror="this.src='{{ asset('assets/images/placeholder.jpg') }}'">
                                    <div class="product-card__name">{{ Str::limit($product->name, 30) }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if($tag->products->count() > 20)
                        <p class="text-muted">Showing first 20 of {{ $tag->products->count() }} products</p>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

