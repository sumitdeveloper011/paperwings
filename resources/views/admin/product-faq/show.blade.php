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
                        <strong>Category:</strong>
                        <span>{{ $productFaq->category->name ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <strong>Total FAQs:</strong>
                        <span class="badge bg-primary">{{ count($productFaq->faqs ?? []) }} FAQ(s)</span>
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

            <!-- FAQs List -->
            @if(!empty($productFaq->faqs) && count($productFaq->faqs) > 0)
                <div class="modern-card mt-4">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-question-circle"></i>
                            FAQs ({{ count($productFaq->faqs) }})
                        </h3>
                    </div>
                    <div class="modern-card__body">
                        @foreach($productFaq->faqs as $index => $faq)
                            <div class="faq-item-detail mb-4 pb-4 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <span class="badge bg-secondary me-2">FAQ #{{ $index + 1 }}</span>
                                        <span class="badge {{ $faq['status'] ?? true ? 'bg-success' : 'bg-secondary' }}">
                                            {{ ($faq['status'] ?? true) ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                    @if(isset($faq['sort_order']))
                                        <small class="text-muted">Sort Order: {{ $faq['sort_order'] }}</small>
                                    @endif
                                </div>
                                <div class="mb-2">
                                    <strong class="text-primary">
                                        <i class="fas fa-question-circle"></i> Question:
                                    </strong>
                                    <p class="mb-0 mt-1">{{ $faq['question'] ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <strong class="text-success">
                                        <i class="fas fa-check-circle"></i> Answer:
                                    </strong>
                                    <div class="mt-1 p-3 bg-light rounded">
                                        {!! nl2br(e($faq['answer'] ?? 'N/A')) !!}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="modern-card mt-4">
                    <div class="modern-card__body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            No FAQs found for this product.
                        </div>
                    </div>
                </div>
            @endif
            </div>
        </div>
    </div>
</div>
@endsection

