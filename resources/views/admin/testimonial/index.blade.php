@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-star"></i>
                    Testimonials
                </h1>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.testimonials.create') }}" class="btn btn-primary btn-icon">
                    <i class="fas fa-plus"></i>
                    <span>Add Testimonial</span>
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Main Content Card -->
    <div class="modern-card modern-card--compact">
        <div class="modern-card__header">
            <div class="modern-card__header-content">
                <h3 class="modern-card__title">
                    <i class="fas fa-list"></i>
                    All Testimonials
                </h3>
                <p class="modern-card__subtitle">{{ $testimonials->total() }} total testimonials</p>
            </div>
            <div class="modern-card__header-actions">
                <form method="GET" class="search-form">
                    <div class="search-form__wrapper">
                        <i class="fas fa-search search-form__icon"></i>
                        <input type="text" name="search" class="search-form__input"
                               placeholder="Search testimonials..." value="{{ $search }}">
                        @if($search)
                            <a href="{{ route('admin.testimonials.index') }}" class="search-form__clear">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <div class="modern-card__body">
            @if($testimonials->count() > 0)
                <div class="modern-table-wrapper">
                    <table class="modern-table">
                        <thead class="modern-table__head">
                            <tr>
                                <th class="modern-table__th">Image</th>
                                <th class="modern-table__th">Name</th>
                                <th class="modern-table__th">Email</th>
                                <th class="modern-table__th">Review</th>
                                <th class="modern-table__th">Rating</th>
                                <th class="modern-table__th">Status</th>
                                <th class="modern-table__th">Sort Order</th>
                                <th class="modern-table__th modern-table__th--actions">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="modern-table__body">
                            @foreach($testimonials as $testimonial)
                                <tr class="modern-table__row">
                                    <td class="modern-table__td">
                                        <img src="{{ $testimonial->image_url }}" alt="{{ $testimonial->name }}" 
                                             class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">
                                    </td>
                                    <td class="modern-table__td">
                                        <strong>{{ $testimonial->name }}</strong>
                                        @if($testimonial->designation)
                                            <br><small class="text-muted">{{ $testimonial->designation }}</small>
                                        @endif
                                    </td>
                                    <td class="modern-table__td">{{ $testimonial->email }}</td>
                                    <td class="modern-table__td">
                                        <div class="text-truncate" style="max-width: 250px;" title="{{ $testimonial->review }}">
                                            {{ Str::limit($testimonial->review, 50) }}
                                        </div>
                                    </td>
                                    <td class="modern-table__td">
                                        <div class="star-rating">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= $testimonial->rating ? 'text-warning' : 'text-muted' }}"></i>
                                            @endfor
                                        </div>
                                    </td>
                                    <td class="modern-table__td">
                                        <form method="POST" action="{{ route('admin.testimonials.updateStatus', $testimonial) }}" class="status-form">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" class="status-select" onchange="this.form.submit()">
                                                <option value="1" {{ $testimonial->status ? 'selected' : '' }}>Active</option>
                                                <option value="0" {{ !$testimonial->status ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td class="modern-table__td">{{ $testimonial->sort_order ?? 0 }}</td>
                                    <td class="modern-table__td modern-table__td--actions">
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.testimonials.show', $testimonial) }}"
                                               class="action-btn action-btn--view" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.testimonials.edit', $testimonial) }}"
                                               class="action-btn action-btn--edit" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST"
                                                  action="{{ route('admin.testimonials.destroy', $testimonial) }}"
                                                  class="action-form"
                                                  onsubmit="return confirm('Are you sure you want to delete this testimonial?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="action-btn action-btn--delete" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($testimonials->hasPages())
                    <div class="pagination-wrapper">
                        {{ $testimonials->links('components.pagination') }}
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <div class="empty-state__icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3 class="empty-state__title">No Testimonials Found</h3>
                    <p class="empty-state__text">Start by creating your first testimonial</p>
                    <a href="{{ route('admin.testimonials.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Add Testimonial
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

