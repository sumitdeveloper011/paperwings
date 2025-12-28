@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-boxes"></i>
                    Bundle Details
                </h1>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.bundles.edit', $bundle) }}" class="btn btn-primary btn-icon">
                    <i class="fas fa-edit"></i>
                    <span>Edit</span>
                </a>
                <a href="{{ route('admin.bundles.index') }}" class="btn btn-outline-secondary btn-icon">
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
                    <h3 class="modern-card__title">Bundle Information</h3>
                </div>
                <div class="modern-card__body">
                    @if($bundle->image)
                        <div class="mb-3">
                            <img src="{{ asset('storage/' . $bundle->image) }}" 
                                 alt="{{ $bundle->name }}" 
                                 style="max-width: 200px; border-radius: 5px;">
                        </div>
                    @endif
                    <div class="info-row">
                        <strong>Name:</strong>
                        <span>{{ $bundle->name }}</span>
                    </div>
                    <div class="info-row">
                        <strong>Slug:</strong>
                        <code>{{ $bundle->slug }}</code>
                    </div>
                    @if($bundle->description)
                    <div class="info-row">
                        <strong>Description:</strong>
                        <div class="mt-2">{!! nl2br(e($bundle->description)) !!}</div>
                    </div>
                    @endif
                    <div class="info-row">
                        <strong>Bundle Price:</strong>
                        <span class="text-success"><strong>${{ number_format($bundle->bundle_price, 2) }}</strong></span>
                    </div>
                    @if($bundle->discount_percentage)
                    <div class="info-row">
                        <strong>Discount:</strong>
                        <span class="badge bg-success">{{ $bundle->discount_percentage }}%</span>
                    </div>
                    @endif
                    <div class="info-row">
                        <strong>Status:</strong>
                        <span class="badge {{ $bundle->status ? 'bg-success' : 'bg-secondary' }}">
                            {{ $bundle->status ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div class="info-row">
                        <strong>Sort Order:</strong>
                        <span>{{ $bundle->sort_order ?? 0 }}</span>
                    </div>
                </div>
            </div>

            @if($bundle->products->count() > 0)
            <div class="modern-card mt-4">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">Products in Bundle ({{ $bundle->products->count() }})</h3>
                </div>
                <div class="modern-card__body">
                    <div class="row">
                        @foreach($bundle->products as $product)
                            <div class="col-md-4 mb-3">
                                <div class="product-card p-3" style="border: 1px solid #ddd; border-radius: 5px;">
                                    <img src="{{ $product->main_image }}" 
                                         alt="{{ $product->name }}" 
                                         style="width: 100%; height: 150px; object-fit: cover; border-radius: 5px; margin-bottom: 10px;"
                                         onerror="this.src='{{ asset('assets/images/placeholder.jpg') }}'">
                                    <div class="product-card__name"><strong>{{ $product->name }}</strong></div>
                                    <div class="text-muted">Quantity: {{ $product->pivot->quantity ?? 1 }}</div>
                                    <div class="text-success">${{ number_format($product->total_price, 2) }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

