<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <img src="{{ $siteLogo }}" alt="{{ $siteName }}" class="sidebar-logo" onerror="this.src='{{ asset('assets/images/logo.svg') }}'">
            <div class="sidebar-brand-text">
                <h3>{{ $siteName }}</h3>
                <span>Admin Panel</span>
            </div>
        </div>
        <button class="sidebar-close" id="sidebarClose">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <nav class="sidebar-nav">
        <ul class="sidebar-menu">
            <li class="sidebar-item">
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" data-tooltip="Dashboard">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="sidebar-item sidebar-item--has-submenu {{ request()->routeIs('admin.categories.*') || request()->routeIs('admin.products.*') || request()->routeIs('admin.sliders.*') || request()->routeIs('admin.pages.*') || request()->routeIs('admin.about-sections.*') || request()->routeIs('admin.bundles.*') ? 'active' : '' }}">
                <a href="#" class="sidebar-link sidebar-link--has-submenu" data-tooltip="Content Management">
                    <i class="fas fa-folder-open"></i>
                    <span>Content Management</span>
                    <i class="fas fa-chevron-down sidebar-submenu-toggle"></i>
                </a>
                <ul class="sidebar-submenu">
                    <li class="sidebar-submenu-item">
                        <a href="{{ route('admin.categories.index') }}" class="sidebar-submenu-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                            <i class="fas fa-tags"></i>
                            <span>Categories</span>
                        </a>
                    </li>
                    <li class="sidebar-submenu-item">
                        <a href="{{ route('admin.products.index') }}" class="sidebar-submenu-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                            <i class="fas fa-box"></i>
                            <span>Products</span>
                        </a>
                    </li>
                    <li class="sidebar-submenu-item">
                        <a href="{{ route('admin.bundles.index') }}" class="sidebar-submenu-link {{ request()->routeIs('admin.bundles.*') ? 'active' : '' }}">
                            <i class="fas fa-boxes"></i>
                            <span>Product Bundles</span>
                        </a>
                    </li>
                    <li class="sidebar-submenu-item">
                        <a href="{{ route('admin.sliders.index') }}" class="sidebar-submenu-link {{ request()->routeIs('admin.sliders.*') ? 'active' : '' }}">
                            <i class="fas fa-images"></i>
                            <span>Sliders</span>
                        </a>
                    </li>
                    <li class="sidebar-submenu-item">
                        <a href="{{ route('admin.pages.index') }}" class="sidebar-submenu-link {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}">
                            <i class="fas fa-file-alt"></i>
                            <span>Pages</span>
                        </a>
                    </li>
                    <li class="sidebar-submenu-item">
                        <a href="{{ route('admin.about-sections.index') }}" class="sidebar-submenu-link {{ request()->routeIs('admin.about-sections.*') ? 'active' : '' }}">
                            <i class="fas fa-info-circle"></i>
                            <span>About Section</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="sidebar-item sidebar-item--has-submenu {{ request()->routeIs('admin.coupons.*') || request()->routeIs('admin.testimonials.*') || request()->routeIs('admin.special-offers-banners.*') || request()->routeIs('admin.faqs.*') ? 'active' : '' }}">
                <a href="#" class="sidebar-link sidebar-link--has-submenu" data-tooltip="Marketing">
                    <i class="fas fa-bullhorn"></i>
                    <span>Marketing</span>
                    <i class="fas fa-chevron-down sidebar-submenu-toggle"></i>
                </a>
                <ul class="sidebar-submenu">
                    <li class="sidebar-submenu-item">
                        <a href="{{ route('admin.coupons.index') }}" class="sidebar-submenu-link {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
                            <i class="fas fa-ticket-alt"></i>
                            <span>Coupons</span>
                        </a>
                    </li>
                    <li class="sidebar-submenu-item">
                        <a href="{{ route('admin.testimonials.index') }}" class="sidebar-submenu-link {{ request()->routeIs('admin.testimonials.*') ? 'active' : '' }}">
                            <i class="fas fa-star"></i>
                            <span>Testimonials</span>
                        </a>
                    </li>
                    <li class="sidebar-submenu-item">
                        <a href="{{ route('admin.special-offers-banners.index') }}" class="sidebar-submenu-link {{ request()->routeIs('admin.special-offers-banners.*') ? 'active' : '' }}">
                            <i class="fas fa-bullhorn"></i>
                            <span>Special Offers</span>
                        </a>
                    </li>
                    <li class="sidebar-submenu-item">
                        <a href="{{ route('admin.faqs.index') }}" class="sidebar-submenu-link {{ request()->routeIs('admin.faqs.*') ? 'active' : '' }}">
                            <i class="fas fa-question-circle"></i>
                            <span>FAQs</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="sidebar-item sidebar-item--has-submenu {{ request()->routeIs('admin.product-faqs.*') || request()->routeIs('admin.tags.*') || request()->routeIs('admin.questions.*') ? 'active' : '' }}">
                <a href="#" class="sidebar-link sidebar-link--has-submenu" data-tooltip="Product Features">
                    <i class="fas fa-cube"></i>
                    <span>Product Features</span>
                    <i class="fas fa-chevron-down sidebar-submenu-toggle"></i>
                </a>
                <ul class="sidebar-submenu">
                    <li class="sidebar-submenu-item">
                        <a href="{{ route('admin.product-faqs.index') }}" class="sidebar-submenu-link {{ request()->routeIs('admin.product-faqs.*') ? 'active' : '' }}">
                            <i class="fas fa-question"></i>
                            <span>Product FAQs</span>
                        </a>
                    </li>
                    <li class="sidebar-submenu-item">
                        <a href="{{ route('admin.tags.index') }}" class="sidebar-submenu-link {{ request()->routeIs('admin.tags.*') ? 'active' : '' }}">
                            <i class="fas fa-tags"></i>
                            <span>Tags</span>
                        </a>
                    </li>
                    <li class="sidebar-submenu-item">
                        <a href="{{ route('admin.questions.index') }}" class="sidebar-submenu-link {{ request()->routeIs('admin.questions.*') ? 'active' : '' }}">
                            <i class="fas fa-comments"></i>
                            <span>Questions & Answers</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="sidebar-item sidebar-item--has-submenu {{ request()->routeIs('admin.orders.*') || request()->routeIs('admin.users.*') || request()->routeIs('admin.subscriptions.*') ? 'active' : '' }}">
                <a href="#" class="sidebar-link sidebar-link--has-submenu" data-tooltip="Orders & Customers">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Orders & Customers</span>
                    <i class="fas fa-chevron-down sidebar-submenu-toggle"></i>
                </a>
                <ul class="sidebar-submenu">
                    <li class="sidebar-submenu-item">
                        <a href="{{ route('admin.orders.index') }}" class="sidebar-submenu-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Orders</span>
                        </a>
                    </li>
                    <li class="sidebar-submenu-item">
                        <a href="{{ route('admin.users.index') }}" class="sidebar-submenu-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <i class="fas fa-users"></i>
                            <span>Users</span>
                        </a>
                    </li>
                    <li class="sidebar-submenu-item">
                        <a href="{{ route('admin.subscriptions.index') }}" class="sidebar-submenu-link {{ request()->routeIs('admin.subscriptions.*') ? 'active' : '' }}">
                            <i class="fas fa-envelope"></i>
                            <span>Subscriptions</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="sidebar-item sidebar-item--has-submenu {{ request()->routeIs('admin.regions.*') || request()->routeIs('admin.shipping-prices.*') ? 'active' : '' }}">
                <a href="#" class="sidebar-link sidebar-link--has-submenu" data-tooltip="Shipping">
                    <i class="fas fa-shipping-fast"></i>
                    <span>Shipping</span>
                    <i class="fas fa-chevron-down sidebar-submenu-toggle"></i>
                </a>
                <ul class="sidebar-submenu">
                    <li class="sidebar-submenu-item">
                        <a href="{{ route('admin.regions.index') }}" class="sidebar-submenu-link {{ request()->routeIs('admin.regions.*') ? 'active' : '' }}">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Regions</span>
                        </a>
                    </li>
                    <li class="sidebar-submenu-item">
                        <a href="{{ route('admin.shipping-prices.index') }}" class="sidebar-submenu-link {{ request()->routeIs('admin.shipping-prices.*') ? 'active' : '' }}">
                            <i class="fas fa-shipping-fast"></i>
                            <span>Shipping Prices</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="sidebar-item">
                <a href="{{ route('admin.contacts.index') }}" class="sidebar-link {{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}" data-tooltip="Contact Messages">
                    <i class="fas fa-envelope"></i>
                    <span>Contact Messages</span>
                </a>
            </li>

            <li class="sidebar-item">
                <a href="{{ route('admin.reviews.index') }}" class="sidebar-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}" data-tooltip="Reviews">
                    <i class="fas fa-star-half-alt"></i>
                    <span>Reviews</span>
                </a>
            </li>

            <li class="sidebar-item sidebar-item--has-submenu {{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}">
                <a href="#" class="sidebar-link sidebar-link--has-submenu" data-tooltip="Analytics & Reports">
                    <i class="fas fa-chart-line"></i>
                    <span>Analytics & Reports</span>
                    <i class="fas fa-chevron-down sidebar-submenu-toggle"></i>
                </a>
                <ul class="sidebar-submenu">
                    <li class="sidebar-submenu-item">
                        <a href="{{ route('admin.analytics.index') }}" class="sidebar-submenu-link {{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}">
                            <i class="fas fa-chart-line"></i>
                            <span>Analytics</span>
                        </a>
                    </li>
                    @php
                        $gaSettings = \App\Models\Setting::whereIn('key', ['google_analytics_id', 'google_analytics_enabled'])
                            ->pluck('value', 'key')
                            ->toArray();
                        $gaId = $gaSettings['google_analytics_id'] ?? '';
                        $gaEnabled = isset($gaSettings['google_analytics_enabled']) && $gaSettings['google_analytics_enabled'] == '1';
                    @endphp
                    @if($gaEnabled && !empty($gaId))
                    <li class="sidebar-submenu-item">
                        <a href="https://analytics.google.com" target="_blank" class="sidebar-submenu-link">
                            <i class="fab fa-google"></i>
                            <span>Google Analytics</span>
                            <i class="fas fa-external-link-alt sidebar-link__external"></i>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>

            <li class="sidebar-item sidebar-item--has-submenu {{ request()->routeIs('admin.roles.*') || request()->routeIs('admin.permissions.*') ? 'active' : '' }}">
                <a href="#" class="sidebar-link sidebar-link--has-submenu" data-tooltip="Roles & Permissions">
                    <i class="fas fa-shield-alt"></i>
                    <span>Roles & Permissions</span>
                    <i class="fas fa-chevron-down sidebar-submenu-toggle"></i>
                </a>
                <ul class="sidebar-submenu">
                    <li class="sidebar-submenu-item">
                        <a href="{{ route('admin.roles.index') }}" class="sidebar-submenu-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                            <i class="fas fa-user-tag"></i>
                            <span>Roles</span>
                        </a>
                    </li>
                    <li class="sidebar-submenu-item">
                        <a href="{{ route('admin.permissions.index') }}" class="sidebar-submenu-link {{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}">
                            <i class="fas fa-key"></i>
                            <span>Permissions</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="sidebar-item sidebar-item--has-submenu {{ request()->routeIs('admin.settings.*') || request()->routeIs('admin.api-settings.*') ? 'active' : '' }}">
                <a href="#" class="sidebar-link sidebar-link--has-submenu" data-tooltip="Settings">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                    <i class="fas fa-chevron-down sidebar-submenu-toggle"></i>
                </a>
                <ul class="sidebar-submenu">
                    <li class="sidebar-submenu-item">
                        <a href="{{ route('admin.settings.index') }}" class="sidebar-submenu-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                            <i class="fas fa-cog"></i>
                            <span>Site Settings</span>
                        </a>
                    </li>
                    <li class="sidebar-submenu-item">
                        <a href="{{ route('admin.api-settings.index') }}" class="sidebar-submenu-link {{ request()->routeIs('admin.api-settings.*') ? 'active' : '' }}">
                            <i class="fas fa-key"></i>
                            <span>API Settings</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
</aside>
