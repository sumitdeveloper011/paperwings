@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-edit"></i>
                    Edit Coupon
                </h1>
                <p class="page-header__subtitle">Update coupon information</p>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary btn-icon">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Coupons</span>
                </a>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.coupons.update', $coupon) }}" class="modern-form" id="couponForm">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Main Form -->
            <div class="col-lg-8">
                <div class="modern-card">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-info-circle"></i>
                            Coupon Information
                        </h3>
                    </div>
                    <div class="modern-card__body">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label for="code" class="form-label-modern">
                                        Coupon Code <span class="required">*</span>
                                    </label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-ticket-alt input-icon"></i>
                                        <input type="text" 
                                               class="form-input-modern @error('code') is-invalid @enderror" 
                                               id="code" 
                                               name="code" 
                                               value="{{ old('code', $coupon->code) }}" 
                                               placeholder="e.g., SAVE20"
                                               required>
                                    </div>
                                    @error('code')
                                        <div class="form-error">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label for="name" class="form-label-modern">
                                        Coupon Name <span class="required">*</span>
                                    </label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-tag input-icon"></i>
                                        <input type="text" 
                                               class="form-input-modern @error('name') is-invalid @enderror" 
                                               id="name" 
                                               name="name" 
                                               value="{{ old('name', $coupon->name) }}" 
                                               placeholder="e.g., Summer Sale 2024"
                                               required>
                                    </div>
                                    @error('name')
                                        <div class="form-error">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group-modern">
                            <label for="description" class="form-label-modern">
                                Description
                            </label>
                            <div class="input-wrapper">
                                <textarea class="form-input-modern @error('description') is-invalid @enderror" 
                                          id="description" 
                                          name="description" 
                                          rows="3"
                                          placeholder="Enter coupon description">{{ old('description', $coupon->description) }}</textarea>
                            </div>
                            @error('description')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label for="type" class="form-label-modern">
                                        Discount Type <span class="required">*</span>
                                    </label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-percent input-icon"></i>
                                        <select class="form-input-modern @error('type') is-invalid @enderror" 
                                                id="type" 
                                                name="type" 
                                                required>
                                            <option value="">Select Type</option>
                                            <option value="percentage" {{ old('type', $coupon->type) === 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                            <option value="fixed" {{ old('type', $coupon->type) === 'fixed' ? 'selected' : '' }}>Fixed Amount ($)</option>
                                        </select>
                                    </div>
                                    @error('type')
                                        <div class="form-error">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label for="value" class="form-label-modern">
                                        Discount Value <span class="required">*</span>
                                    </label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-dollar-sign input-icon"></i>
                                        <input type="number" 
                                               step="0.01"
                                               class="form-input-modern @error('value') is-invalid @enderror" 
                                               id="value" 
                                               name="value" 
                                               value="{{ old('value', $coupon->value) }}" 
                                               placeholder="0.00"
                                               required>
                                    </div>
                                    @error('value')
                                        <div class="form-error">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label for="minimum_amount" class="form-label-modern">
                                        Minimum Amount
                                    </label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-shopping-cart input-icon"></i>
                                        <input type="number" 
                                               step="0.01"
                                               class="form-input-modern @error('minimum_amount') is-invalid @enderror" 
                                               id="minimum_amount" 
                                               name="minimum_amount" 
                                               value="{{ old('minimum_amount', $coupon->minimum_amount) }}" 
                                               placeholder="0.00">
                                    </div>
                                    <div class="form-hint">
                                        <i class="fas fa-info-circle"></i>
                                        Minimum order amount to use this coupon
                                    </div>
                                    @error('minimum_amount')
                                        <div class="form-error">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label for="maximum_discount" class="form-label-modern">
                                        Maximum Discount
                                    </label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-tag input-icon"></i>
                                        <input type="number" 
                                               step="0.01"
                                               class="form-input-modern @error('maximum_discount') is-invalid @enderror" 
                                               id="maximum_discount" 
                                               name="maximum_discount" 
                                               value="{{ old('maximum_discount', $coupon->maximum_discount) }}" 
                                               placeholder="0.00">
                                    </div>
                                    <div class="form-hint">
                                        <i class="fas fa-info-circle"></i>
                                        Maximum discount amount (for percentage type)
                                    </div>
                                    @error('maximum_discount')
                                        <div class="form-error">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label for="usage_limit" class="form-label-modern">
                                        Usage Limit
                                    </label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-users input-icon"></i>
                                        <input type="number" 
                                               class="form-input-modern @error('usage_limit') is-invalid @enderror" 
                                               id="usage_limit" 
                                               name="usage_limit" 
                                               value="{{ old('usage_limit', $coupon->usage_limit) }}" 
                                               placeholder="Leave empty for unlimited">
                                    </div>
                                    @error('usage_limit')
                                        <div class="form-error">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label for="usage_limit_per_user" class="form-label-modern">
                                        Usage Limit Per User
                                    </label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-user input-icon"></i>
                                        <input type="number" 
                                               class="form-input-modern @error('usage_limit_per_user') is-invalid @enderror" 
                                               id="usage_limit_per_user" 
                                               name="usage_limit_per_user" 
                                               value="{{ old('usage_limit_per_user', $coupon->usage_limit_per_user) }}" 
                                               placeholder="Leave empty for unlimited">
                                    </div>
                                    @error('usage_limit_per_user')
                                        <div class="form-error">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label for="start_date" class="form-label-modern">
                                        Start Date <span class="required">*</span>
                                    </label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-calendar input-icon"></i>
                                        <input type="text" 
                                               class="form-input-modern date-input @error('start_date') is-invalid @enderror" 
                                               id="start_date" 
                                               name="start_date" 
                                               value="{{ old('start_date', $coupon->start_date->format('d-m-Y')) }}" 
                                               placeholder="dd-mm-yyyy"
                                               autocomplete="off"
                                               readonly
                                               required>
                                        <span class="datepicker-trigger" data-target="#start_date">
                                            <i class="fas fa-calendar-alt"></i>
                                        </span>
                                    </div>
                                    <div class="form-hint">
                                        <i class="fas fa-info-circle"></i>
                                        Format: dd-mm-yyyy (e.g., 25-12-2024)
                                    </div>
                                    @error('start_date')
                                        <div class="form-error">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label for="end_date" class="form-label-modern">
                                        End Date <span class="required">*</span>
                                    </label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-calendar input-icon"></i>
                                        <input type="text" 
                                               class="form-input-modern date-input @error('end_date') is-invalid @enderror" 
                                               id="end_date" 
                                               name="end_date" 
                                               value="{{ old('end_date', $coupon->end_date->format('d-m-Y')) }}" 
                                               placeholder="dd-mm-yyyy"
                                               autocomplete="off"
                                               readonly
                                               required>
                                        <span class="datepicker-trigger" data-target="#end_date">
                                            <i class="fas fa-calendar-alt"></i>
                                        </span>
                                    </div>
                                    <div class="form-hint">
                                        <i class="fas fa-info-circle"></i>
                                        Format: dd-mm-yyyy (e.g., 31-12-2024)
                                    </div>
                                    @error('end_date')
                                        <div class="form-error">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                Status
                            </label>
                            <div class="input-wrapper">
                                <label class="toggle-switch">
                                    <input type="checkbox" name="status" value="1" {{ old('status', $coupon->status) ? 'checked' : '' }}>
                                    <span class="toggle-slider"></span>
                                </label>
                                <span class="toggle-label">Active</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Actions -->
            <div class="col-lg-4">
                <!-- Save Card -->
                <div class="modern-card modern-card--sticky">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-save"></i>
                            Save Coupon
                        </h3>
                    </div>
                    <div class="modern-card__body">
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary btn-block btn-lg">
                                <i class="fas fa-save"></i>
                                Update Coupon
                            </button>
                            <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary btn-block">
                                <i class="fas fa-times"></i>
                                Cancel
                            </a>
                        </div>
                        <div class="form-info">
                            <div class="info-item">
                                <i class="fas fa-info-circle"></i>
                                <span>Changes will be applied immediately after saving</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Tips -->
                <div class="modern-card">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-lightbulb"></i>
                            Quick Tips
                        </h3>
                    </div>
                    <div class="modern-card__body">
                        <ul class="tips-list">
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Use clear, memorable coupon codes that are easy to remember.</span>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Set realistic usage limits to prevent abuse of discounts.</span>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Percentage discounts work best for high-value items.</span>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Fixed amount discounts are ideal for low to medium value products.</span>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Set minimum order amounts to encourage larger purchases.</span>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Always set an end date to create urgency and prevent indefinite use.</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// Quick Tips Position
document.addEventListener('DOMContentLoaded', function() {
    const saveCard = document.querySelector('.modern-card--sticky');
    const quickTipsCard = saveCard ? saveCard.nextElementSibling : null;

    if (saveCard && quickTipsCard && quickTipsCard.classList.contains('modern-card')) {
        function updateQuickTipsPosition() {
            const saveCardHeight = saveCard.offsetHeight;
            const saveCardTop = 100; // top position of sticky Save Settings card
            const margin = 24; // 1.5rem = 24px
            const quickTipsTop = saveCardTop + saveCardHeight + margin;

            quickTipsCard.style.top = quickTipsTop + 'px';
        }

        // Initial calculation
        updateQuickTipsPosition();

        // Update on resize
        window.addEventListener('resize', updateQuickTipsPosition);

        // Use ResizeObserver to watch for Save Settings card height changes
        if (window.ResizeObserver) {
            const resizeObserver = new ResizeObserver(updateQuickTipsPosition);
            resizeObserver.observe(saveCard);
        }
    }

    // Initialize Bootstrap Datepicker
    $('#start_date').datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true,
        todayHighlight: true,
        orientation: 'bottom auto',
        clearBtn: true
    });

    $('#end_date').datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true,
        todayHighlight: true,
        orientation: 'bottom auto',
        clearBtn: true
    });

    // Set initial min date for end date if start date exists
    const startDateVal = $('#start_date').val();
    if (startDateVal) {
        const parts = startDateVal.split('-');
        if (parts.length === 3) {
            const startDate = new Date(parts[2], parts[1] - 1, parts[0]);
            const minDate = new Date(startDate);
            minDate.setDate(minDate.getDate() + 1);
            $('#end_date').datepicker('setStartDate', minDate);
        }
    }

    // Update end date min date when start date changes
    $('#start_date').on('changeDate', function(e) {
        const startDate = e.date;
        if (startDate) {
            const minDate = new Date(startDate);
            minDate.setDate(minDate.getDate() + 1);
            $('#end_date').datepicker('setStartDate', minDate);
            // If end date is before new min date, clear it
            const endDate = $('#end_date').datepicker('getDate');
            if (endDate && endDate <= startDate) {
                $('#end_date').datepicker('setDate', null);
            }
        }
    });

    // Trigger datepicker on icon click
    $('.datepicker-trigger').on('click', function() {
        const target = $(this).data('target');
        $(target).datepicker('show');
    });

    // Convert dd-mm-yyyy to yyyy-mm-dd for form submission
    function convertDateToISO(dateString) {
        if (!dateString) return '';
        const parts = dateString.split('-');
        if (parts.length === 3) {
            const day = parts[0].padStart(2, '0');
            const month = parts[1].padStart(2, '0');
            const year = parts[2];
            return `${year}-${month}-${day}`;
        }
        return dateString;
    }

    // Convert dates before form submission
    const form = document.getElementById('couponForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            
            if (startDateInput && startDateInput.value) {
                startDateInput.value = convertDateToISO(startDateInput.value);
            }
            if (endDateInput && endDateInput.value) {
                endDateInput.value = convertDateToISO(endDateInput.value);
            }
        });
    }
});
</script>
@endsection

