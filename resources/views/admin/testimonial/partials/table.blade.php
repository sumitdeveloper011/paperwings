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
                            <img src="{{ $testimonial->image ? asset('storage/' . $testimonial->image) : asset('assets/images/profile.png') }}"
                                 alt="{{ $testimonial->name }}"
                                 class="img-thumbnail"
                                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;"
                                 onerror="this.src='{{ asset('assets/images/profile.png') }}'">
                        </td>
                        <td class="modern-table__td">
                            <strong>{{ $testimonial->name }}</strong>
                            @if($testimonial->designation)
                                <br><small class="text-muted">{{ $testimonial->designation }}</small>
                            @endif
                        </td>
                        <td class="modern-table__td">{{ $testimonial->email }}</td>
                        <td class="modern-table__td">
                            <div class="text-truncate" style="max-width: 250px;" title="{{ strip_tags($testimonial->review) }}">
                                {{ \Illuminate\Support\Str::limit(strip_tags($testimonial->review), 50) }}
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
                                <select name="status" class="status-select" data-testimonial-id="{{ $testimonial->uuid }}">
                                    @php
                                        $status = (int) $testimonial->status;
                                    @endphp
                                    <option value="1" {{ $status == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ $status == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </form>
                        </td>
                        <td class="modern-table__td">{{ $testimonial->sort_order ?? 0 }}</td>
                        <td class="modern-table__td modern-table__td--actions">
                            <div class="action-buttons">
                                @can('testimonials.view')
                                <a href="{{ route('admin.testimonials.show', $testimonial) }}"
                                   class="action-btn action-btn--view" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @endcan
                                @can('testimonials.edit')
                                <a href="{{ route('admin.testimonials.edit', $testimonial) }}"
                                   class="action-btn action-btn--edit" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @can('testimonials.delete')
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
                                @endcan
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="empty-state">
        <div class="empty-state__icon">
            <i class="fas fa-star"></i>
        </div>
        <h3 class="empty-state__title">No Testimonials Found</h3>
        @if(request()->get('search'))
            <p class="empty-state__text">No testimonials found matching "{{ request()->get('search') }}"</p>
            <a href="{{ route('admin.testimonials.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left"></i>
                View All Testimonials
            </a>
        @else
            <p class="empty-state__text">Start by creating your first testimonial</p>
            @can('testimonials.create')
            <a href="{{ route('admin.testimonials.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Add Testimonial
            </a>
            @endcan
        @endif
    </div>
@endif
