@extends('layouts.frontend.main')
@section('content')
    @include('include.frontend.breadcrumb')
    <section class="account-section">
        <div class="container">
            <div class="row">
                <!-- Account Sidebar -->
                <div class="col-lg-3">
                    <div class="account-sidebar">
                        <div class="account-user">
                            <div class="account-user__avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="account-user__info">
                                <h3 class="account-user__name">John Doe</h3>
                                <p class="account-user__email">john.doe@example.com</p>
                            </div>
                        </div>
                        
                        <nav class="account-nav">
                            <ul class="account-nav__list">
                                <li class="account-nav__item">
                                    <a href="#view-profile" class="account-nav__link account-nav__link--active">
                                        <i class="fas fa-user-circle"></i>
                                        <span>View Profile</span>
                                    </a>
                                </li>
                                <li class="account-nav__item">
                                    <a href="#edit-profile" class="account-nav__link">
                                        <i class="fas fa-edit"></i>
                                        <span>Edit Profile</span>
                                    </a>
                                </li>
                                <li class="account-nav__item">
                                    <a href="#change-password" class="account-nav__link">
                                        <i class="fas fa-key"></i>
                                        <span>Change Password</span>
                                    </a>
                                </li>
                                <li class="account-nav__item">
                                    <a href="#manage-addresses" class="account-nav__link">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>Manage Addresses</span>
                                    </a>
                                </li>
                                <li class="account-nav__item">
                                    <a href="#my-orders" class="account-nav__link">
                                        <i class="fas fa-shopping-bag"></i>
                                        <span>My Orders</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>

                <!-- Account Content -->
                <div class="col-lg-9">
                    <!-- View Profile -->
                    <div class="account-content" id="view-profile">
                        <div class="account-block">
                            <h2 class="account-block__title">View Profile</h2>
                            
                            <div class="profile-view">
                                <div class="profile-view__header">
                                    <div class="profile-view__avatar-large">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="profile-view__info">
                                        <h3 class="profile-view__name">John Doe</h3>
                                        <p class="profile-view__email">john.doe@example.com</p>
                                        <p class="profile-view__phone">+1 (555) 123-4567</p>
                                    </div>
                                </div>
                                
                                <div class="profile-view__details">
                                    <div class="profile-detail-row">
                                        <div class="profile-detail-label">First Name</div>
                                        <div class="profile-detail-value">John</div>
                                    </div>
                                    <div class="profile-detail-row">
                                        <div class="profile-detail-label">Last Name</div>
                                        <div class="profile-detail-value">Doe</div>
                                    </div>
                                    <div class="profile-detail-row">
                                        <div class="profile-detail-label">Email Address</div>
                                        <div class="profile-detail-value">john.doe@example.com</div>
                                    </div>
                                    <div class="profile-detail-row">
                                        <div class="profile-detail-label">Phone Number</div>
                                        <div class="profile-detail-value">+1 (555) 123-4567</div>
                                    </div>
                                    <div class="profile-detail-row">
                                        <div class="profile-detail-label">Date of Birth</div>
                                        <div class="profile-detail-value">January 15, 1990</div>
                                    </div>
                                    <div class="profile-detail-row">
                                        <div class="profile-detail-label">Gender</div>
                                        <div class="profile-detail-value">Male</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Profile -->
                    <div class="account-content account-content--hidden" id="edit-profile">
                        <div class="account-block">
                            <h2 class="account-block__title">Edit Profile / Personal Info</h2>
                            
                            <form class="account-form" id="editProfileForm">
                                <div class="account-form__grid">
                                    <div class="form-group">
                                        <label for="editFirstName" class="form-label">First Name <span class="required">*</span></label>
                                        <input type="text" id="editFirstName" class="form-input" value="John" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="editLastName" class="form-label">Last Name <span class="required">*</span></label>
                                        <input type="text" id="editLastName" class="form-input" value="Doe" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="editEmail" class="form-label">Email Address <span class="required">*</span></label>
                                        <input type="email" id="editEmail" class="form-input" value="john.doe@example.com" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="editPhone" class="form-label">Phone Number <span class="required">*</span></label>
                                        <input type="tel" id="editPhone" class="form-input" value="+1 (555) 123-4567" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="editDateOfBirth" class="form-label">Date of Birth</label>
                                        <input type="date" id="editDateOfBirth" class="form-input" value="1990-01-15">
                                    </div>
                                    <div class="form-group">
                                        <label for="editGender" class="form-label">Gender</label>
                                        <select id="editGender" class="form-input">
                                            <option value="">Select Gender</option>
                                            <option value="male" selected>Male</option>
                                            <option value="female">Female</option>
                                            <option value="other">Other</option>
                                            <option value="prefer-not-to-say">Prefer not to say</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="account-form__actions">
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                    <button type="button" class="btn btn-outline-secondary">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Change Password -->
                    <div class="account-content account-content--hidden" id="change-password">
                        <div class="account-block">
                            <h2 class="account-block__title">Change Password</h2>
                            
                            <form class="account-form" id="changePasswordForm">
                                <div class="form-group">
                                    <label for="currentPassword" class="form-label">Current Password <span class="required">*</span></label>
                                    <input type="password" id="currentPassword" class="form-input" placeholder="Enter your current password" required>
                                    <div class="form-help">
                                        <i class="fas fa-info-circle"></i>
                                        You must enter your current password to change it
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="newPassword" class="form-label">New Password <span class="required">*</span></label>
                                    <input type="password" id="newPassword" class="form-input" placeholder="Enter your new password" required>
                                    <div class="form-help">
                                        <i class="fas fa-info-circle"></i>
                                        Password must be at least 8 characters long
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="confirmPassword" class="form-label">Confirm New Password <span class="required">*</span></label>
                                    <input type="password" id="confirmPassword" class="form-input" placeholder="Confirm your new password" required>
                                </div>
                                
                                <div class="account-form__actions">
                                    <button type="submit" class="btn btn-primary">Update Password</button>
                                    <button type="button" class="btn btn-outline-secondary">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Manage Addresses -->
                    <div class="account-content account-content--hidden" id="manage-addresses">
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
                                
                                <div class="address-card">
                                    <div class="address-card__header">
                                        <span class="address-card__badge">Default</span>
                                        <div class="address-card__actions">
                                            <button class="address-card__action" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="address-card__action" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="address-card__body">
                                        <p class="address-card__name">John Doe</p>
                                        <p class="address-card__address">123 Main Street, Apt 4B</p>
                                        <p class="address-card__address">New York, NY 10001</p>
                                        <p class="address-card__address">United States</p>
                                        <p class="address-card__phone">Phone: +1 (555) 123-4567</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Shipping Address -->
                            <div class="address-section">
                                <h3 class="address-section__title">
                                    <i class="fas fa-truck"></i>
                                    Shipping Address
                                </h3>
                                
                                <div class="address-card">
                                    <div class="address-card__header">
                                        <span class="address-card__badge">Default</span>
                                        <div class="address-card__actions">
                                            <button class="address-card__action" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="address-card__action" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="address-card__body">
                                        <p class="address-card__name">John Doe</p>
                                        <p class="address-card__address">123 Main Street, Apt 4B</p>
                                        <p class="address-card__address">New York, NY 10001</p>
                                        <p class="address-card__address">United States</p>
                                        <p class="address-card__phone">Phone: +1 (555) 123-4567</p>
                                    </div>
                                </div>
                                
                                <div class="address-card">
                                    <div class="address-card__header">
                                        <div class="address-card__actions">
                                            <button class="address-card__action" title="Set as Default">
                                                <i class="fas fa-star"></i>
                                            </button>
                                            <button class="address-card__action" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="address-card__action" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="address-card__body">
                                        <p class="address-card__name">John Doe</p>
                                        <p class="address-card__address">456 Oak Avenue</p>
                                        <p class="address-card__address">Los Angeles, CA 90001</p>
                                        <p class="address-card__address">United States</p>
                                        <p class="address-card__phone">Phone: +1 (555) 987-6543</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Add Address Form (Hidden by default) -->
                            <div class="address-form-container" id="addressFormContainer" style="display: none;">
                                <div class="account-block">
                                    <h3 class="account-block__title">Add New Address</h3>
                                    
                                    <form class="account-form" id="addAddressForm">
                                        <div class="form-group">
                                            <label for="addressType" class="form-label">Address Type <span class="required">*</span></label>
                                            <select id="addressType" class="form-input" required>
                                                <option value="">Select Type</option>
                                                <option value="billing">Billing Address</option>
                                                <option value="shipping">Shipping Address</option>
                                            </select>
                                        </div>
                                        
                                        <div class="account-form__grid">
                                            <div class="form-group">
                                                <label for="addressFirstName" class="form-label">First Name <span class="required">*</span></label>
                                                <input type="text" id="addressFirstName" class="form-input" placeholder="Enter first name" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="addressLastName" class="form-label">Last Name <span class="required">*</span></label>
                                                <input type="text" id="addressLastName" class="form-input" placeholder="Enter last name" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="addressPhone" class="form-label">Phone Number <span class="required">*</span></label>
                                                <input type="tel" id="addressPhone" class="form-input" placeholder="Enter phone number" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="addressEmail" class="form-label">Email Address</label>
                                                <input type="email" id="addressEmail" class="form-input" placeholder="Enter email address">
                                            </div>
                                            <div class="form-group form-group--full">
                                                <label for="addressStreet" class="form-label">Street Address <span class="required">*</span></label>
                                                <input type="text" id="addressStreet" class="form-input" placeholder="Enter street address" required>
                                            </div>
                                            <div class="form-group form-group--full">
                                                <label for="addressStreet2" class="form-label">Apartment, suite, etc. (optional)</label>
                                                <input type="text" id="addressStreet2" class="form-input" placeholder="Apartment, suite, unit, building, floor, etc.">
                                            </div>
                                            <div class="form-group">
                                                <label for="addressCity" class="form-label">City <span class="required">*</span></label>
                                                <input type="text" id="addressCity" class="form-input" placeholder="Enter city" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="addressState" class="form-label">State / Province <span class="required">*</span></label>
                                                <input type="text" id="addressState" class="form-input" placeholder="Enter state" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="addressZip" class="form-label">ZIP / Postal Code <span class="required">*</span></label>
                                                <input type="text" id="addressZip" class="form-input" placeholder="Enter ZIP code" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="addressCountry" class="form-label">Country <span class="required">*</span></label>
                                                <select id="addressCountry" class="form-input" required>
                                                    <option value="">Select Country</option>
                                                    <option value="US" selected>United States</option>
                                                    <option value="UK">United Kingdom</option>
                                                    <option value="CA">Canada</option>
                                                    <option value="AU">Australia</option>
                                                    <option value="DE">Germany</option>
                                                    <option value="FR">France</option>
                                                    <option value="IT">Italy</option>
                                                    <option value="ES">Spain</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group form-group--checkbox">
                                            <label class="checkbox-label">
                                                <input type="checkbox" id="setAsDefault">
                                                <span class="checkmark"></span>
                                                Set as default address
                                            </label>
                                        </div>
                                        
                                        <div class="account-form__actions">
                                            <button type="submit" class="btn btn-primary">Save Address</button>
                                            <button type="button" class="btn btn-outline-secondary" id="cancelAddressBtn">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- My Orders -->
                    <div class="account-content account-content--hidden" id="my-orders">
                        <div class="account-block">
                            <h2 class="account-block__title">My Orders / Order History</h2>
                            
                            <div class="orders-list">
                                <div class="order-history-item">
                                    <div class="order-history-item__header">
                                        <div class="order-history-item__info">
                                            <div class="order-history-item__number">
                                                <strong>Order #</strong>
                                                <span>ORD-2024-001</span>
                                            </div>
                                            <div class="order-history-item__date">
                                                <i class="fas fa-calendar"></i>
                                                <span>March 20, 2024</span>
                                            </div>
                                            <div class="order-history-item__status">
                                                <span class="order-status order-status--delivered">Delivered</span>
                                            </div>
                                        </div>
                                        <div class="order-history-item__total">
                                            <strong>Total: $131.36</strong>
                                        </div>
                                    </div>
                                    <div class="order-history-item__body">
                                        <div class="order-history-item__products">
                                            <div class="order-product-mini">
                                                <img src="assets/images/product-1.jpg" alt="Premium Notebook">
                                                <span class="order-product-mini__name">Premium Notebook</span>
                                                <span class="order-product-mini__qty">x2</span>
                                            </div>
                                            <div class="order-product-mini">
                                                <img src="assets/images/product-2.jpg" alt="Professional Pen Set">
                                                <span class="order-product-mini__name">Professional Pen Set</span>
                                                <span class="order-product-mini__qty">x1</span>
                                            </div>
                                            <div class="order-product-mini">
                                                <img src="assets/images/product-3.jpg" alt="Art Supply Kit">
                                                <span class="order-product-mini__name">Art Supply Kit</span>
                                                <span class="order-product-mini__qty">x1</span>
                                            </div>
                                        </div>
                                        <div class="order-history-item__actions">
                                            <a href="#order-details-001" class="btn btn-outline-primary btn-sm view-order-details">
                                                <i class="fas fa-eye"></i>
                                                View Details
                                            </a>
                                            <button class="btn btn-outline-secondary btn-sm">
                                                <i class="fas fa-redo"></i>
                                                Reorder
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="order-history-item">
                                    <div class="order-history-item__header">
                                        <div class="order-history-item__info">
                                            <div class="order-history-item__number">
                                                <strong>Order #</strong>
                                                <span>ORD-2024-002</span>
                                            </div>
                                            <div class="order-history-item__date">
                                                <i class="fas fa-calendar"></i>
                                                <span>March 15, 2024</span>
                                            </div>
                                            <div class="order-history-item__status">
                                                <span class="order-status order-status--processing">Processing</span>
                                            </div>
                                        </div>
                                        <div class="order-history-item__total">
                                            <strong>Total: $89.99</strong>
                                        </div>
                                    </div>
                                    <div class="order-history-item__body">
                                        <div class="order-history-item__products">
                                            <div class="order-product-mini">
                                                <img src="assets/images/product-2.jpg" alt="Professional Pen Set">
                                                <span class="order-product-mini__name">Professional Pen Set</span>
                                                <span class="order-product-mini__qty">x2</span>
                                            </div>
                                            <div class="order-product-mini">
                                                <img src="assets/images/product-1.jpg" alt="Office Organizer">
                                                <span class="order-product-mini__name">Office Organizer</span>
                                                <span class="order-product-mini__qty">x1</span>
                                            </div>
                                        </div>
                                        <div class="order-history-item__actions">
                                            <a href="#order-details-002" class="btn btn-outline-primary btn-sm view-order-details">
                                                <i class="fas fa-eye"></i>
                                                View Details
                                            </a>
                                            <button class="btn btn-outline-secondary btn-sm">
                                                <i class="fas fa-times"></i>
                                                Cancel Order
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="order-history-item">
                                    <div class="order-history-item__header">
                                        <div class="order-history-item__info">
                                            <div class="order-history-item__number">
                                                <strong>Order #</strong>
                                                <span>ORD-2024-003</span>
                                            </div>
                                            <div class="order-history-item__date">
                                                <i class="fas fa-calendar"></i>
                                                <span>March 10, 2024</span>
                                            </div>
                                            <div class="order-history-item__status">
                                                <span class="order-status order-status--delivered">Delivered</span>
                                            </div>
                                        </div>
                                        <div class="order-history-item__total">
                                            <strong>Total: $45.99</strong>
                                        </div>
                                    </div>
                                    <div class="order-history-item__body">
                                        <div class="order-history-item__products">
                                            <div class="order-product-mini">
                                                <img src="assets/images/product-3.jpg" alt="Art Supply Kit">
                                                <span class="order-product-mini__name">Art Supply Kit</span>
                                                <span class="order-product-mini__qty">x1</span>
                                            </div>
                                        </div>
                                        <div class="order-history-item__actions">
                                            <a href="#order-details-003" class="btn btn-outline-primary btn-sm view-order-details">
                                                <i class="fas fa-eye"></i>
                                                View Details
                                            </a>
                                            <button class="btn btn-outline-secondary btn-sm">
                                                <i class="fas fa-redo"></i>
                                                Reorder
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Details -->
                    <div class="account-content account-content--hidden" id="order-details">
                        <div class="account-block">
                            <div class="account-block__header">
                                <div>
                                    <a href="#my-orders" class="back-link">
                                        <i class="fas fa-arrow-left"></i>
                                        Back to Orders
                                    </a>
                                    <h2 class="account-block__title">Order Details</h2>
                                </div>
                            </div>
                            
                            <div class="order-details">
                                <!-- Order Header Info -->
                                <div class="order-details__header">
                                    <div class="order-details__info-row">
                                        <div class="order-details__info-item">
                                            <span class="order-details__label">Order Number</span>
                                            <span class="order-details__value">ORD-2024-001</span>
                                        </div>
                                        <div class="order-details__info-item">
                                            <span class="order-details__label">Order Date</span>
                                            <span class="order-details__value">March 20, 2024</span>
                                        </div>
                                        <div class="order-details__info-item">
                                            <span class="order-details__label">Order Status</span>
                                            <span class="order-status order-status--delivered">Delivered</span>
                                        </div>
                                        <div class="order-details__info-item">
                                            <span class="order-details__label">Total Amount</span>
                                            <span class="order-details__value order-details__value--total">$131.36</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Order Items -->
                                <div class="order-details__section">
                                    <h3 class="order-details__section-title">Order Items</h3>
                                    <div class="order-details__items">
                                        <div class="order-details-item">
                                            <div class="order-details-item__image">
                                                <img src="assets/images/product-1.jpg" alt="Premium Notebook">
                                            </div>
                                            <div class="order-details-item__info">
                                                <h4 class="order-details-item__name">Premium Notebook</h4>
                                                <div class="order-details-item__meta">
                                                    <span>SKU: NB-001</span>
                                                    <span>Qty: 2</span>
                                                </div>
                                                <div class="order-details-item__price">
                                                    <span>$24.99 x 2 = $49.98</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="order-details-item">
                                            <div class="order-details-item__image">
                                                <img src="assets/images/product-2.jpg" alt="Professional Pen Set">
                                            </div>
                                            <div class="order-details-item__info">
                                                <h4 class="order-details-item__name">Professional Pen Set</h4>
                                                <div class="order-details-item__meta">
                                                    <span>SKU: PEN-002</span>
                                                    <span>Qty: 1</span>
                                                </div>
                                                <div class="order-details-item__price">
                                                    <span>$19.99 x 1 = $19.99</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="order-details-item">
                                            <div class="order-details-item__image">
                                                <img src="assets/images/product-3.jpg" alt="Art Supply Kit">
                                            </div>
                                            <div class="order-details-item__info">
                                                <h4 class="order-details-item__name">Art Supply Kit</h4>
                                                <div class="order-details-item__meta">
                                                    <span>SKU: ART-003</span>
                                                    <span>Qty: 1</span>
                                                </div>
                                                <div class="order-details-item__price">
                                                    <span>$45.99 x 1 = $45.99</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Order Summary -->
                                <div class="order-details__section">
                                    <h3 class="order-details__section-title">Order Summary</h3>
                                    <div class="order-details__summary">
                                        <div class="order-summary-row">
                                            <span class="order-summary-label">Subtotal</span>
                                            <span class="order-summary-value">$115.96</span>
                                        </div>
                                        <div class="order-summary-row">
                                            <span class="order-summary-label">Shipping</span>
                                            <span class="order-summary-value">$5.00</span>
                                        </div>
                                        <div class="order-summary-row">
                                            <span class="order-summary-label">Tax</span>
                                            <span class="order-summary-value">$10.40</span>
                                        </div>
                                        <div class="order-summary-row order-summary-row--total">
                                            <span class="order-summary-label">Total</span>
                                            <span class="order-summary-value">$131.36</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Shipping Address -->
                                <div class="order-details__section">
                                    <h3 class="order-details__section-title">Shipping Address</h3>
                                    <div class="order-details__address">
                                        <p class="order-details__address-name">John Doe</p>
                                        <p class="order-details__address-line">123 Main Street, Apt 4B</p>
                                        <p class="order-details__address-line">New York, NY 10001</p>
                                        <p class="order-details__address-line">United States</p>
                                        <p class="order-details__address-phone">Phone: +1 (555) 123-4567</p>
                                    </div>
                                </div>

                                <!-- Billing Address -->
                                <div class="order-details__section">
                                    <h3 class="order-details__section-title">Billing Address</h3>
                                    <div class="order-details__address">
                                        <p class="order-details__address-name">John Doe</p>
                                        <p class="order-details__address-line">123 Main Street, Apt 4B</p>
                                        <p class="order-details__address-line">New York, NY 10001</p>
                                        <p class="order-details__address-line">United States</p>
                                        <p class="order-details__address-phone">Phone: +1 (555) 123-4567</p>
                                    </div>
                                </div>

                                <!-- Tracking Info -->
                                <div class="order-details__section">
                                    <h3 class="order-details__section-title">Tracking Information</h3>
                                    <div class="order-tracking">
                                        <div class="order-tracking__item order-tracking__item--completed">
                                            <div class="order-tracking__icon">
                                                <i class="fas fa-check-circle"></i>
                                            </div>
                                            <div class="order-tracking__content">
                                                <div class="order-tracking__status">Order Placed</div>
                                                <div class="order-tracking__date">March 20, 2024 - 10:30 AM</div>
                                            </div>
                                        </div>
                                        <div class="order-tracking__item order-tracking__item--completed">
                                            <div class="order-tracking__icon">
                                                <i class="fas fa-check-circle"></i>
                                            </div>
                                            <div class="order-tracking__content">
                                                <div class="order-tracking__status">Order Confirmed</div>
                                                <div class="order-tracking__date">March 20, 2024 - 11:00 AM</div>
                                            </div>
                                        </div>
                                        <div class="order-tracking__item order-tracking__item--completed">
                                            <div class="order-tracking__icon">
                                                <i class="fas fa-check-circle"></i>
                                            </div>
                                            <div class="order-tracking__content">
                                                <div class="order-tracking__status">Shipped</div>
                                                <div class="order-tracking__date">March 21, 2024 - 2:15 PM</div>
                                                <div class="order-tracking__tracking-number">Tracking: TRK123456789</div>
                                            </div>
                                        </div>
                                        <div class="order-tracking__item order-tracking__item--completed">
                                            <div class="order-tracking__icon">
                                                <i class="fas fa-check-circle"></i>
                                            </div>
                                            <div class="order-tracking__content">
                                                <div class="order-tracking__status">Delivered</div>
                                                <div class="order-tracking__date">March 23, 2024 - 3:45 PM</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Order Actions -->
                                <div class="order-details__actions">
                                    <button class="btn btn-outline-primary">
                                        <i class="fas fa-download"></i>
                                        Download Invoice
                                    </button>
                                    <button class="btn btn-outline-secondary">
                                        <i class="fas fa-redo"></i>
                                        Reorder Items
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection