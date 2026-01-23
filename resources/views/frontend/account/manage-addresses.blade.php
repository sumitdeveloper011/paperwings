@extends('layouts.frontend.main')
@section('content')

<style>
    .address-autocomplete-wrapper {
        position: relative;
    }

    .address-suggestions {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        max-height: 300px;
        overflow-y: auto;
        z-index: 1000;
        margin-top: 4px;
    }

    .address-suggestion-item {
        padding: 0.75rem 1rem;
        cursor: pointer;
        border-bottom: 1px solid #f0f0f0;
        transition: background-color 0.2s ease;
    }

    .address-suggestion-item:hover {
        background-color: #f8f9fa;
    }

    .address-suggestion-item:last-child {
        border-bottom: none;
    }
</style>
@include('frontend.partials.page-header', [
    'title' => 'Manage Addresses',
    'breadcrumbs' => [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'My Account', 'url' => route('account.view-profile')],
        ['label' => 'Manage Addresses', 'url' => null]
    ]
])
<section class="account-section">
    <div class="container">
        <div class="row">
            @include('frontend.account.partials.sidebar')

            <!-- Account Content -->
            <div class="col-lg-9">
                <!-- Manage Addresses -->
                <div class="account-content">
                    <div class="account-block">
                        <div class="account-block__header">
                            <h2 class="account-block__title">Manage Addresses</h2>
                            <button class="btn btn-primary btn-sm" id="addAddressBtn">
                                <i class="fas fa-plus"></i>
                                Add New Address
                            </button>
                        </div>

                        <!-- Billing Address -->
                        <div class="address-section">
                            <h3 class="address-section__title">
                                <i class="fas fa-file-invoice"></i>
                                Billing Address
                            </h3>

                            @if($billingAddresses && $billingAddresses->count() > 0)
                                @foreach($billingAddresses as $address)
                                <div class="address-card">
                                    <div class="address-card__header">
                                        @if($address->is_default)
                                        <span class="address-card__badge">Default</span>
                                        @endif
                                        <div class="address-card__actions">
                                            <button type="button" class="address-card__action" title="Edit" onclick="editAddress({{ $address->id }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form method="POST" action="{{ route('account.addresses.destroy', $address->id) }}" style="display: inline;" class="delete-address-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="address-card__action" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="address-card__body">
                                        <p class="address-card__name">{{ $address->first_name }} {{ $address->last_name }}</p>
                                        <p class="address-card__address">{{ $address->street_address }}</p>
                                        @if($address->suburb)
                                        <p class="address-card__address">{{ $address->suburb }}</p>
                                        @endif
                                        <p class="address-card__address">{{ $address->city }}{{ $address->region ? ', ' . $address->region->name : '' }} {{ $address->zip_code }}</p>
                                        <p class="address-card__address">{{ $address->country ?? 'New Zealand' }}</p>
                                        @if($address->phone)
                                        <p class="address-card__phone">Phone: {{ $address->phone }}</p>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <p class="text-muted">No billing addresses found.</p>
                            @endif
                        </div>

                        <!-- Shipping Address -->
                        <div class="address-section">
                            <h3 class="address-section__title">
                                <i class="fas fa-truck"></i>
                                Shipping Address
                            </h3>

                            @if($shippingAddresses && $shippingAddresses->count() > 0)
                                @foreach($shippingAddresses as $address)
                                <div class="address-card">
                                    <div class="address-card__header">
                                        @if($address->is_default)
                                        <span class="address-card__badge">Default</span>
                                        @endif
                                        <div class="address-card__actions">
                                            @if(!$address->is_default)
                                            <form method="POST" action="{{ route('account.addresses.set-default', $address->id) }}" style="display: inline;">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="address-card__action" title="Set as Default">
                                                    <i class="fas fa-star"></i>
                                                </button>
                                            </form>
                                            @endif
                                            <button type="button" class="address-card__action" title="Edit" onclick="editAddress({{ $address->id }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form method="POST" action="{{ route('account.addresses.destroy', $address->id) }}" style="display: inline;" class="delete-address-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="address-card__action" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="address-card__body">
                                        <p class="address-card__name">{{ $address->first_name }} {{ $address->last_name }}</p>
                                        <p class="address-card__address">{{ $address->street_address }}</p>
                                        @if($address->suburb)
                                        <p class="address-card__address">{{ $address->suburb }}</p>
                                        @endif
                                        <p class="address-card__address">{{ $address->city }}{{ $address->region ? ', ' . $address->region->name : '' }} {{ $address->zip_code }}</p>
                                        <p class="address-card__address">{{ $address->country ?? 'New Zealand' }}</p>
                                        @if($address->phone)
                                        <p class="address-card__phone">Phone: {{ $address->phone }}</p>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <p class="text-muted">No shipping addresses found.</p>
                            @endif
                        </div>

                        <!-- Add Address Form (Hidden by default) -->
                        <div class="address-form-container" id="addressFormContainer" style="display: none;">
                            <div class="account-block">
                                <h3 class="account-block__title" id="addressFormTitle">Add New Address</h3>

                                <form class="account-form" id="addAddressForm" method="POST" action="{{ route('account.addresses.store') }}">
                                    @csrf
                                    <input type="hidden" id="addressMethod" name="_method" value="POST">
                                    <input type="hidden" id="addressId" name="address_id" value="">

                                    <div class="form-group">
                                        <label for="addressType" class="form-label">Address Type <span class="required">*</span></label>
                                        <select id="addressType" name="type" class="form-input" required>
                                            <option value="">Select Type</option>
                                            <option value="billing">Billing Address</option>
                                            <option value="shipping">Shipping Address</option>
                                        </select>
                                    </div>

                                    <div class="account-form__grid">
                                        <div class="form-group">
                                            <label for="addressFirstName" class="form-label">First Name <span class="required">*</span></label>
                                            <input type="text" id="addressFirstName" name="first_name" class="form-input" placeholder="Enter first name" value="{{ old('first_name', $user->first_name ?? '') }}" minlength="2" maxlength="50" required>
                                            <div class="invalid-feedback" style="display: none;"></div>
                                        </div>
                                        <div class="form-group">
                                            <label for="addressLastName" class="form-label">Last Name <span class="required">*</span></label>
                                            <input type="text" id="addressLastName" name="last_name" class="form-input" placeholder="Enter last name" value="{{ old('last_name', $user->last_name ?? '') }}" minlength="2" maxlength="50" required>
                                            <div class="invalid-feedback" style="display: none;"></div>
                                        </div>
                                        <div class="form-group">
                                            <label for="addressPhone" class="form-label">Phone Number <span class="required">*</span></label>
                                            <input type="tel" id="addressPhone" name="phone" class="form-input" placeholder="Enter phone number" value="{{ old('phone', $user->userDetail->phone ?? '') }}" pattern="[\d\+\s\-]+" inputmode="numeric" maxlength="20" required>
                                            <div class="invalid-feedback" style="display: none;"></div>
                                        </div>
                                        <div class="form-group">
                                            <label for="addressEmail" class="form-label">Email Address</label>
                                            <input type="email" id="addressEmail" name="email" class="form-input" placeholder="Enter email address" value="{{ old('email', $user->email ?? '') }}">
                                        </div>
                                        <div class="form-group">
                                            <label for="addressCountry" class="form-label">Country</label>
                                            <input type="text" id="addressCountry" name="country" class="form-input" value="New Zealand" readonly style="background-color: #f8f9fa;">
                                        </div>
                                        <div class="form-group">
                                            <label for="addressRegion" class="form-label">Region <span class="required">*</span></label>
                                            <select id="addressRegion" name="region_id" class="form-input" required>
                                                <option value="">Select Region</option>
                                                @foreach($regions as $region)
                                                    <option value="{{ $region->id }}" {{ old('region_id') == $region->id ? 'selected' : '' }}>
                                                        {{ $region->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback" style="display: none;"></div>
                                        </div>
                                        <div class="form-group form-group--full">
                                            <label for="addressStreet" class="form-label">Street Address <span class="required">*</span></label>
                                            <div class="address-autocomplete-wrapper">
                                                <input type="text" id="addressStreet" name="street_address" class="form-input address-autocomplete" placeholder="Start typing your address (e.g., 123 Queen Street)" value="{{ old('street_address') }}" minlength="5" maxlength="255" autocomplete="off" required>
                                                <div id="addressStreetSuggestions" class="address-suggestions" style="display: none;"></div>
                                            </div>
                                            <small class="form-text text-muted">
                                                <i class="fas fa-info-circle"></i> Start typing to search for your address
                                            </small>
                                            <div class="invalid-feedback" style="display: none;"></div>
                                        </div>
                                        <div class="form-group">
                                            <label for="addressCity" class="form-label">City <span class="required">*</span></label>
                                            <input type="text" id="addressCity" name="city" class="form-input" placeholder="Enter city" value="{{ old('city') }}" minlength="2" maxlength="100" required>
                                            <div class="invalid-feedback" style="display: none;"></div>
                                        </div>
                                        <div class="form-group">
                                            <label for="addressSuburb" class="form-label">Suburb</label>
                                            <input type="text" id="addressSuburb" name="suburb" class="form-input" placeholder="Enter suburb" value="{{ old('suburb') }}">
                                        </div>
                                        <div class="form-group">
                                            <label for="addressPostcode" class="form-label">Postcode <span class="required">*</span></label>
                                            <input type="text" id="addressPostcode" name="zip_code" class="form-input" placeholder="Enter postcode" value="{{ old('zip_code') }}" pattern="[0-9]{4}" inputmode="numeric" maxlength="4" required>
                                            <div class="invalid-feedback" style="display: none;"></div>
                                        </div>
                                    </div>

                                    <div class="form-group form-group--checkbox">
                                        <label class="checkbox-label">
                                            <input type="checkbox" id="setAsDefault" name="is_default" value="1">
                                            <span class="checkmark"></span>
                                            Set as default address
                                        </label>
                                    </div>

                                    <div class="account-form__actions">
                                        <button type="submit" class="btn btn-primary" id="addressSubmitBtn">Save Address</button>
                                        <button type="button" class="btn btn-outline-secondary" id="cancelAddressBtn">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const addAddressBtn = document.getElementById('addAddressBtn');
    const addressFormContainer = document.getElementById('addressFormContainer');
    const cancelAddressBtn = document.getElementById('cancelAddressBtn');
    const addAddressForm = document.getElementById('addAddressForm');
    const addressFormTitle = document.getElementById('addressFormTitle');
    const addressSubmitBtn = document.getElementById('addressSubmitBtn');
    const addressIdInput = document.getElementById('addressId');

    // Add new address
    if (addAddressBtn && addressFormContainer) {
        addAddressBtn.addEventListener('click', function(e) {
            e.preventDefault();
            // Reset form for adding
            if (addAddressForm) {
                addAddressForm.action = '{{ route("account.addresses.store") }}';
                addAddressForm.method = 'POST';
                const methodInput = document.getElementById('addressMethod');
                if (methodInput) methodInput.value = 'POST';
                addAddressForm.reset();
                if (addressIdInput) addressIdInput.value = '';
                if (addressFormTitle) addressFormTitle.textContent = 'Add New Address';
                if (addressSubmitBtn) addressSubmitBtn.textContent = 'Save Address';
            }
            addressFormContainer.style.display = 'block';
            addressFormContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    }

    // Cancel form
    if (cancelAddressBtn && addressFormContainer) {
        cancelAddressBtn.addEventListener('click', function(e) {
            e.preventDefault();
            addressFormContainer.style.display = 'none';
            if (addAddressForm) {
                addAddressForm.reset();
                if (addressIdInput) addressIdInput.value = '';
            }
        });
    }
});

// Edit address function
function editAddress(addressId) {
    const addressFormContainer = document.getElementById('addressFormContainer');
    const addAddressForm = document.getElementById('addAddressForm');
    const addressFormTitle = document.getElementById('addressFormTitle');
    const addressSubmitBtn = document.getElementById('addressSubmitBtn');
    const addressIdInput = document.getElementById('addressId');

    fetch(`/account/addresses/${addressId}/edit`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.address) {
                const addr = data.address;

                // Update form action and method
                addAddressForm.action = `/account/addresses/${addressId}`;
                addAddressForm.method = 'POST';

                // Update method spoofing for PUT
                const methodInput = document.getElementById('addressMethod');
                if (methodInput) {
                    methodInput.value = 'PUT';
                }

                // Populate form fields
                document.getElementById('addressType').value = addr.type;
                document.getElementById('addressFirstName').value = addr.first_name || '';
                document.getElementById('addressLastName').value = addr.last_name || '';
                document.getElementById('addressPhone').value = addr.phone || '';
                document.getElementById('addressEmail').value = addr.email || '';
                document.getElementById('addressStreet').value = addr.street_address || '';
                document.getElementById('addressSuburb').value = addr.suburb || '';
                document.getElementById('addressCity').value = addr.city || '';
                document.getElementById('addressRegion').value = addr.region_id || '';
                document.getElementById('addressPostcode').value = addr.zip_code || '';
                document.getElementById('setAsDefault').checked = addr.is_default || false;

                if (addressIdInput) addressIdInput.value = addr.id;
                if (addressFormTitle) addressFormTitle.textContent = 'Edit Address';
                if (addressSubmitBtn) addressSubmitBtn.textContent = 'Update Address';

                // Show form
                addressFormContainer.style.display = 'block';
                addressFormContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        })
        .catch(async error => {
            if (window.customAlert) {
                await window.customAlert('Error loading address. Please try again.', 'Error', 'error');
            } else {
                alert('Error loading address. Please try again.');
            }
        });
}

// If page loads with address to edit, populate form
@php
    // Check if we're editing a specific address (passed from controller, not from foreach loop)
    // We check the route name to ensure we're on the edit route
    $isEditing = request()->routeIs('account.addresses.edit') && isset($address) && is_object($address) && isset($address->id);
@endphp
@if($isEditing)
document.addEventListener('DOMContentLoaded', function() {
    const addressFormContainer = document.getElementById('addressFormContainer');
    const addAddressForm = document.getElementById('addAddressForm');
    const addressFormTitle = document.getElementById('addressFormTitle');
    const addressSubmitBtn = document.getElementById('addressSubmitBtn');
    const methodInput = document.getElementById('addressMethod');

    if (addressFormContainer && addAddressForm) {
        // Update form for edit
        addAddressForm.action = '{{ route("account.addresses.update", $address->id) }}';
        if (methodInput) methodInput.value = 'PUT';

        // Populate form fields
        const typeField = document.getElementById('addressType');
        const firstNameField = document.getElementById('addressFirstName');
        const lastNameField = document.getElementById('addressLastName');
        const phoneField = document.getElementById('addressPhone');
        const emailField = document.getElementById('addressEmail');
        const streetField = document.getElementById('addressStreet');
        const suburbField = document.getElementById('addressSuburb');
        const cityField = document.getElementById('addressCity');
        const regionField = document.getElementById('addressRegion');
        const postcodeField = document.getElementById('addressPostcode');
        const defaultCheckbox = document.getElementById('setAsDefault');

        if (typeField) typeField.value = '{{ $address->type }}';
        if (firstNameField) firstNameField.value = '{{ addslashes($address->first_name) }}';
        if (lastNameField) lastNameField.value = '{{ addslashes($address->last_name) }}';
        if (phoneField) phoneField.value = '{{ addslashes($address->phone) }}';
        if (emailField) emailField.value = '{{ addslashes($address->email ?? '') }}';
        if (streetField) streetField.value = '{{ addslashes($address->street_address) }}';
        if (suburbField) suburbField.value = '{{ addslashes($address->suburb ?? '') }}';
        if (cityField) cityField.value = '{{ addslashes($address->city) }}';
        if (regionField) regionField.value = '{{ $address->region_id }}';
        if (postcodeField) postcodeField.value = '{{ addslashes($address->zip_code) }}';
        if (defaultCheckbox) defaultCheckbox.checked = {{ $address->is_default ? 'true' : 'false' }};

        if (addressFormTitle) addressFormTitle.textContent = 'Edit Address';
        if (addressSubmitBtn) addressSubmitBtn.textContent = 'Update Address';

        // Show form
        addressFormContainer.style.display = 'block';
        addressFormContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
});
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (window.FormSubmissionHandler) {
        window.FormSubmissionHandler.init('addAddressForm', {
            loadingText: 'Saving Address...',
            timeout: 10000
        });
    }
});

// NZ Post Address Autocomplete for Account Address Form
(function() {
    'use strict';

    function initAccountAddressAutocomplete() {
        const addressInput = document.getElementById('addressStreet');
        const suggestionsContainer = document.getElementById('addressStreetSuggestions');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        if (!addressInput || !suggestionsContainer) return;

        let searchTimeout;

        addressInput.addEventListener('input', (e) => {
            const query = e.target.value.trim();
            clearTimeout(searchTimeout);

            if (query.length < 3) {
                suggestionsContainer.style.display = 'none';
                return;
            }

            searchTimeout = setTimeout(() => {
                searchAccountAddresses(query, suggestionsContainer, csrfToken);
            }, 300);
        });

        document.addEventListener('click', (e) => {
            if (!addressInput.contains(e.target) && !suggestionsContainer.contains(e.target)) {
                suggestionsContainer.style.display = 'none';
            }
        });
    }

    async function searchAccountAddresses(query, container, csrfToken) {
        try {
            const response = await fetch('/account/search-address', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ query: query })
            });

            const data = await response.json();

            if (data.success && data.results && data.results.length > 0) {
                displayAccountSuggestions(data.results, container);
            } else {
                container.style.display = 'none';
            }
        } catch (error) {
            container.style.display = 'none';
        }
    }

    function displayAccountSuggestions(results, container) {
        container.innerHTML = '';
        
        results.forEach((result) => {
            const suggestionItem = document.createElement('div');
            suggestionItem.className = 'address-suggestion-item';
            suggestionItem.textContent = result.display;
            suggestionItem.addEventListener('click', () => {
                selectAccountAddress(result);
                container.style.display = 'none';
            });
            container.appendChild(suggestionItem);
        });

        container.style.display = 'block';
    }

    async function selectAccountAddress(result) {
        const addressInput = document.getElementById('addressStreet');
        const cityInput = document.getElementById('addressCity');
        const suburbInput = document.getElementById('addressSuburb');
        const postcodeInput = document.getElementById('addressPostcode');
        const regionSelect = document.getElementById('addressRegion');

        if (addressInput) addressInput.value = result.street_address || '';

        if (result.id) {
            try {
                const response = await fetch(`/account/get-address/${result.id}`);
                const data = await response.json();
                
                if (data.success && data.address) {
                    if (cityInput) cityInput.value = data.address.city || '';
                    if (suburbInput) suburbInput.value = data.address.suburb || '';
                    if (postcodeInput) postcodeInput.value = data.address.postcode || '';
                    
                    if (regionSelect && data.address.region) {
                        const options = Array.from(regionSelect.options);
                        const regionOption = options.find(opt => 
                            opt.text.toLowerCase().includes(data.address.region.toLowerCase())
                        );
                        if (regionOption) {
                            regionSelect.value = regionOption.value;
                        }
                    }
                } else {
                    populateFromResult(result, cityInput, suburbInput, postcodeInput, regionSelect);
                }
            } catch (error) {
                populateFromResult(result, cityInput, suburbInput, postcodeInput, regionSelect);
            }
        } else {
            populateFromResult(result, cityInput, suburbInput, postcodeInput, regionSelect);
        }
    }

    function populateFromResult(result, cityInput, suburbInput, postcodeInput, regionSelect) {
        if (cityInput) cityInput.value = result.city || '';
        if (suburbInput) suburbInput.value = result.suburb || '';
        if (postcodeInput) postcodeInput.value = result.postcode || '';

        if (regionSelect && result.region) {
            const options = Array.from(regionSelect.options);
            const regionOption = options.find(opt => 
                opt.text.toLowerCase().includes(result.region.toLowerCase())
            );
            if (regionOption) {
                regionSelect.value = regionOption.value;
            }
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAccountAddressAutocomplete);
    } else {
        initAccountAddressAutocomplete();
    }
})();
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize native form validation for address form
    function initAddressFormValidation() {
        if (typeof window.initFormValidationNative === 'undefined') {
            setTimeout(initAddressFormValidation, 100);
            return;
        }

        const form = document.getElementById('addAddressForm');
        if (!form) {
            setTimeout(initAddressFormValidation, 100);
            return;
        }

        const validationRules = {
            'type': {
                required: true
            },
            'first_name': {
                required: true,
                minlength: 2,
                maxlength: 50
            },
            'last_name': {
                required: true,
                minlength: 2,
                maxlength: 50
            },
            'phone': {
                required: true,
                nzPhone: true
            },
            'email': {
                email: true,
                maxlength: 255
            },
            'street_address': {
                required: true,
                minlength: 5,
                maxlength: 255
            },
            'city': {
                required: true,
                minlength: 2,
                maxlength: 100
            },
            'zip_code': {
                required: true,
                nzPostcode: true
            },
            'region_id': {
                required: true
            }
        };

        const validationMessages = {
            'type': {
                required: 'Please select an address type.'
            },
            'first_name': {
                required: 'Please enter your first name.',
                minlength: 'First name must be at least 2 characters.',
                maxlength: 'First name cannot exceed 50 characters.'
            },
            'last_name': {
                required: 'Please enter your last name.',
                minlength: 'Last name must be at least 2 characters.',
                maxlength: 'Last name cannot exceed 50 characters.'
            },
            'phone': {
                required: 'Please enter your phone number.',
                nzPhone: 'Please enter a valid New Zealand phone number (numbers only, e.g., 0211234567 or 091234567).'
            },
            'email': {
                email: 'Please enter a valid email address.',
                maxlength: 'Email cannot exceed 255 characters.'
            },
            'street_address': {
                required: 'Please enter your street address.',
                minlength: 'Street address must be at least 5 characters.',
                maxlength: 'Street address cannot exceed 255 characters.'
            },
            'city': {
                required: 'Please enter your city.',
                minlength: 'City must be at least 2 characters.',
                maxlength: 'City cannot exceed 100 characters.'
            },
            'zip_code': {
                required: 'Please enter your postcode.',
                nzPostcode: 'Please enter a valid 4-digit New Zealand postcode (numbers only).'
            },
            'region_id': {
                required: 'Please select a region.'
            }
        };

        window.initFormValidationNative('#addAddressForm', {
            rules: validationRules,
            messages: validationMessages,
            onInvalid: function(errors, validator) {
                validator.scrollToFirstError();
            }
        });
    }

    initAddressFormValidation();
});

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.delete-address-form').forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const form = this;
            
            if (window.customConfirm) {
                const confirmed = await window.customConfirm(
                    'Are you sure you want to delete this address?',
                    'Delete Address',
                    'question'
                );
                if (confirmed) {
                    form.submit();
                }
            } else {
                if (confirm('Are you sure you want to delete this address?')) {
                    form.submit();
                }
            }
        });
    });
});
</script>
@endpush
@endsection
