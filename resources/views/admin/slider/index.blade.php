@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="content-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="content-title">Sliders</h1>
                <p class="content-subtitle">Manage your website sliders</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.sliders.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Slider
                </a>
            </div>
        </div>
    </div>

    <div class="content-body">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="card-title mb-0">All Sliders</h5>
                    </div>
                    <div class="col-auto">
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="toggleSortMode">
                                <i class="fas fa-sort"></i> Sort Mode
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($sliders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped" id="slidersTable">
                            <thead>
                                <tr>
                                    <th width="50">Order</th>
                                    <th width="100">Image</th>
                                    <th>Heading</th>
                                    <th>Sub Heading</th>
                                    <th>Buttons</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th width="200">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="sortable-sliders">
                                @foreach($sliders as $slider)
                                    <tr data-id="{{ $slider->id }}">
                                        <td>
                                            <span class="badge bg-secondary">{{ $slider->sort_order }}</span>
                                            <div class="sort-handle d-none">
                                                <i class="fas fa-grip-vertical text-muted"></i>
                                            </div>
                                        </td>
                                        <td>
                                            <img src="{{ $slider->image_url }}" alt="{{ $slider->heading }}" 
                                                 class="img-thumbnail" style="width: 80px; height: 50px; object-fit: cover;">
                                        </td>
                                        <td>
                                            <strong>{{ $slider->heading }}</strong>
                                        </td>
                                        <td>
                                            {{ $slider->sub_heading ?? '-' }}
                                        </td>
                                        <td>
                                            @if($slider->has_buttons)
                                                <div class="d-flex flex-column gap-1">
                                                    @foreach($slider->buttons as $button)
                                                        <span class="badge bg-info">{{ $button['name'] }}</span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-muted">No buttons</span>
                                            @endif
                                        </td>
                                        <td>
                                            <form method="POST" action="{{ route('admin.sliders.updateStatus', $slider) }}" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                                    <option value="active" {{ $slider->status === 'active' ? 'selected' : '' }}>Active</option>
                                                    <option value="inactive" {{ $slider->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td>{{ $slider->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <!-- Move Up/Down buttons -->
                                                <form method="POST" action="{{ route('admin.sliders.moveUp', $slider) }}" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Move Up">
                                                        <i class="fas fa-arrow-up"></i>
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.sliders.moveDown', $slider) }}" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-outline-warning" title="Move Down">
                                                        <i class="fas fa-arrow-down"></i>
                                                    </button>
                                                </form>
                                                
                                                <!-- Standard actions -->
                                                <a href="{{ route('admin.sliders.show', $slider) }}" class="btn btn-sm btn-outline-info" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.sliders.edit', $slider) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                
                                                <!-- Duplicate button -->
                                                <form method="POST" action="{{ route('admin.sliders.duplicate', $slider) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-secondary" title="Duplicate">
                                                        <i class="fas fa-copy"></i>
                                                    </button>
                                                </form>
                                                
                                                <!-- Delete button -->
                                                <form method="POST" action="{{ route('admin.sliders.destroy', $slider) }}" class="d-inline" 
                                                      onsubmit="return confirm('Are you sure you want to delete this slider?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
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
                    <div class="text-center py-4">
                        <i class="fas fa-images fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Sliders Found</h5>
                        <p class="text-muted">Start by creating your first slider</p>
                        <a href="{{ route('admin.sliders.create') }}" class="btn btn-primary">Add Slider</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.sort-handle {
    cursor: move;
}

.sortable-placeholder {
    background-color: #f8f9fa;
    border: 2px dashed #dee2e6;
}

#sortable-sliders.ui-sortable tr {
    cursor: move;
}

.table tbody tr.ui-sortable-helper {
    background-color: #fff;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}
</style>

<!-- Include jQuery UI for sortable functionality -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/ui-lightness/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let sortMode = false;
    const toggleButton = document.getElementById('toggleSortMode');
    const sortableContainer = document.getElementById('sortable-sliders');
    const sortHandles = document.querySelectorAll('.sort-handle');

    // Toggle sort mode
    toggleButton.addEventListener('click', function() {
        sortMode = !sortMode;
        
        if (sortMode) {
            // Enable sort mode
            this.innerHTML = '<i class="fas fa-check"></i> Save Order';
            this.classList.remove('btn-outline-secondary');
            this.classList.add('btn-success');
            
            // Show sort handles
            sortHandles.forEach(handle => handle.classList.remove('d-none'));
            
            // Initialize sortable
            $(sortableContainer).sortable({
                handle: '.sort-handle',
                placeholder: 'sortable-placeholder',
                update: function(event, ui) {
                    // Auto-save when items are moved
                    saveOrder();
                }
            });
            
        } else {
            // Disable sort mode
            this.innerHTML = '<i class="fas fa-sort"></i> Sort Mode';
            this.classList.remove('btn-success');
            this.classList.add('btn-outline-secondary');
            
            // Hide sort handles
            sortHandles.forEach(handle => handle.classList.add('d-none'));
            
            // Destroy sortable
            if ($(sortableContainer).hasClass('ui-sortable')) {
                $(sortableContainer).sortable('destroy');
            }
        }
    });

    function saveOrder() {
        const sliderIds = [];
        document.querySelectorAll('#sortable-sliders tr').forEach(row => {
            const id = row.getAttribute('data-id');
            if (id) {
                sliderIds.push(parseInt(id));
            }
        });

        fetch('{{ route("admin.sliders.updateOrder") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                sliders: sliderIds
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update order numbers in the table
                document.querySelectorAll('#sortable-sliders tr').forEach((row, index) => {
                    const orderBadge = row.querySelector('.badge');
                    if (orderBadge) {
                        orderBadge.textContent = index + 1;
                    }
                });
                
                // Show success message (you can implement a toast notification here)
                console.log('Order updated successfully');
            } else {
                alert('Failed to update order: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the order');
        });
    }
});
</script>
@endsection
