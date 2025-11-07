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
                <a href="{{ route('admin.sliders.create') }}" class="btn btn-primary btn-icon">
                    <i class="fas fa-plus"></i>
                    <span>Add Slider</span>
                </a>
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
                <p class="modern-card__subtitle">{{ $sliders->total() }} total sliders</p>
            </div>
        </div>

        <div class="modern-card__body">
            @if($sliders->count() > 0)
                <div class="modern-table-wrapper">
                    <table class="modern-table">
                        <thead class="modern-table__head">
                            <tr>
                                <th class="modern-table__th" width="50">
                                    <span>Order</span>
                                </th>
                                <th class="modern-table__th" width="100">
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
                                <th class="modern-table__th modern-table__th--actions" width="200">
                                    <span>Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="modern-table__body">
                            @foreach($sliders as $slider)
                                <tr class="modern-table__row">
                                    <td class="modern-table__td">
                                        <span class="badge badge--secondary">{{ $slider->sort_order }}</span>
                                    </td>
                                    <td class="modern-table__td">
                                        <div class="category-image" style="width: 80px; height: 50px;">
                                            <img src="{{ $slider->image_url }}" 
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
                                            <select name="status" class="status-select" onchange="this.form.submit()">
                                                <option value="1" {{ $slider->status === '1' ? 'selected' : '' }}>Active</option>
                                                <option value="0" {{ $slider->status === '0' ? 'selected' : '' }}>Inactive</option>
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
                                        <div class="action-buttons">
                                            <form method="POST" action="{{ route('admin.sliders.moveUp', $slider) }}" class="action-form">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="action-btn action-btn--success" title="Move Up">
                                                    <i class="fas fa-arrow-up"></i>
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.sliders.moveDown', $slider) }}" class="action-form">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="action-btn action-btn--warning" title="Move Down">
                                                    <i class="fas fa-arrow-down"></i>
                                                </button>
                                            </form>
                                            <a href="{{ route('admin.sliders.show', $slider) }}" 
                                               class="action-btn action-btn--view" 
                                               title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.sliders.edit', $slider) }}" 
                                               class="action-btn action-btn--edit" 
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.sliders.duplicate', $slider) }}" class="action-form">
                                                @csrf
                                                <button type="submit" class="action-btn action-btn--secondary" title="Duplicate">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </form>
                                            <form method="POST" 
                                                  action="{{ route('admin.sliders.destroy', $slider) }}" 
                                                  class="action-form"
                                                  onsubmit="return confirm('Are you sure you want to delete this slider?')">
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
            @else
                <div class="empty-state">
                    <div class="empty-state__icon">
                        <i class="fas fa-images"></i>
                    </div>
                    <h3 class="empty-state__title">No Sliders Found</h3>
                    <p class="empty-state__text">Start by creating your first slider</p>
                    <a href="{{ route('admin.sliders.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Add Slider
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
