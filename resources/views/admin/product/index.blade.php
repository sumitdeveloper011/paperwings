@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-box"></i>
                    Products
                </h1>
                <p class="page-header__subtitle">Manage and organize your products</p>
            </div>
            <div class="page-header__actions">
                @can('products.create')
                <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-icon">
                    <i class="fas fa-plus"></i>
                    <span>Add Product</span>
                </a>
                @endcan
                @can('products.view')
                <a href="{{ route('admin.products.trash') }}" class="btn btn-warning btn-icon" style="background-color: #ffc107; color: #000; border-color: #ffc107;">
                    <i class="fas fa-trash-restore"></i>
                    <span>Trash</span>
                </a>
                @endcan
                @if(auth()->user()->hasRole('SuperAdmin'))
                <button type="button" id="importProductsBtn" class="btn btn-primary btn-icon">
                    <i class="fas fa-download"></i>
                    <span>Import from EposNow</span>
                </button>
                <button type="button" id="importStockBtn" class="btn btn-success btn-icon">
                    <i class="fas fa-boxes"></i>
                    <span>Import Products Stock from EposNow</span>
                </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="modern-card">
        <div class="modern-card__header">
            <div class="modern-card__header-content">
                <h3 class="modern-card__title">
                    <i class="fas fa-list"></i>
                    All Products
                </h3>
                <p class="modern-card__subtitle">{{ $products->total() }} total products</p>
            </div>
            <div class="modern-card__header-actions">
                <form method="GET" class="search-form" id="search-form">
                    <div class="search-form__wrapper">
                        @include('components.select-category', [
                            'id' => 'category-filter',
                            'name' => 'category_id',
                            'label' => '',
                            'required' => false,
                            'selected' => $categoryUuid,
                            'categories' => $categories,
                            'useUuid' => true,
                            'placeholder' => 'All Categories',
                            'class' => 'form-select-modern',
                            'useSelect2' => true,
                            'showLabel' => false,
                            'wrapperClass' => '',
                            'style' => 'width: 200px; margin-right: 0.5rem;',
                            'select2Width' => '200px'
                        ])
                        <div class="search-form__input-wrapper">
                            <input type="text"
                                   name="search"
                                   id="search-input"
                                   class="search-form__input"
                                   placeholder="Search products..."
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
            <div id="products-results-container">
                @include('admin.product.partials.table', ['products' => $products])
            </div>
            <div id="products-pagination-container">
                @if($products->hasPages())
                    <div class="pagination-wrapper">
                        {{ $products->links('components.pagination') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
/* Discount Badge Styles */
.price-with-discount {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.price-original {
    line-height: 1.2;
}

.price-discounted {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    line-height: 1.2;
}

.discount-badge {
    display: inline-block;
    background: var(--danger-color);
    color: white;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
    line-height: 1.2;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.8;
    }
}
</style>

<script>
// Product import functionality - see modal and script below
</script>

<!-- Import Progress Modal -->
<div class="modal fade" id="importProgressModal" tabindex="-1" role="dialog" aria-labelledby="importProgressModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importProgressModalLabel">
                    <i class="fas fa-download"></i> Importing Products from EposNow
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
                            <div class="col-3 text-center">
                                <div class="stat-item">
                                    <div class="stat-value" id="statInserted">0</div>
                                    <div class="stat-label">Inserted</div>
                                </div>
                            </div>
                            <div class="col-3 text-center">
                                <div class="stat-item">
                                    <div class="stat-value" id="statUpdated">0</div>
                                    <div class="stat-label">Updated</div>
                                </div>
                            </div>
                            <div class="col-3 text-center">
                                <div class="stat-item">
                                    <div class="stat-value" id="statImages">0</div>
                                    <div class="stat-label">Images</div>
                                </div>
                            </div>
                            <div class="col-3 text-center">
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
                <button type="button" id="retryFailedBtn" class="btn btn-warning" style="display: none;">
                    <i class="fas fa-redo"></i> Retry Failed Products
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="location.reload()">Refresh Page</button>
            </div>
        </div>
    </div>
</div>

<!-- Stock Import Progress Modal -->
<div class="modal fade" id="stockImportProgressModal" tabindex="-1" role="dialog" aria-labelledby="stockImportProgressModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="stockImportProgressModalLabel">
                    <i class="fas fa-boxes"></i> Importing Products Stock from EposNow
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="closeStockModalBtn">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="import-progress-container">
                    <div class="import-progress-message" id="stockImportProgressMessage">
                        Starting stock import...
                    </div>
                    <div class="progress" style="height: 25px; margin-top: 15px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated"
                             role="progressbar"
                             id="stockImportProgressBar"
                             style="width: 0%;"
                             aria-valuenow="0"
                             aria-valuemin="0"
                             aria-valuemax="100">
                            <span id="stockImportProgressText">0%</span>
                        </div>
                    </div>
                    <div class="import-stats" id="stockImportStats" style="margin-top: 15px; display: none;">
                        <div class="row">
                            <div class="col-4 text-center">
                                <div class="stat-item">
                                    <div class="stat-value" id="statStockUpdated">0</div>
                                    <div class="stat-label">Updated</div>
                                </div>
                            </div>
                            <div class="col-4 text-center">
                                <div class="stat-item">
                                    <div class="stat-value" id="statStockSkipped">0</div>
                                    <div class="stat-label">Skipped</div>
                                </div>
                            </div>
                            <div class="col-4 text-center">
                                <div class="stat-item">
                                    <div class="stat-value" id="statStockFailed">0</div>
                                    <div class="stat-label">Failed</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" id="stockImportModalFooter" style="display: none;">
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
    const importBtn = document.getElementById('importProductsBtn');
    if (!importBtn) return;

    const modal = document.getElementById('importProgressModal');
    const progressBar = document.getElementById('importProgressBar');
    const progressText = document.getElementById('importProgressText');
    const progressMessage = document.getElementById('importProgressMessage');
    const importStats = document.getElementById('importStats');
    const modalFooter = document.getElementById('importModalFooter');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const retryFailedBtn = document.getElementById('retryFailedBtn');

    let jobId = null;
    let statusCheckInterval = null;
    let currentJobId = null; // Store current job ID for retry

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
        fetch('{{ route("admin.products.getProductsForEposNow") }}', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => {
            // Check if response is OK
            if (!response.ok) {
                return response.text().then(text => {
                    throw new Error(`HTTP ${response.status}: ${text.substring(0, 200)}`);
                });
            }

            // Check content-type before parsing JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                return response.text().then(text => {
                    throw new Error('Response is not JSON. Server returned: ' + text.substring(0, 200));
                });
            }

            return response.json();
        })
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
            let errorMessage = 'Error: ' + error.message;

            // Better error message for JSON parse errors
            if (error.message.includes('JSON.parse') || error.message.includes('Unexpected token')) {
                errorMessage = 'Server returned invalid response. Please check server logs.';
            }

            progressMessage.textContent = errorMessage;
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
            const baseUrl = '{{ url("admin/products/import-status") }}';
            const encodedJobId = encodeURIComponent(jobId);
            const statusUrl = `${baseUrl}?jobId=${encodedJobId}`;

            fetch(statusUrl, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.text();
            })
            .then(text => {
                text = text.trim();
                try {
                    return JSON.parse(text);
                } catch (e) {
                    const jsonMatch = text.match(/\{[\s\S]*\}/);
                    if (jsonMatch) {
                        try {
                            return JSON.parse(jsonMatch[0]);
                        } catch (e2) {
                            return null;
                        }
                    }
                    return null;
                }
            })
            .then(data => {
                if (!data) return;

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
                        if (progress.images_imported !== undefined) {
                            document.getElementById('statImages').textContent = progress.images_imported;
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

                        // Show retry button if there are failed products
                        if (progress.failed > 0 && jobId) {
                            currentJobId = jobId;
                            retryFailedBtn.style.display = 'inline-block';
                            retryFailedBtn.disabled = false;
                            retryFailedBtn.innerHTML = '<i class="fas fa-redo"></i> Retry Failed Products';
                            retryFailedBtn.onclick = function() {
                                retryFailedProducts(currentJobId);
                            };
                        } else {
                            retryFailedBtn.style.display = 'none';
                        }

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
            });
        }, 2000); // Check every 2 seconds
    }

    // Retry failed products function
    function retryFailedProducts(jobIdToRetry) {
        if (!jobIdToRetry) {
            alert('No job ID available for retry');
            return;
        }

        // Disable retry button
        retryFailedBtn.disabled = true;
        retryFailedBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Retrying...';

        // Reset progress
        progressBar.style.width = '0%';
        progressBar.setAttribute('aria-valuenow', '0');
        progressText.textContent = '0%';
        progressMessage.textContent = 'Retrying failed products...';
        progressBar.classList.remove('bg-success', 'bg-danger');
        progressBar.classList.add('progress-bar-animated');
        importStats.style.display = 'none';
        modalFooter.style.display = 'none';

        // Start retry
        fetch('{{ route("admin.products.retryFailedProducts") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                jobId: jobIdToRetry
            })
        })
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => {
                    throw new Error(`HTTP ${response.status}: ${text.substring(0, 200)}`);
                });
            }

            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                return response.text().then(text => {
                    throw new Error('Response is not JSON. Server returned: ' + text.substring(0, 200));
                });
            }

            return response.json();
        })
        .then(data => {
            if (data.success) {
                jobId = data.job_id;
                currentJobId = data.job_id;
                progressMessage.textContent = 'Retry started for ' + data.failed_count + ' products! Checking status...';

                // Start polling for status
                startStatusPolling();
            } else {
                throw new Error(data.message || 'Failed to retry');
            }
        })
        .catch(error => {
            console.error('Retry Error:', error);
            progressMessage.textContent = 'Error: ' + error.message;
            progressBar.classList.remove('progress-bar-animated');
            retryFailedBtn.disabled = false;
            retryFailedBtn.innerHTML = '<i class="fas fa-redo"></i> Retry Failed Products';
            modalFooter.style.display = 'block';
        });
    }

    // Clean up interval when modal is closed
    if (typeof $ !== 'undefined') {
        $(modal).on('hidden.bs.modal', function() {
            if (statusCheckInterval) {
                clearInterval(statusCheckInterval);
                statusCheckInterval = null;
            }
            retryFailedBtn.style.display = 'none';
        });
    }

    // Stock Import Functionality
    const importStockBtn = document.getElementById('importStockBtn');
    if (importStockBtn) {
        const stockModal = document.getElementById('stockImportProgressModal');
        const stockProgressBar = document.getElementById('stockImportProgressBar');
        const stockProgressText = document.getElementById('stockImportProgressText');
        const stockProgressMessage = document.getElementById('stockImportProgressMessage');
        const stockImportStats = document.getElementById('stockImportStats');
        const stockModalFooter = document.getElementById('stockImportModalFooter');
        const closeStockModalBtn = document.getElementById('closeStockModalBtn');

        let stockJobId = null;
        let stockStatusCheckInterval = null;

        let stockBootstrapModal = null;
        if (typeof bootstrap !== 'undefined') {
            stockBootstrapModal = new bootstrap.Modal(stockModal);
        }

        importStockBtn.addEventListener('click', function() {
            importStockBtn.disabled = true;
            importStockBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Starting...</span>';

            stockProgressBar.style.width = '0%';
            stockProgressBar.setAttribute('aria-valuenow', '0');
            stockProgressText.textContent = '0%';
            stockProgressMessage.textContent = 'Starting stock import...';
            stockImportStats.style.display = 'none';
            stockModalFooter.style.display = 'none';

            if (stockBootstrapModal) {
                stockBootstrapModal.show();
            } else {
                $(stockModal).modal('show');
            }

            fetch('{{ route("admin.products.getStockForEposNow") }}', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => {
                        throw new Error(`HTTP ${response.status}: ${text.substring(0, 200)}`);
                    });
                }

                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    return response.text().then(text => {
                        throw new Error('Response is not JSON. Server returned: ' + text.substring(0, 200));
                    });
                }

                return response.json();
            })
            .then(data => {
                if (data.success) {
                    stockJobId = data.job_id;
                    stockProgressMessage.textContent = 'Stock import job started! Checking status...';
                    startStockStatusPolling();
                } else {
                    throw new Error(data.message || 'Failed to start stock import');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                let errorMessage = 'Error: ' + error.message;

                if (error.message.includes('JSON.parse') || error.message.includes('Unexpected token')) {
                    errorMessage = 'Server returned invalid response. Please check server logs.';
                }

                stockProgressMessage.textContent = errorMessage;
                stockProgressBar.classList.remove('progress-bar-animated');
                stockProgressBar.style.width = '0%';
                importStockBtn.disabled = false;
                importStockBtn.innerHTML = '<i class="fas fa-boxes"></i> <span>Import Products Stock from EposNow</span>';
            });
        });

        function startStockStatusPolling() {
            if (!stockJobId) return;

            stockStatusCheckInterval = setInterval(function() {
                const baseUrl = '{{ url("admin/products/stock-import-status") }}';
                const encodedJobId = encodeURIComponent(stockJobId);
                const statusUrl = `${baseUrl}?jobId=${encodedJobId}`;

                fetch(statusUrl, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.text();
                })
                .then(text => {
                    text = text.trim();
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        const jsonMatch = text.match(/\{[\s\S]*\}/);
                        if (jsonMatch) {
                            try {
                                return JSON.parse(jsonMatch[0]);
                            } catch (e2) {
                                return null;
                            }
                        }
                        return null;
                    }
                })
                .then(data => {
                    if (!data) return;

                    if (data.success && data.data) {
                        const progress = data.data;

                        const percentage = progress.percentage || 0;
                        stockProgressBar.style.width = percentage + '%';
                        stockProgressBar.setAttribute('aria-valuenow', percentage);
                        stockProgressText.textContent = percentage + '%';

                        stockProgressMessage.textContent = progress.message || 'Processing...';

                        if (progress.updated !== undefined || progress.skipped !== undefined) {
                            stockImportStats.style.display = 'block';
                            if (progress.updated !== undefined) {
                                document.getElementById('statStockUpdated').textContent = progress.updated;
                            }
                            if (progress.skipped !== undefined) {
                                document.getElementById('statStockSkipped').textContent = progress.skipped;
                            }
                            if (progress.failed !== undefined) {
                                document.getElementById('statStockFailed').textContent = progress.failed;
                            }
                        }

                        if (progress.status === 'completed' || percentage === 100) {
                            clearInterval(stockStatusCheckInterval);
                            stockProgressBar.classList.remove('progress-bar-animated');
                            stockProgressBar.classList.add('bg-success');
                            stockProgressMessage.textContent = progress.message || 'Stock import completed successfully!';
                            stockModalFooter.style.display = 'block';

                            importStockBtn.disabled = false;
                            importStockBtn.innerHTML = '<i class="fas fa-boxes"></i> <span>Import Products Stock from EposNow</span>';
                        } else if (progress.status === 'failed' || progress.status === 'rate_limited') {
                            clearInterval(stockStatusCheckInterval);
                            stockProgressBar.classList.remove('progress-bar-animated');
                            stockProgressBar.classList.add('bg-danger');

                            let errorMessage = progress.message || 'Stock import failed!';
                            if (progress.status === 'rate_limited') {
                                errorMessage = '⚠️ API Rate Limit Reached: ' + (progress.message || 'You have reached your maximum API limit. Please wait a few minutes and try again.');
                            }

                            stockProgressMessage.textContent = errorMessage;
                            stockModalFooter.style.display = 'block';
                            importStockBtn.disabled = false;
                            importStockBtn.innerHTML = '<i class="fas fa-boxes"></i> <span>Import Products Stock from EposNow</span>';
                        }
                    } else if (!data.success && data.status === 'not_found') {
                        clearInterval(stockStatusCheckInterval);
                        stockProgressMessage.textContent = 'Job not found or expired';
                        stockModalFooter.style.display = 'block';
                        importStockBtn.disabled = false;
                        importStockBtn.innerHTML = '<i class="fas fa-boxes"></i> <span>Import Products Stock from EposNow</span>';
                    }
                })
                .catch(error => {
                    console.error('Status check error:', error);
                });
            }, 2000);
        }

        if (typeof $ !== 'undefined') {
            $(stockModal).on('hidden.bs.modal', function() {
                if (stockStatusCheckInterval) {
                    clearInterval(stockStatusCheckInterval);
                    stockStatusCheckInterval = null;
                }
            });
        }
    }
});
</script>

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('assets/js/admin-search.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Admin Search
    AdminSearch.init({
        searchInput: '#search-input',
        searchForm: '#search-form',
        searchButton: '#search-button',
        clearButton: '#clear-search',
        resultsContainer: '#products-results-container',
        paginationContainer: '#products-pagination-container',
        loadingIndicator: '#search-loading',
        searchUrl: '{{ route('admin.products.index') }}',
        debounceDelay: 300,
        additionalParams: function() {
            const categoryId = document.getElementById('category-filter')?.value || '';
            return categoryId ? { category_id: categoryId } : {};
        }
    });

    // Handle category filter change - Support both Select2 and native select
    function setupCategoryFilter() {
        if (typeof jQuery === 'undefined') {
            // jQuery not loaded yet, retry
            setTimeout(setupCategoryFilter, 100);
            return;
        }

        const $categoryFilter = jQuery('#category-filter');
        if ($categoryFilter.length === 0) return;

        // Check if Select2 is initialized
        if ($categoryFilter.data('select2')) {
            // Use jQuery/Select2 change event
            $categoryFilter.off('change.categoryFilter').on('change.categoryFilter', function() {
                AdminSearch.performSearch();
            });
        } else {
            // Use native change event as fallback
            const categoryFilter = document.getElementById('category-filter');
            if (categoryFilter) {
                categoryFilter.removeEventListener('change', handleCategoryChange);
                categoryFilter.addEventListener('change', handleCategoryChange);
            }
        }
    }

    function handleCategoryChange() {
        AdminSearch.performSearch();
    }

    // Setup filter when Select2 is initialized
    if (typeof jQuery !== 'undefined') {
        jQuery(document).on('select2:initialized', '#category-filter', function() {
            setupCategoryFilter();
        });
    }

    // Setup filter after a short delay to ensure Select2 is initialized
    setTimeout(setupCategoryFilter, 500);

    // Also setup immediately in case Select2 loads quickly
    setupCategoryFilter();

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

            const productId = select.getAttribute('data-product-id');
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
                    showToast('Error', 'Failed to update product status', 'error', 5000);
                } else {
                    alert('Error updating status. Please try again.');
                }
            });
        }
    });
});
</script>
@endpush
@endsection
