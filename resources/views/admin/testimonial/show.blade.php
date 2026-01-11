@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-eye"></i>
                    View Testimonial
                </h1>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.testimonials.edit', $testimonial) }}" class="btn btn-primary btn-icon">
                    <i class="fas fa-edit"></i>
                    <span>Edit</span>
                </a>
                <a href="{{ route('admin.testimonials.index') }}" class="btn btn-outline-secondary btn-icon">
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
                    <h3 class="modern-card__title">Testimonial Details</h3>
                </div>
                <div class="modern-card__body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Image:</strong>
                        </div>
                        <div class="col-md-8">
                            <img src="{{ $testimonial->image ? asset('storage/' . $testimonial->image) : asset('assets/images/profile.png') }}"
                                 alt="{{ $testimonial->name }}"
                                 style="width: 120px; height: 120px; object-fit: cover; border-radius: 50%;"
                                 onerror="this.src='{{ asset('assets/images/profile.png') }}'">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Name:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $testimonial->name }}
                        </div>
                    </div>

                    @if($testimonial->email)
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Email:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $testimonial->email }}
                        </div>
                    </div>
                    @endif

                    @if($testimonial->designation)
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Designation:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $testimonial->designation }}
                        </div>
                    </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Rating:</strong>
                        </div>
                        <div class="col-md-8">
                            <div class="star-rating">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $testimonial->rating ? 'text-warning' : 'text-muted' }}"></i>
                                @endfor
                                <span class="ms-2">({{ $testimonial->rating }}/5)</span>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Review:</strong>
                        </div>
                        <div class="col-md-8">
                            <p>{{ $testimonial->review }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Status:</strong>
                        </div>
                        <div class="col-md-8">
                            @if($testimonial->status)
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
                            {{ $testimonial->sort_order ?? 0 }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Created At:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $testimonial->created_at->format('M d, Y h:i A') }}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <strong>Updated At:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $testimonial->updated_at->format('M d, Y h:i A') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

