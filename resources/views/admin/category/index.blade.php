@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-tags"></i>
                    Categories
                </h1>
                <p class="page-header__subtitle">Manage and organize your product categories</p>
            </div>
            <div class="page-header__actions">
                @can('categories.create')
                <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-icon">
                    <i class="fas fa-plus"></i>
                    <span>Add Category</span>
                </a>
                @endcan
                <button type="button" id="importCategoriesBtn" class="btn btn-primary btn-icon">
                    <i class="fas fa-download"></i>
                    <span>Import from EposNow</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="modern-card">
        <div class="modern-card__header">
            <div class="modern-card__header-content">
                <h3 class="modern-card__title">
                    <i class="fas fa-list"></i>
                    All Categories
                </h3>
                <p class="modern-card__subtitle">{{ $categories->total() }} total categories</p>
            </div>
            <div class="modern-card__header-actions">
                <form method="GET" class="search-form">
                    <div class="search-form__wrapper">
                        <i class="fas fa-search search-form__icon"></i>
                        <input type="text" name="search" class="search-form__input"
                               placeholder="Search categories..." value="{{ $search }}">
                        @if($search)
                            <a href="{{ route('admin.categories.index') }}" class="search-form__clear">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <div class="modern-card__body">
            @if($categories->count() > 0)
                <div class="modern-table-wrapper">
                    <table class="modern-table">
                        <thead class="modern-table__head">
                            <tr>
                                <th class="modern-table__th">
                                    <span>Image</span>
                                </th>
                                <th class="modern-table__th">
                                    <span>Name</span>
                                </th>
                                <th class="modern-table__th">
                                    <span>Slug</span>
                                </th>
                                <th class="modern-table__th">
                                    <span>Status</span>
                                </th>
                                <th class="modern-table__th">
                                    <span>Created</span>
                                </th>
                                <th class="modern-table__th modern-table__th--actions">
                                    <span>Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="modern-table__body">
                            @foreach($categories as $category)
                                <tr class="modern-table__row">
                                    <td class="modern-table__td">
                                        <div class="category-image">
                                            <img src="{{ $category->image_url }}"
                                                 alt="{{ $category->name }}"
                                                 class="category-image__img"
                                                 onerror="this.src='{{ asset('assets/images/placeholder.jpg') }}'">
                                        </div>
                                    </td>
                                    <td class="modern-table__td">
                                        <div class="category-name">
                                            <strong>{{ $category->name }}</strong>
                                        </div>
                                    </td>
                                    <td class="modern-table__td">
                                        <code class="category-slug">{{ $category->slug }}</code>
                                    </td>
                                    <td class="modern-table__td">
                                        <form method="POST" action="{{ route('admin.categories.updateStatus', $category) }}" class="status-form">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" class="status-select" onchange="this.form.submit()">
                                                @php
                                                    $status = (int) $category->status;
                                                @endphp
                                                <option value="1" {{ $status == 1 ? 'selected' : '' }}>Active</option>
                                                <option value="0" {{ $status == 0 ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td class="modern-table__td">
                                        <div class="category-date">
                                            <i class="fas fa-calendar-alt"></i>
                                            {{ $category->created_at->format('M d, Y') }}
                                        </div>
                                    </td>
                                    <td class="modern-table__td modern-table__td--actions">
                                        <div class="action-buttons">
                                            @can('categories.view')
                                            <a href="{{ route('admin.categories.show', $category) }}"
                                               class="action-btn action-btn--view"
                                               title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                            @can('categories.edit')
                                            <a href="{{ route('admin.categories.edit', $category) }}"
                                               class="action-btn action-btn--edit"
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                            @can('categories.delete')
                                            <form method="POST"
                                                  action="{{ route('admin.categories.destroy', $category) }}"
                                                  class="action-form"
                                                  onsubmit="return confirm('Are you sure you want to delete this category?')">
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

                <!-- Pagination -->
                @if($categories->hasPages())
                    <div class="pagination-wrapper">
                        {{ $categories->links('components.pagination') }}
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <div class="empty-state__icon">
                        <i class="fas fa-folder-open"></i>
                    </div>
                    <h3 class="empty-state__title">No Categories Found</h3>
                    @if($search)
                        <p class="empty-state__text">No categories found matching "{{ $search }}"</p>
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left"></i>
                            View All Categories
                        </a>
                    @else
                        <p class="empty-state__text">Start by creating your first category</p>
                        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Add Category
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Import Progress Modal -->
<div class="modal fade" id="importProgressModal" tabindex="-1" role="dialog" aria-labelledby="importProgressModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importProgressModalLabel">
                    <i class="fas fa-download"></i> Importing Categories from EposNow
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="closeModalBtn">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="import-progress-container">
                    <div class="import-progress-message" id="importProgressMessage">
                        Starting import...
                    </div>
                    <div class="progress" style="height: 25px; margin-top: 15px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated"
                             role="progressbar"
                             id="importProgressBar"
                             style="width: 0%;"
                             aria-valuenow="0"
                             aria-valuemin="0"
                             aria-valuemax="100">
                            <span id="importProgressText">0%</span>
                        </div>
                    </div>
                    <div class="import-stats" id="importStats" style="margin-top: 15px; display: none;">
                        <div class="row">
                            <div class="col-4 text-center">
                                <div class="stat-item">
                                    <div class="stat-value" id="statInserted">0</div>
                                    <div class="stat-label">Inserted</div>
                                </div>
                            </div>
                            <div class="col-4 text-center">
                                <div class="stat-item">
                                    <div class="stat-value" id="statUpdated">0</div>
                                    <div class="stat-label">Updated</div>
                                </div>
                            </div>
                            <div class="col-4 text-center">
                                <div class="stat-item">
                                    <div class="stat-value" id="statFailed">0</div>
                                    <div class="stat-label">Failed</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" id="importModalFooter" style="display: none;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="location.reload()">Refresh Page</button>
            </div>
        </div>
    </div>
</div>

<style>
.import-progress-container {
    padding: 10px 0;
}
.import-progress-message {
    font-size: 14px;
    color: #666;
    margin-bottom: 10px;
}
.import-stats {
    padding: 15px;
    background: #f8f9fa;
    border-radius: 5px;
}
.stat-item {
    padding: 10px;
}
.stat-value {
    font-size: 24px;
    font-weight: bold;
    color: #007bff;
}
.stat-label {
    font-size: 12px;
    color: #666;
    margin-top: 5px;
}
.progress-bar {
    font-size: 14px;
    font-weight: 500;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const importBtn = document.getElementById('importCategoriesBtn');
    const modal = document.getElementById('importProgressModal');
    const progressBar = document.getElementById('importProgressBar');
    const progressText = document.getElementById('importProgressText');
    const progressMessage = document.getElementById('importProgressMessage');
    const importStats = document.getElementById('importStats');
    const modalFooter = document.getElementById('importModalFooter');
    const closeModalBtn = document.getElementById('closeModalBtn');

    let jobId = null;
    let statusCheckInterval = null;

    // Initialize Bootstrap modal if using Bootstrap
    let bootstrapModal = null;
    if (typeof bootstrap !== 'undefined') {
        bootstrapModal = new bootstrap.Modal(modal);
    }

    importBtn.addEventListener('click', function() {
        // Disable button
        importBtn.disabled = true;
        importBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Starting...</span>';

        // Reset progress
        progressBar.style.width = '0%';
        progressBar.setAttribute('aria-valuenow', '0');
        progressText.textContent = '0%';
        progressMessage.textContent = 'Starting import...';
            importStats.style.display = 'none';
            modalFooter.style.display = 'none';

        // Show modal
        if (bootstrapModal) {
            bootstrapModal.show();
        } else {
            $(modal).modal('show');
        }

        // Start import
        fetch('{{ route("admin.categories.getCategoriesForEposNow") }}', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                jobId = data.job_id;
                progressMessage.textContent = 'Import job started! Checking status...';

                // Start polling for status
                startStatusPolling();
            } else {
                throw new Error(data.message || 'Failed to start import');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            progressMessage.textContent = 'Error: ' + error.message;
            progressBar.classList.remove('progress-bar-animated');
            progressBar.style.width = '0%';
            importBtn.disabled = false;
            importBtn.innerHTML = '<i class="fas fa-download"></i> <span>Import from EposNow</span>';
        });
    });

    function startStatusPolling() {
        if (!jobId) return;

        statusCheckInterval = setInterval(function() {
            // Build URL with jobId as query parameter
            // Use absolute URL to avoid any base URL issues
            const baseUrl = '{{ url("admin/categories/import-status") }}';
            const encodedJobId = encodeURIComponent(jobId);
            const statusUrl = `${baseUrl}?jobId=${encodedJobId}`;

            console.log('Checking status for job:', jobId);
            console.log('Status URL:', statusUrl);
            console.log('Base URL from route helper:', '{{ route("admin.categories.importStatus") }}');

            fetch(statusUrl, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                // Check if response is OK
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                // Get response as text first to check for issues
                return response.text();
            })
            .then(text => {
                // Trim whitespace and check for valid JSON
                text = text.trim();

                // Try to parse JSON
                try {
                    const json = JSON.parse(text);
                    return json;
                } catch (e) {
                    console.error('JSON Parse Error:', e);
                    console.error('Response text (first 1000 chars):', text.substring(0, 1000));

                    // Try to extract JSON if there's extra content
                    const jsonMatch = text.match(/\{[\s\S]*\}/);
                    if (jsonMatch) {
                        try {
                            return JSON.parse(jsonMatch[0]);
                        } catch (e2) {
                            console.error('Failed to parse extracted JSON:', e2);
                        }
                    }

                    // If still can't parse, show error but don't break polling
                    progressMessage.textContent = 'Error parsing response. Job may still be running...';
                    return null;
                }
            })
            .then(data => {
                // Skip if data is null (parsing failed)
                if (!data) {
                    return;
                }

                if (data.success && data.data) {
                    const progress = data.data;

                    // Update progress bar
                    const percentage = progress.percentage || 0;
                    progressBar.style.width = percentage + '%';
                    progressBar.setAttribute('aria-valuenow', percentage);
                    progressText.textContent = percentage + '%';

                    // Update message
                    progressMessage.textContent = progress.message || 'Processing...';

                    // Update stats if available
                    if (progress.inserted !== undefined || progress.updated !== undefined) {
                        importStats.style.display = 'block';
                        if (progress.inserted !== undefined) {
                            document.getElementById('statInserted').textContent = progress.inserted;
                        }
                        if (progress.updated !== undefined) {
                            document.getElementById('statUpdated').textContent = progress.updated;
                        }
                        if (progress.failed !== undefined) {
                            document.getElementById('statFailed').textContent = progress.failed;
                        }
                    }

                    // Check if completed
                    if (progress.status === 'completed' || percentage === 100) {
                        clearInterval(statusCheckInterval);
                        progressBar.classList.remove('progress-bar-animated');
                        progressBar.classList.add('bg-success');
                        progressMessage.textContent = progress.message || 'Import completed successfully!';
                        modalFooter.style.display = 'block';
                        importBtn.disabled = false;
                        importBtn.innerHTML = '<i class="fas fa-download"></i> <span>Import from EposNow</span>';
                    } else if (progress.status === 'failed' || progress.status === 'rate_limited') {
                        clearInterval(statusCheckInterval);
                        progressBar.classList.remove('progress-bar-animated');
                        progressBar.classList.add('bg-danger');

                        let errorMessage = progress.message || 'Import failed!';
                        if (progress.status === 'rate_limited') {
                            errorMessage = '⚠️ API Rate Limit Reached: ' + (progress.message || 'You have reached your maximum API limit. Please wait a few minutes and try again.');
                        }

                        progressMessage.textContent = errorMessage;
                        modalFooter.style.display = 'block';
                        importBtn.disabled = false;
                        importBtn.innerHTML = '<i class="fas fa-download"></i> <span>Import from EposNow</span>';
                    }
                } else if (!data.success && data.status === 'not_found') {
                    clearInterval(statusCheckInterval);
                    progressMessage.textContent = 'Job not found or expired';
                    modalFooter.style.display = 'block';
                    importBtn.disabled = false;
                    importBtn.innerHTML = '<i class="fas fa-download"></i> <span>Import from EposNow</span>';
                }
            })
            .catch(error => {
                console.error('Status check error:', error);
                // Don't stop polling on error, just log it
                // The job might still be processing
            });
        }, 2000); // Check every 2 seconds
    }

    // Clean up interval when modal is closed
    if (typeof $ !== 'undefined') {
        $(modal).on('hidden.bs.modal', function() {
            if (statusCheckInterval) {
                clearInterval(statusCheckInterval);
                statusCheckInterval = null;
            }
        });
    }
});
</script>
@endsection
