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
                <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-icon">
                    <i class="fas fa-plus"></i>
                    <span>Add Product</span>
                </a>
                <button type="button" id="importProductsBtn" class="btn btn-primary btn-icon">
                    <i class="fas fa-download"></i>
                    <span>Import from EposNow</span>
                </button>
                <a href="{{ route('admin.products.importAllImages') }}"
                   class="btn btn-success btn-icon"
                   id="eposnowImageImportBtn"
                   onclick="showEposNowLoader(event)">
                    <i class="fas fa-images"></i>
                    <span>Import Images for All Products</span>
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
                    All Products
                </h3>
                <p class="modern-card__subtitle">{{ $products->total() }} total products</p>
            </div>
            <div class="modern-card__header-actions">
                <form method="GET" class="search-form">
                    <div class="search-form__wrapper" style="flex-wrap: wrap; gap: 0.5rem; align-items: center;">
                        <select name="category_id" class="form-select-modern" onchange="this.form.submit()" style="width: 180px;">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ $categoryId == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <select name="brand_id" class="form-select-modern" onchange="this.form.submit()" style="width: 150px;">
                            <option value="">All Brands</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ $brandId == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="search-form__input-wrapper" style="position: relative; display: flex; align-items: center;">
                            <i class="fas fa-search search-form__icon" style="position: absolute; left: 12px; z-index: 1; color: var(--text-secondary);"></i>
                            <input type="text" name="search" class="search-form__input"
                                   placeholder="Search products..." value="{{ $search }}" style="width: 200px; padding-left: 40px;">
                        </div>
                        @if($search || $categoryId || $brandId)
                            <a href="{{ route('admin.products.index') }}" class="search-form__clear">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <div class="modern-card__body">
            @if($products->count() > 0)
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
                                    <span>Category</span>
                                </th>
                                <th class="modern-table__th">
                                    <span>Price</span>
                                </th>
                                <th class="modern-table__th">
                                    <span>Status</span>
                                </th>
                                <th class="modern-table__th modern-table__th--actions">
                                    <span>Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="modern-table__body">
                            @foreach($products as $product)
                                <tr class="modern-table__row">
                                    <td class="modern-table__td">
                                        <div class="category-image">
                                            <img src="{{ $product->main_image }}"
                                                 alt="{{ $product->name }}"
                                                 class="category-image__img"
                                                 onerror="this.src='{{ asset('assets/images/placeholder.jpg') }}'">
                                        </div>
                                    </td>
                                    <td class="modern-table__td">
                                        <div class="category-name">
                                            <strong>{{ $product->name }}</strong>
                                            @if($product->short_description)
                                                <br>
                                                <small class="text-muted">{{ Str::limit($product->short_description, 50) }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="modern-table__td">
                                        <div>
                                            <span class="badge badge--info">
                                                <i class="fas fa-tag"></i>
                                                {{ $product->category->name }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="modern-table__td">
                                        <div>
                                            @if($product->discount_price)
                                                <div class="price-with-discount">
                                                    <div class="price-original">
                                                        <strong class="text-muted" style="text-decoration: line-through; font-size: 0.875rem;">
                                                            ${{ number_format($product->total_price, 2) }}
                                                        </strong>
                                                    </div>
                                                    <div class="price-discounted">
                                                        <strong class="text-success" style="font-size: 1rem;">
                                                            ${{ number_format($product->discount_price, 2) }}
                                                        </strong>
                                                        <span class="discount-badge">
                                                            @php
                                                                $discountPercent = $product->total_price > 0 ? round((($product->total_price - $product->discount_price) / $product->total_price) * 100, 0) : 0;
                                                                $discountPriceWithoutTax = round($product->discount_price / 1.15, 2);
                                                            @endphp
                                                            -{{ $discountPercent }}%
                                                        </span>
                                                    </div>
                                                    <small class="text-muted">Ex. Tax: ${{ number_format($discountPriceWithoutTax, 2) }}</small>
                                                </div>
                                            @else
                                                <strong class="text-success">${{ number_format($product->total_price, 2) }}</strong>
                                                <br>
                                                <small class="text-muted">Ex. Tax: ${{ number_format($product->price_without_tax, 2) }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="modern-table__td">
                                        <form method="POST" action="{{ route('admin.products.updateStatus', $product) }}" class="status-form">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" class="status-select" onchange="this.form.submit()">
                                                <option value="active" {{ $product->status === 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="inactive" {{ $product->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td class="modern-table__td modern-table__td--actions">
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.products.show', $product) }}"
                                               class="action-btn action-btn--view"
                                               title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.products.edit', $product) }}"
                                               class="action-btn action-btn--edit"
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST"
                                                  action="{{ route('admin.products.destroy', $product) }}"
                                                  class="action-form"
                                                  onsubmit="return confirm('Are you sure you want to delete this product?')">
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

                <!-- Pagination -->
                @if($products->hasPages())
                    <div class="pagination-wrapper">
                        {{ $products->links('components.pagination') }}
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <div class="empty-state__icon">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <h3 class="empty-state__title">No Products Found</h3>
                    @if($search)
                        <p class="empty-state__text">No products found matching "{{ $search }}"</p>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left"></i>
                            View All Products
                        </a>
                    @elseif($categoryId || $brandId)
                        <p class="empty-state__text">No products found with selected filters</p>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left"></i>
                            View All Products
                        </a>
                    @else
                        <p class="empty-state__text">Start by creating your first product</p>
                        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Add Product
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<!-- EposNow Import Loader Overlay -->
<div id="eposnowLoader" class="eposnow-loader-overlay" style="display: none;">
    <div class="eposnow-loader-content">
        <div class="eposnow-loader-spinner">
            <div class="spinner-ring"></div>
            <div class="spinner-ring"></div>
            <div class="spinner-ring"></div>
            <div class="spinner-ring"></div>
        </div>
        <h3 class="eposnow-loader-title">Importing Products from EposNow</h3>
        <p class="eposnow-loader-text">Please wait while we fetch and import products...</p>
        <p class="eposnow-loader-subtext">This may take a few moments</p>
    </div>
</div>

<style>
.eposnow-loader-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(55, 78, 148, 0.95);
    backdrop-filter: blur(5px);
    z-index: 9999;
    display: flex;
    justify-content: center;
    align-items: center;
    animation: fadeIn 0.3s ease-in-out;
}

.eposnow-loader-content {
    text-align: center;
    color: white;
    padding: 2rem;
}

.eposnow-loader-spinner {
    position: relative;
    width: 80px;
    height: 80px;
    margin: 0 auto 2rem;
}

.spinner-ring {
    position: absolute;
    width: 100%;
    height: 100%;
    border: 4px solid transparent;
    border-top-color: var(--teal);
    border-radius: 50%;
    animation: spin 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
}

.spinner-ring:nth-child(1) {
    animation-delay: -0.45s;
    border-top-color: var(--teal);
}

.spinner-ring:nth-child(2) {
    animation-delay: -0.3s;
    border-top-color: var(--sky-blue);
    width: 70px;
    height: 70px;
    top: 5px;
    left: 5px;
}

.spinner-ring:nth-child(3) {
    animation-delay: -0.15s;
    border-top-color: var(--lavender);
    width: 60px;
    height: 60px;
    top: 10px;
    left: 10px;
}

.spinner-ring:nth-child(4) {
    border-top-color: var(--light-green);
    width: 50px;
    height: 50px;
    top: 15px;
    left: 15px;
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

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

.eposnow-loader-title {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: white;
}

.eposnow-loader-text {
    font-size: 1rem;
    margin-bottom: 0.25rem;
    color: rgba(255, 255, 255, 0.9);
}

.eposnow-loader-subtext {
    font-size: 0.875rem;
    color: rgba(255, 255, 255, 0.7);
    margin-top: 0.5rem;
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
        fetch('{{ route("admin.products.getProductsForEposNow") }}', {
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
