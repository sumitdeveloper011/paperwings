@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-images"></i>
                    Sliders
                </h1>
                <p class="page-header__subtitle">Manage your website sliders</p>
            </div>
            <div class="page-header__actions">
                @can('sliders.create')
                <a href="{{ route('admin.sliders.create') }}" class="btn btn-primary btn-icon">
                    <i class="fas fa-plus"></i>
                    <span>Add Slider</span>
                </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="modern-card">
        <div class="modern-card__header">
            <div class="modern-card__header-content">
                <h3 class="modern-card__title">
                    <i class="fas fa-list"></i>
                    All Sliders
                </h3>
                <p class="modern-card__subtitle">{{ $sliders ? $sliders->count() : 0 }} total sliders</p>
            </div>
        </div>

        <div class="modern-card__body">
            @if($sliders && $sliders->count() > 0)
                <div class="modern-table-wrapper modern-table-wrapper--enhanced">
                    <table class="modern-table modern-table--enhanced">
                        <thead class="modern-table__head modern-table__head--sticky">
                            <tr>
                                <th class="modern-table__th" width="50">
                                    <span>Order</span>
                                </th>
                                <th class="modern-table__th" width="120">
                                    <span>Image</span>
                                </th>
                                <th class="modern-table__th">
                                    <span>Heading</span>
                                </th>
                                <th class="modern-table__th">
                                    <span>Sub Heading</span>
                                </th>
                                <th class="modern-table__th">
                                    <span>Buttons</span>
                                </th>
                                <th class="modern-table__th">
                                    <span>Status</span>
                                </th>
                                <th class="modern-table__th">
                                    <span>Created</span>
                                </th>
                                <th class="modern-table__th modern-table__th--actions" width="250">
                                    <span>Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="modern-table__body">
                            @foreach($sliders as $index => $slider)
                                <tr class="modern-table__row modern-table__row--animated" style="animation-delay: {{ $index * 0.05 }}s;">
                                    <td class="modern-table__td">
                                        <span class="badge badge--secondary">{{ $slider->sort_order }}</span>
                                    </td>
                                    <td class="modern-table__td">
                                        <div class="category-image category-image--enhanced">
                                            @php
                                                $imageUrl = $slider->thumbnail_url ?? $slider->image_url ?? asset('assets/images/placeholder.png');
                                            @endphp
                                            <img src="{{ $imageUrl }}"
                                                 alt="{{ $slider->heading }}"
                                                 class="category-image__img"
                                                 onerror="this.src='{{ asset('assets/images/placeholder.png') }}'">
                                        </div>
                                    </td>
                                    <td class="modern-table__td">
                                        <div class="category-name">
                                            <strong>{{ $slider->heading }}</strong>
                                        </div>
                                    </td>
                                    <td class="modern-table__td">
                                        <span class="text-muted">{{ $slider->sub_heading ?? '-' }}</span>
                                    </td>
                                    <td class="modern-table__td">
                                        @if($slider->has_buttons)
                                            <div class="button-badges">
                                                @foreach($slider->buttons as $button)
                                                    <span class="badge badge--info badge--sm">
                                                        {{ $button['name'] }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-muted">No buttons</span>
                                        @endif
                                    </td>
                                    <td class="modern-table__td">
                                        <form method="POST" action="{{ route('admin.sliders.updateStatus', $slider) }}" class="status-form">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" class="status-select" data-slider-id="{{ $slider->id }}">
                                                <option value="1" {{ (int)$slider->status === 1 ? 'selected' : '' }}>Active</option>
                                                <option value="0" {{ (int)$slider->status === 0 ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td class="modern-table__td">
                                        <div class="category-date">
                                            <i class="fas fa-calendar-alt"></i>
                                            {{ $slider->created_at->format('M d, Y') }}
                                        </div>
                                    </td>
                                    <td class="modern-table__td modern-table__td--actions">
                                        <div class="action-buttons action-buttons--enhanced">
                                            <form method="POST" action="{{ route('admin.sliders.moveUp', $slider) }}" class="action-form">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="action-btn action-btn--success action-btn--ripple" title="Move Up">
                                                    <i class="fas fa-arrow-up"></i>
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.sliders.moveDown', $slider) }}" class="action-form">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="action-btn action-btn--warning action-btn--ripple" title="Move Down">
                                                    <i class="fas fa-arrow-down"></i>
                                                </button>
                                            </form>
                                            @can('sliders.view')
                                            <a href="{{ route('admin.sliders.show', $slider) }}"
                                               class="action-btn action-btn--view action-btn--ripple"
                                               title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                            @can('sliders.edit')
                                            <a href="{{ route('admin.sliders.edit', $slider) }}"
                                               class="action-btn action-btn--edit action-btn--ripple"
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                            @can('sliders.create')
                                            <form method="POST" action="{{ route('admin.sliders.duplicate', $slider) }}" class="action-form">
                                                @csrf
                                                <button type="submit" class="action-btn action-btn--secondary action-btn--ripple" title="Duplicate">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </form>
                                            @endcan
                                            @can('sliders.delete')
                                            <form method="POST"
                                                  action="{{ route('admin.sliders.destroy', $slider) }}"
                                                  class="action-form"
                                                  onsubmit="return confirm('Are you sure you want to delete this slider?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="action-btn action-btn--delete action-btn--ripple" title="Delete">
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
                <div class="empty-state empty-state--enhanced">
                    <div class="empty-state__icon">
                        <i class="fas fa-images"></i>
                    </div>
                    <h3 class="empty-state__title">No Sliders Found</h3>
                    <p class="empty-state__text">Start by creating your first slider</p>
                    @can('sliders.create')
                    <a href="{{ route('admin.sliders.create') }}" class="btn btn-primary btn-ripple">
                        <i class="fas fa-plus"></i>
                        Add Slider
                    </a>
                    @endcan
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle status change with AJAX (prevent page freeze)
    document.addEventListener('change', function(e) {
        if (e.target && e.target.classList.contains('status-select')) {
            e.preventDefault();
            e.stopPropagation();

            const select = e.target;
            const form = select.closest('.status-form');
            if (!form) return;

            const sliderId = select.getAttribute('data-slider-id');
            const newStatus = select.value;
            const originalValue = select.value === '1' ? '0' : '1';

            // Disable select during request
            select.disabled = true;
            const originalText = select.options[select.selectedIndex].textContent;
            select.options[select.selectedIndex].textContent = 'Updating...';

            // Get CSRF token
            const csrfToken = form.querySelector('input[name="_token"]').value;
            const formAction = form.getAttribute('action');

            // Send AJAX request
            fetch(formAction, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: new URLSearchParams({
                    '_token': csrfToken,
                    '_method': 'PATCH',
                    'status': newStatus
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Re-enable select
                select.disabled = false;
                select.options[select.selectedIndex].textContent = originalText;

                // Show success message if available
                if (data && data.message) {
                    if (typeof showToast === 'function') {
                        showToast('Success', data.message, 'success', 3000);
                    }
                }
            })
            .catch(error => {
                console.error('Error updating status:', error);
                // Revert to original value on error
                select.value = originalValue;
                select.disabled = false;
                select.options[select.selectedIndex].textContent = originalText;
                if (typeof showToast === 'function') {
                    showToast('Error', 'Failed to update slider status', 'error', 5000);
                } else {
                    alert('Error updating status. Please try again.');
                }
            });
        }
    });

    // Handle up/down arrow buttons with AJAX
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (form && form.classList.contains('action-form')) {
            const action = form.getAttribute('action');
            if (action && (action.includes('move-up') || action.includes('move-down'))) {
                e.preventDefault();
                e.stopPropagation();

                const button = form.querySelector('button[type="submit"]');
                if (!button) return;

                const originalHtml = button.innerHTML;
                const originalDisabled = button.disabled;

                // Disable button during request
                button.disabled = true;
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                // Get CSRF token
                const csrfToken = form.querySelector('input[name="_token"]').value;

                // Send AJAX request
                fetch(action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: new URLSearchParams({
                        '_token': csrfToken,
                        '_method': 'PATCH'
                    })
                })
                .then(response => {
                    return response.json().then(data => {
                        if (!response.ok && response.status !== 200) {
                            throw new Error(data.message || 'Network response was not ok');
                        }
                        return data;
                    });
                })
                .then(data => {
                    // Re-enable button
                    button.disabled = originalDisabled;
                    button.innerHTML = originalHtml;

                    if (data && data.success) {
                        if (typeof showToast === 'function') {
                            showToast('Success', data.message || 'Slider moved successfully!', 'success', 3000);
                        }
                        // Reload page to show updated order
                        setTimeout(() => {
                            window.location.reload();
                        }, 500);
                    } else {
                        // Handle case where move was not possible (e.g., already at top/bottom)
                        const messageType = data.type || 'info';
                        if (typeof showToast === 'function') {
                            showToast('Info', data.message || 'Cannot move slider further', messageType, 3000);
                        } else {
                            alert(data.message || 'Cannot move slider further');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error moving slider:', error);
                    // Re-enable button
                    button.disabled = originalDisabled;
                    button.innerHTML = originalHtml;
                    if (typeof showToast === 'function') {
                        showToast('Error', error.message || 'Failed to move slider', 'error', 5000);
                    } else {
                        alert(error.message || 'Error moving slider. Please try again.');
                    }
                });
            }
        }
    });
});
</script>
@endpush
@endsection
