@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-edit"></i>
                    Edit Testimonial
                </h1>
            </div>
            <div class="page-header__actions">
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
                    <h3 class="modern-card__title">Testimonial Information</h3>
                </div>
                <div class="modern-card__body">
                    <form method="POST" action="{{ route('admin.testimonials.update', $testimonial) }}" class="modern-form" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group-modern">
                            <label for="name" class="form-label-modern">Name <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-user input-icon"></i>
                                <input type="text" 
                                       class="form-input-modern @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $testimonial->name) }}" 
                                       placeholder="Enter name"
                                       required>
                            </div>
                            @error('name')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label for="email" class="form-label-modern">Email</label>
                            <div class="input-wrapper">
                                <i class="fas fa-envelope input-icon"></i>
                                <input type="email" 
                                       class="form-input-modern @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email', $testimonial->email) }}" 
                                       placeholder="Enter email">
                            </div>
                            @error('email')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label for="designation" class="form-label-modern">Designation</label>
                            <div class="input-wrapper">
                                <i class="fas fa-briefcase input-icon"></i>
                                <input type="text" 
                                       class="form-input-modern @error('designation') is-invalid @enderror" 
                                       id="designation" 
                                       name="designation" 
                                       value="{{ old('designation', $testimonial->designation) }}" 
                                       placeholder="Enter designation">
                            </div>
                            @error('designation')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label for="review" class="form-label-modern">Review <span class="required">*</span></label>
                            <textarea class="form-input-modern @error('review') is-invalid @enderror" 
                                      id="review" 
                                      name="review" 
                                      rows="5"
                                      placeholder="Enter review" required>{{ old('review', $testimonial->review) }}</textarea>
                            @error('review')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label for="rating" class="form-label-modern">Rating <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-star input-icon"></i>
                                <select class="form-input-modern @error('rating') is-invalid @enderror" 
                                        id="rating" name="rating" required>
                                    <option value="">Select Rating</option>
                                    @for($i = 1; $i <= 5; $i++)
                                        <option value="{{ $i }}" {{ old('rating', $testimonial->rating) == $i ? 'selected' : '' }}>
                                            {{ $i }} Star{{ $i > 1 ? 's' : '' }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            @error('rating')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label for="image" class="form-label-modern">Image</label>
                            @if($testimonial->image)
                                <div class="mb-2">
                                    <img src="{{ $testimonial->image_url }}" alt="{{ $testimonial->name }}" 
                                         style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%;">
                                </div>
                            @endif
                            <input type="file" 
                                   class="form-input-modern @error('image') is-invalid @enderror" 
                                   id="image" 
                                   name="image" 
                                   accept="image/*">
                            <small class="form-text text-muted">Max size: 2MB. Formats: JPEG, PNG, JPG, GIF</small>
                            @error('image')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label for="sort_order" class="form-label-modern">Sort Order</label>
                            <div class="input-wrapper">
                                <i class="fas fa-sort input-icon"></i>
                                <input type="number" 
                                       class="form-input-modern @error('sort_order') is-invalid @enderror" 
                                       id="sort_order" 
                                       name="sort_order" 
                                       value="{{ old('sort_order', $testimonial->sort_order ?? 0) }}" 
                                       min="0">
                            </div>
                            @error('sort_order')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label for="status" class="form-label-modern">Status <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-toggle-on input-icon"></i>
                                <select class="form-input-modern @error('status') is-invalid @enderror" 
                                        id="status" name="status" required>
                                    <option value="1" {{ old('status', $testimonial->status) == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status', $testimonial->status) == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            @error('status')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                Update Testimonial
                            </button>
                            <a href="{{ route('admin.testimonials.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i>
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

