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
                <p class="page-header__subtitle">Organize your product categories</p>
            </div>
            <div class="page-header__actions">
                @can('categories.create')
                <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-icon">
                    <i class="fas fa-plus"></i>
                    <span>Add Category</span>
                </a>
                @endcan
                @if(auth()->user()->hasRole('SuperAdmin'))
                <button type="button" id="importCategoriesBtn" class="btn btn-primary btn-icon">
                    <i class="fas fa-download"></i>
                    <span>Import from EposNow</span>
                </button>
                @endif
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 1.5rem;">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" style="margin-bottom: 1.5rem;">
            <i class="fas fa-exclamation-circle"></i>
            {{ session('error') }}
        </div>
    @endif

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
                <form method="GET" class="search-form" id="search-form">
                    <div class="search-form__wrapper">
                        <div class="search-form__input-wrapper">
                            <input type="text"
                                   name="search"
                                   id="search-input"
                                   class="search-form__input"
                                   placeholder="Search categories..."
                                   value="{{ $search }}"
                                   autocomplete="off">
                            <button type="button" id="search-button" class="search-form__button">
                                <i class="fas fa-search"></i>
                            </button>
                            <a href="#" id="clear-search" class="search-form__clear" style="display: {{ $search ? 'flex' : 'none' }};">
                                <i class="fas fa-times"></i>
                            </a>
                            <div id="search-loading" class="search-form__loading" style="display: none;">
                                <i class="fas fa-spinner fa-spin"></i>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="modern-card__body">
            <div id="categories-results-container">
                @include('admin.category.partials.table', ['categories' => $categories])
            </div>
            <div id="categories-pagination-container">
                @if($categories->hasPages())
                    <div class="pagination-wrapper">
                        {{ $categories->links('components.pagination') }}
                    </div>
                @endif
            </div>
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

@push('scripts')
<script src="{{ asset('assets/js/admin-search.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Admin Search
    AdminSearch.init({
        searchInput: '#search-input',
        searchForm: '#search-form',
        searchButton: '#search-button',
        clearButton: '#clear-search',
        resultsContainer: '#categories-results-container',
        paginationContainer: '#categories-pagination-container',
        loadingIndicator: '#search-loading',
        searchUrl: '{{ route('admin.categories.index') }}',
        debounceDelay: 300
    });

    // Intercept pagination links on initial load
    AdminSearch.interceptPaginationLinks();

    // Handle status change with AJAX (prevent page freeze)
    // Use event delegation to handle dynamically added elements
    document.addEventListener('change', function(e) {
        if (e.target && e.target.classList.contains('status-select')) {
            e.preventDefault();
            e.stopPropagation();

            const select = e.target;
            const form = select.closest('.status-form');
            if (!form) return;

            const categoryId = select.getAttribute('data-category-id');
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
                    // You can add a toast notification here if needed
                    console.log('Status updated:', data.message);
                }
            })
            .catch(error => {
                console.error('Error updating status:', error);
                // Revert to original value on error
                select.value = originalValue;
                select.disabled = false;
                select.options[select.selectedIndex].textContent = originalText;
                alert('Error updating status. Please try again.');
            });
        }
    });
});
</script>
@endpush
@endsection
