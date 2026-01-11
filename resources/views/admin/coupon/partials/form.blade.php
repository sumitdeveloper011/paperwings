@php
    $coupon = $coupon ?? null;
@endphp

<!-- Basic Information -->
<div class="modern-card mb-4">
    <div class="modern-card__header">
        <h3 class="modern-card__title">
            <i class="fas fa-info-circle"></i>
            Basic Information
        </h3>
    </div>
    <div class="modern-card__body">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="code" class="form-label">Coupon Code <span class="text-danger">*</span></label>
                    <input type="text"
                           class="form-control @error('code') is-invalid @enderror"
                           id="code"
                           name="code"
                           value="{{ old('code', $coupon->code ?? '') }}"
                           placeholder="e.g., SAVE20"
                           required>
                    @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="name" class="form-label">Coupon Name <span class="text-danger">*</span></label>
                    <input type="text"
                           class="form-control @error('name') is-invalid @enderror"
                           id="name"
                           name="name"
                           value="{{ old('name', $coupon->name ?? '') }}"
                           placeholder="e.g., Summer Sale 2024"
                           required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Description -->
<div class="modern-card mb-4">
    <div class="modern-card__header">
        <h3 class="modern-card__title">
            <i class="fas fa-align-left"></i>
            Description
        </h3>
    </div>
    <div class="modern-card__body">
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control @error('description') is-invalid @enderror"
                      id="description"
                      name="description"
                      rows="6"
                      data-required="false"
                      placeholder="Enter coupon description">{{ old('description', $coupon->description ?? '') }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<!-- Discount Settings -->
<div class="modern-card mb-4">
    <div class="modern-card__header">
        <h3 class="modern-card__title">
            <i class="fas fa-percent"></i>
            Discount Settings
        </h3>
    </div>
    <div class="modern-card__body">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="type" class="form-label">Discount Type <span class="text-danger">*</span></label>
                    <select class="form-select @error('type') is-invalid @enderror"
                            id="type"
                            name="type"
                            required>
                        <option value="">Select Type</option>
                        <option value="percentage" {{ old('type', $coupon->type ?? '') === 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                        <option value="fixed" {{ old('type', $coupon->type ?? '') === 'fixed' ? 'selected' : '' }}>Fixed Amount ($)</option>
                    </select>
                    @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="value" class="form-label">Discount Value <span class="text-danger">*</span></label>
                    <input type="text"
                           inputmode="decimal"
                           pattern="[0-9]+(\.[0-9]{1,2})?"
                           class="form-control @error('value') is-invalid @enderror"
                           id="value"
                           name="value"
                           value="{{ old('value', $coupon->value ?? '') }}"
                           placeholder="0.00"
                           data-validation="numeric"
                           data-required="true"
                           required>
                    @error('value')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="minimum_amount" class="form-label">Minimum Amount</label>
                    <input type="text"
                           inputmode="decimal"
                           pattern="[0-9]+(\.[0-9]{1,2})?"
                           class="form-control @error('minimum_amount') is-invalid @enderror"
                           id="minimum_amount"
                           name="minimum_amount"
                           value="{{ old('minimum_amount', $coupon->minimum_amount ?? '') }}"
                           placeholder="0.00"
                           data-validation="numeric"
                           data-min="0"
                           data-max="1000000">
                    <small class="form-text text-muted">
                        <i class="fas fa-info-circle"></i>
                        Minimum order amount to use this coupon
                    </small>
                    @error('minimum_amount')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="maximum_discount" class="form-label">Maximum Discount</label>
                    <input type="text"
                           inputmode="decimal"
                           pattern="[0-9]+(\.[0-9]{1,2})?"
                           class="form-control @error('maximum_discount') is-invalid @enderror"
                           id="maximum_discount"
                           name="maximum_discount"
                           value="{{ old('maximum_discount', $coupon->maximum_discount ?? '') }}"
                           placeholder="0.00"
                           data-validation="numeric"
                           data-min="0"
                           data-max="100000">
                    <small class="form-text text-muted">
                        <i class="fas fa-info-circle"></i>
                        Maximum discount amount (for percentage type)
                    </small>
                    @error('maximum_discount')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Usage Limits -->
<div class="modern-card mb-4">
    <div class="modern-card__header">
        <h3 class="modern-card__title">
            <i class="fas fa-users"></i>
            Usage Limits
        </h3>
    </div>
    <div class="modern-card__body">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="usage_limit" class="form-label">Usage Limit</label>
                    <input type="text"
                           inputmode="numeric"
                           pattern="[0-9]*"
                           class="form-control @error('usage_limit') is-invalid @enderror"
                           id="usage_limit"
                           name="usage_limit"
                           value="{{ old('usage_limit', $coupon->usage_limit ?? '') }}"
                           placeholder="Leave empty for unlimited"
                           data-validation="integer"
                           data-min="1"
                           data-max="999999">
                    @error('usage_limit')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="usage_limit_per_user" class="form-label">Usage Limit Per User</label>
                    <input type="text"
                           inputmode="numeric"
                           pattern="[0-9]*"
                           class="form-control @error('usage_limit_per_user') is-invalid @enderror"
                           id="usage_limit_per_user"
                           name="usage_limit_per_user"
                           value="{{ old('usage_limit_per_user', $coupon->usage_limit_per_user ?? '') }}"
                           placeholder="Leave empty for unlimited"
                           data-validation="integer"
                           data-min="1"
                           data-max="999999">
                    @error('usage_limit_per_user')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Validity Period -->
<div class="modern-card mb-4">
    <div class="modern-card__header">
        <h3 class="modern-card__title">
            <i class="fas fa-calendar-alt"></i>
            Validity Period
        </h3>
    </div>
    <div class="modern-card__body">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                    <div class="input-wrapper">
                        <input type="text"
                               class="form-control date-input @error('start_date') is-invalid @enderror"
                               id="start_date"
                               name="start_date"
                               value="{{ old('start_date', $coupon && $coupon->start_date ? $coupon->start_date->format('d-m-Y') : '') }}"
                               placeholder="dd-mm-yyyy"
                               autocomplete="off"
                               readonly
                               required>
                        <span class="datepicker-trigger" data-target="#start_date">
                            <i class="fas fa-calendar-alt"></i>
                        </span>
                    </div>
                    <small class="form-text text-muted">
                        <i class="fas fa-info-circle"></i>
                        Format: dd-mm-yyyy (e.g., 25-12-2024)
                    </small>
                    @error('start_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                    <div class="input-wrapper">
                        <input type="text"
                               class="form-control date-input @error('end_date') is-invalid @enderror"
                               id="end_date"
                               name="end_date"
                               value="{{ old('end_date', $coupon && $coupon->end_date ? $coupon->end_date->format('d-m-Y') : '') }}"
                               placeholder="dd-mm-yyyy"
                               autocomplete="off"
                               readonly
                               required>
                        <span class="datepicker-trigger" data-target="#end_date">
                            <i class="fas fa-calendar-alt"></i>
                        </span>
                    </div>
                    <small class="form-text text-muted">
                        <i class="fas fa-info-circle"></i>
                        Format: dd-mm-yyyy (e.g., 31-12-2024)
                    </small>
                    @error('end_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Additional Settings -->
<div class="modern-card mb-4">
    <div class="modern-card__header">
        <h3 class="modern-card__title">
            <i class="fas fa-cog"></i>
            Additional Settings
        </h3>
    </div>
    <div class="modern-card__body">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                    <select class="form-select @error('status') is-invalid @enderror"
                            id="status"
                            name="status"
                            required>
                        <option value="">Select Status</option>
                        <option value="1" {{ old('status', $coupon->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status', $coupon->status ?? 0) == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>
</div>
