@extends('layouts.frontend.main')
@section('content')
<section class="page-header">
    <div class="container">
        <div class="breadcrumb">
            <a href="{{ route('home') }}">Home</a>
            <span>/</span>
            <a href="{{ route('account.view-profile') }}">My Account</a>
            <span>/</span>
            <span>Manage Addresses</span>
        </div>
        <h1 class="page-title">My Account</h1>
    </div>
</section>
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
                                            <form method="POST" action="{{ route('account.addresses.destroy', $address->id) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this address?');">
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
                                            <form method="POST" action="{{ route('account.addresses.destroy', $address->id) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this address?');">
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
                                            <input type="text" id="addressFirstName" name="first_name" class="form-input" placeholder="Enter first name" value="{{ old('first_name', $user->first_name ?? '') }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="addressLastName" class="form-label">Last Name <span class="required">*</span></label>
                                            <input type="text" id="addressLastName" name="last_name" class="form-input" placeholder="Enter last name" value="{{ old('last_name', $user->last_name ?? '') }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="addressPhone" class="form-label">Phone Number <span class="required">*</span></label>
                                            <input type="tel" id="addressPhone" name="phone" class="form-input" placeholder="Enter phone number" value="{{ old('phone', $user->userDetail->phone ?? '') }}" required>
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
                                        </div>
                                        <div class="form-group form-group--full">
                                            <label for="addressStreet" class="form-label">Street Address <span class="required">*</span></label>
                                            <input type="text" id="addressStreet" name="street_address" class="form-input" placeholder="Enter street address" value="{{ old('street_address') }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="addressCity" class="form-label">City <span class="required">*</span></label>
                                            <input type="text" id="addressCity" name="city" class="form-input" placeholder="Enter city" value="{{ old('city') }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="addressSuburb" class="form-label">Suburb</label>
                                            <input type="text" id="addressSuburb" name="suburb" class="form-input" placeholder="Enter suburb" value="{{ old('suburb') }}">
                                        </div>
                                        <div class="form-group">
                                            <label for="addressPostcode" class="form-label">Postcode <span class="required">*</span></label>
                                            <input type="text" id="addressPostcode" name="zip_code" class="form-input" placeholder="Enter postcode" value="{{ old('zip_code') }}" required>
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
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading address. Please try again.');
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
</script>
@endsection
