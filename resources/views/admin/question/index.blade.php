@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-comments"></i>
                    Product Questions
                </h1>
                <p class="page-header__subtitle">Manage customer questions</p>
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="modern-card">
        <div class="modern-card__header">
            <div class="modern-card__header-content">
                <h3 class="modern-card__title">
                    <i class="fas fa-list"></i>
                    All Questions
                </h3>
                <p class="modern-card__subtitle">{{ $questions->total() }} total questions</p>
            </div>
            <div class="modern-card__header-actions">
                <form method="GET" class="search-form" id="search-form">
                    <div class="search-form__wrapper">
                        <div class="search-form__input-wrapper">
                            <input type="text"
                                   name="search"
                                   id="search-input"
                                   class="search-form__input"
                                   placeholder="Search questions..."
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
            <div id="results-container">
                @include('admin.question.partials.table', ['questions' => $questions])
            </div>

            <!-- Pagination Container -->
            <div id="pagination-container">
                @if($questions->total() > 0 && $questions->hasPages())
                    <div class="pagination-wrapper">
                        {{ $questions->links('components.pagination') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('assets/js/admin-search.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize AJAX search
    if (typeof AdminSearch !== 'undefined') {
        AdminSearch.init({
            searchInput: '#search-input',
            searchForm: '#search-form',
            searchButton: '#search-button',
            clearButton: '#clear-search',
            resultsContainer: '#results-container',
            paginationContainer: '#pagination-container',
            loadingIndicator: '#search-loading',
            searchUrl: '{{ route('admin.questions.index') }}',
            debounceDelay: 300
        });
    }

    // AJAX status update
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('status-select') && e.target.hasAttribute('data-question-id')) {
            e.preventDefault();
            const select = e.target;
            const form = select.closest('form');
            const questionId = select.getAttribute('data-question-id');
            const status = select.value;
            const originalValue = select.getAttribute('data-original-value') || (select.querySelector('option[selected]')?.value || select.value);

            // Store original value
            if (!select.hasAttribute('data-original-value')) {
                select.setAttribute('data-original-value', originalValue);
            }

            // Disable select during request
            select.disabled = true;

            // Create form data
            const formData = new FormData();
            formData.append('status', status);
            formData.append('_token', form.querySelector('input[name="_token"]')?.value || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'));
            formData.append('_method', 'PATCH');

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
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
                select.setAttribute('data-original-value', status);

                // Show success message if available
                if (typeof showToast === 'function') {
                    showToast('Success', data.message || 'Question status updated successfully', 'success', 3000);
                }
            })
            .catch(error => {
                // Revert select value on error
                select.value = originalValue;
                select.disabled = false;

                console.error('Error updating status:', error);
                if (typeof showToast === 'function') {
                    showToast('Error', 'Failed to update question status', 'error', 5000);
                } else {
                    alert('Failed to update question status. Please try again.');
                }
            });
        }
    });
});
</script>
@endpush
@endsection
