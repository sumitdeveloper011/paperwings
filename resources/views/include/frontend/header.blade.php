    <header class="header">
        <!-- Utility Bar -->
        <div class="header__utility-bar">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="header__utility-contact">
                            @php
                                $phone = isset($headerPhone) && $headerPhone ? $headerPhone : '(+880) 123 4567';
                                $email = isset($headerEmail) && $headerEmail ? $headerEmail : 'info@paperwings.com';
                            @endphp
                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $phone) }}" class="header__utility-item">
                                <i class="fas fa-phone"></i>
                                <span>{{ $phone }}</span>
                            </a>
                            <a href="mailto:{{ $email }}" class="header__utility-item">
                                <i class="fas fa-envelope"></i>
                                <span>{{ $email }}</span>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        @if(isset($socialLinks['facebook']))
                            <a href="{{ $socialLinks['facebook'] }}" target="_blank" rel="noopener noreferrer" class="header__utility-link">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                        @endif
                        @if(isset($socialLinks['twitter']))
                            <a href="{{ $socialLinks['twitter'] }}" target="_blank" rel="noopener noreferrer" class="header__utility-link">
                                <i class="fab fa-twitter"></i>
                            </a>
                        @endif
                        @if(isset($socialLinks['instagram']))
                            <a href="{{ $socialLinks['instagram'] }}" target="_blank" rel="noopener noreferrer" class="header__utility-link">
                                <i class="fab fa-instagram"></i>
                            </a>
                        @endif
                        @if(isset($socialLinks['linkedin']))
                            <a href="{{ $socialLinks['linkedin'] }}" target="_blank" rel="noopener noreferrer" class="header__utility-link">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                        @endif
                        @if(isset($socialLinks['youtube']))
                            <a href="{{ $socialLinks['youtube'] }}" target="_blank" rel="noopener noreferrer" class="header__utility-link">
                                <i class="fab fa-youtube"></i>
                            </a>
                        @endif
                        @if(isset($socialLinks['pinterest']))
                            <a href="{{ $socialLinks['pinterest'] }}" target="_blank" rel="noopener noreferrer" class="header__utility-link">
                                <i class="fab fa-pinterest"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Navigation Bar -->
        <div class="header__main">
            <div class="container">
                <!-- Row 1: Hamburger (Mobile) + Logo + Search + Actions -->
                <div class="row align-items-center header__main-row-1">
                    <!-- Mobile Hamburger Menu Button (Left Side) -->
                    <div class="col-2 d-md-none">
                        <button type="button" class="header__mobile-nav-btn" id="navMobileMenuToggle" aria-label="Toggle mobile menu">
                            <i class="fas fa-bars"></i>
                        </button>
                    </div>

                    <!-- Logo -->
                    <div class="col-md-3 col-4">
                        <div class="header__logo-wrapper">
                            <a href="{{ route('home') }}" class="header__logo">
                                @if(isset($headerLogo) && $headerLogo)
                                    <img src="{{ asset('storage/' . $headerLogo) }}" alt="{{ config('app.name', 'Paper Wings') }} Logo" class="header__logo-image" onerror="this.src='{{ asset('assets/frontend/images/logo.png') }}'">
                                @else
                                    <img src="{{ asset('assets/frontend/images/logo.png') }}" alt="{{ config('app.name', 'Paper Wings') }} Logo" class="header__logo-image">
                                @endif
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6 d-none d-md-block">
                        <div class="header__search position-relative" id="header-search">
                            <input type="text"
                                   class="header__search-input"
                                   id="header-search-input"
                                   placeholder="Search products..."
                                   autocomplete="off"
                                   aria-label="Search products">
                            <button type="button" class="header__search-btn" id="header-search-btn">
                                <i class="fas fa-search"></i>
                            </button>
                            <div id="search-results-dropdown" class="search-results-dropdown" style="display: none;">
                                <div class="search-results-loading" id="search-loading" style="display: none;">
                                    <i class="fas fa-spinner fa-spin"></i> Searching...
                                </div>
                                <div class="search-results-list" id="search-results-list"></div>
                                <div class="search-results-footer" id="search-results-footer" style="display: none;">
                                    <a href="#" id="view-all-results" class="view-all-results-btn">
                                        View All Results
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="header__actions">
                            <!-- Desktop Actions -->
                            <div class="header__actions-desktop d-none d-md-flex">
                                @if(Auth::check() && isset($userData))
                                <div class="header__user-dropdown" id="userDropdown">
                                    <a href="#" class="header__user-trigger" id="userDropdownTrigger">
                                        <div class="header__user-avatar" id="userAvatar">
                                            @if($userData['has_avatar'])
                                                <img src="{{ $userData['avatar_url'] }}" alt="{{ $userData['name'] }}" id="userAvatarImg" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                                                <span id="userAvatarInitial" style="display: none;">{{ $userData['initial'] }}</span>
                                            @else
                                                <span id="userAvatarInitial">{{ $userData['initial'] }}</span>
                                            @endif
                                        </div>
                                        <span class="header__user-name" id="userName">{{ $userData['name'] }}</span>
                                        <i class="fas fa-chevron-down header__user-arrow"></i>
                                    </a>
                                    <div class="header__user-menu" id="userMenu">
                                        <div class="header__user-menu-header">
                                            <div class="header__user-menu-avatar" id="userMenuAvatar">
                                                @if($userData['has_avatar'])
                                                    <img src="{{ $userData['avatar_url'] }}" alt="{{ $userData['name'] }}" id="userMenuAvatarImg" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                                                    <span id="userMenuAvatarInitial" style="display: none;">{{ $userData['initial'] }}</span>
                                                @else
                                                    <span id="userMenuAvatarInitial">{{ $userData['initial'] }}</span>
                                                @endif
                                            </div>
                                            <div class="header__user-menu-info">
                                                <p class="header__user-menu-name" id="userMenuName">{{ $userData['name'] }}</p>
                                                <p class="header__user-menu-email" id="userMenuEmail">{{ $userData['email'] }}</p>
                                            </div>
                                        </div>
                                        <ul class="header__user-menu-items">
                                            <li class="header__user-menu-item">
                                                <a href="{{ route('account.view-profile') }}" class="header__user-menu-link">
                                                    <i class="fas fa-user"></i>
                                                    <span>My Account</span>
                                                </a>
                                            </li>
                                            <li class="header__user-menu-item">
                                                <a href="{{ route('account.my-orders') }}" class="header__user-menu-link">
                                                    <i class="fas fa-shopping-bag"></i>
                                                    <span>Orders</span>
                                                </a>
                                            </li>
                                            <li class="header__user-menu-item header__user-menu-item--logout">
                                                <form method="POST" action="{{ route('logout') }}" id="logoutForm">
                                                    @csrf
                                                    <button type="submit" class="header__user-menu-link" id="logoutLink">
                                                        <i class="fas fa-sign-out-alt"></i>
                                                        <span>Logout</span>
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                @else
                                <a href="{{ route('login') }}" class="header__login" id="loginLink">LOGIN</a>
                                <span class="header__divider" id="headerDivider">/</span>
                                <a href="{{ route('register') }}" class="header__login header__login--register" id="registerLink">REGISTER</a>
                                @endif
                                <a href="#" class="header__icon wishlist-trigger">
                                    <i class="far fa-heart"></i>
                                    <span class="header__badge" id="wishlist-header-badge">0</span>
                                </a>
                                <a href="#" class="header__icon cart-trigger" id="cart-trigger">
                                    <i class="fas fa-shopping-cart"></i>
                                    <span class="header__badge" id="cart-header-badge">0</span>
                                </a>
                            </div>

                            <!-- Mobile Actions (Icons Only) -->
                            <div class="header__actions-mobile d-flex d-md-none">
                                <!-- Search Icon Button (Mobile) -->
                                <button type="button" class="header__icon header__search-toggle" id="mobileSearchToggle" aria-label="Toggle search">
                                    <i class="fas fa-search"></i>
                                </button>

                                <!-- User Icon Button (Mobile) - For Login/Register or User Menu -->
                                @if(Auth::check() && isset($userData))
                                <div class="header__user-dropdown" id="userDropdownMobile">
                                    <a href="#" class="header__user-trigger header__user-trigger--mobile" id="userDropdownTriggerMobile">
                                        <div class="header__user-avatar" id="userAvatarMobile">
                                            @if($userData['has_avatar'])
                                                <img src="{{ $userData['avatar_url'] }}" alt="{{ $userData['name'] }}" id="userAvatarImgMobile" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                                                <span id="userAvatarInitialMobile" style="display: none;">{{ $userData['initial'] }}</span>
                                            @else
                                                <span id="userAvatarInitialMobile">{{ $userData['initial'] }}</span>
                                            @endif
                                        </div>
                                    </a>
                                    <div class="header__user-menu" id="userMenuMobile">
                                        <div class="header__user-menu-header">
                                            <div class="header__user-menu-avatar" id="userMenuAvatarMobile">
                                                @if($userData['has_avatar'])
                                                    <img src="{{ $userData['avatar_url'] }}" alt="{{ $userData['name'] }}" id="userMenuAvatarImgMobile" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                                                    <span id="userMenuAvatarInitialMobile" style="display: none;">{{ $userData['initial'] }}</span>
                                                @else
                                                    <span id="userMenuAvatarInitialMobile">{{ $userData['initial'] }}</span>
                                                @endif
                                            </div>
                                            <div class="header__user-menu-info">
                                                <p class="header__user-menu-name" id="userMenuNameMobile">{{ $userData['name'] }}</p>
                                                <p class="header__user-menu-email" id="userMenuEmailMobile">{{ $userData['email'] }}</p>
                                            </div>
                                        </div>
                                        <ul class="header__user-menu-items">
                                            <li class="header__user-menu-item">
                                                <a href="{{ route('account.view-profile') }}" class="header__user-menu-link">
                                                    <i class="fas fa-user"></i>
                                                    <span>My Account</span>
                                                </a>
                                            </li>
                                            <li class="header__user-menu-item">
                                                <a href="{{ route('account.my-orders') }}" class="header__user-menu-link">
                                                    <i class="fas fa-shopping-bag"></i>
                                                    <span>Orders</span>
                                                </a>
                                            </li>
                                            <li class="header__user-menu-item header__user-menu-item--logout">
                                                <form method="POST" action="{{ route('logout') }}" id="logoutFormMobile">
                                                    @csrf
                                                    <button type="submit" class="header__user-menu-link" id="logoutLinkMobile">
                                                        <i class="fas fa-sign-out-alt"></i>
                                                        <span>Logout</span>
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                @else
                                <!-- User Icon for Login/Register Dropdown (Mobile) -->
                                <div class="header__user-dropdown" id="userLoginDropdownMobile">
                                    <button type="button" class="header__icon header__user-login-toggle" id="userLoginToggleMobile" aria-label="User menu">
                                        <i class="fas fa-user"></i>
                                    </button>
                                    <div class="header__user-menu header__user-menu--login" id="userLoginMenuMobile">
                                        <ul class="header__user-menu-items">
                                            <li class="header__user-menu-item">
                                                <a href="{{ route('login') }}" class="header__user-menu-link">
                                                    <i class="fas fa-sign-in-alt"></i>
                                                    <span>Login</span>
                                                </a>
                                            </li>
                                            <li class="header__user-menu-item">
                                                <a href="{{ route('register') }}" class="header__user-menu-link">
                                                    <i class="fas fa-user-plus"></i>
                                                    <span>Register</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                @endif

                                <a href="#" class="header__icon wishlist-trigger">
                                    <i class="far fa-heart"></i>
                                    <span class="header__badge" id="wishlist-header-badge-mobile">0</span>
                                </a>
                                <a href="#" class="header__icon cart-trigger" id="cart-trigger-mobile">
                                    <i class="fas fa-shopping-cart"></i>
                                    <span class="header__badge" id="cart-header-badge-mobile">0</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Row 2: Expandable Search Bar (Mobile Only) -->
                <div class="row d-md-none header__main-row-2" id="mobileSearchRow" style="display: none;">
                    <div class="col-12">
                        <div class="header__search position-relative" id="header-search-mobile">
                            <input type="text"
                                   class="header__search-input"
                                   id="header-search-input-mobile"
                                   placeholder="Search products..."
                                   autocomplete="off"
                                   aria-label="Search products">
                            <button type="button" class="header__search-btn" id="header-search-btn-mobile">
                                <i class="fas fa-search"></i>
                            </button>
                            <button type="button" class="header__search-close" id="mobileSearchClose" aria-label="Close search">
                                <i class="fas fa-times"></i>
                            </button>
                            <div id="search-results-dropdown-mobile" class="search-results-dropdown" style="display: none;">
                                <div class="search-results-loading" id="search-loading-mobile" style="display: none;">
                                    <i class="fas fa-spinner fa-spin"></i> Searching...
                                </div>
                                <div class="search-results-list" id="search-results-list-mobile"></div>
                                <div class="search-results-footer" id="search-results-footer-mobile" style="display: none;">
                                    <a href="#" id="view-all-results-mobile" class="view-all-results-btn">
                                        View All Results
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Section -->
        <nav class="nav-section">
            <div class="container">
                <div class="row align-items-center">
                    <!-- Desktop Categories Dropdown -->
                    <div class="col-md-3 d-none d-md-block">
                        <div class="nav__categories-dropdown mega-menu-wrapper">
                            <button type="button" class="nav__categories-btn" id="megaMenuTrigger">
                                <i class="fas fa-bars"></i>
                                Browse Categories
                                <i class="fas fa-chevron-down ms-2 mega-menu-arrow"></i>
                            </button>
                            <div class="mega-menu" id="megaMenu" style="display: none;">
                                <div class="mega-menu__container">
                                    @if(isset($headerCategories) && $headerCategories->count() > 0)
                                        <div class="mega-menu__content">
                                            <div class="mega-menu__columns">
                                                @php
                                                    $categoriesPerColumn = ceil($headerCategories->count() / 4);
                                                    $columns = $headerCategories->chunk($categoriesPerColumn);
                                                @endphp
                                                @foreach($columns as $columnCategories)
                                                    <div class="mega-menu__column">
                                                        <ul class="mega-menu__list">
                                                            @foreach($columnCategories as $category)
                                                                <li class="mega-menu__item">
                                                                    <a href="{{ route('category.show', $category->slug) }}" class="mega-menu__link">
                                                                        <span class="mega-menu__link-name">{{ $category->name }}</span>
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="mega-menu__footer">
                                            <a href="{{ route('shop') }}" class="mega-menu__view-all">
                                                View All Categories
                                                <i class="fas fa-arrow-right ms-2"></i>
                                            </a>
                                        </div>
                                    @else
                                        <div class="mega-menu__empty">
                                            <p class="mega-menu__empty-text">No categories available</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Desktop Navigation Menu -->
                    <div class="col-md-9 d-none d-md-block">
                        <ul class="nav__menu">
                            <li class="nav__item">
                                <a href="{{ route('home') }}" class="nav__link {{ request()->routeIs('home') ? 'active' : '' }}">
                                    Home
                                </a>
                            </li>
                            <li class="nav__item">
                                <a href="{{ route('shop') }}" class="nav__link {{ request()->routeIs('shop') ? 'active' : '' }}">
                                    Shop
                                </a>
                            </li>
                            <li class="nav__item">
                                <a href="{{ route('bundles.index') }}" class="nav__link {{ request()->routeIs('bundles*') || request()->routeIs('bundle*') ? 'active' : '' }}">
                                    Bundles
                                </a>
                            </li>
                            <li class="nav__item">
                                <a href="{{ route('contact') }}" class="nav__link {{ request()->routeIs('contact*') ? 'active' : '' }}">Contact</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Mobile Slide-In Menu (From Left) -->
        <div class="nav__mobile-menu-overlay" id="navMobileMenuOverlay"></div>
        <div class="nav__mobile-menu" id="navMobileMenu">
            <div class="nav__mobile-menu-header">
                <h3 class="nav__mobile-menu-title">Menu</h3>
                <button type="button" class="nav__mobile-menu-close" id="navMobileMenuClose" aria-label="Close menu">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="nav__mobile-menu-content">
                <!-- Navigation Links -->
                <ul class="nav__mobile-menu-list">
                    <li class="nav__mobile-menu-item">
                        <a href="{{ route('home') }}" class="nav__mobile-menu-link {{ request()->routeIs('home') ? 'active' : '' }}">
                            <i class="fas fa-home"></i>
                            <span>Home</span>
                        </a>
                    </li>
                    <li class="nav__mobile-menu-item">
                        <a href="{{ route('shop') }}" class="nav__mobile-menu-link {{ request()->routeIs('shop') ? 'active' : '' }}">
                            <i class="fas fa-shopping-bag"></i>
                            <span>Shop</span>
                        </a>
                    </li>
                    <li class="nav__mobile-menu-item">
                        <a href="{{ route('bundles.index') }}" class="nav__mobile-menu-link {{ request()->routeIs('bundles*') || request()->routeIs('bundle*') ? 'active' : '' }}">
                            <i class="fas fa-box"></i>
                            <span>Bundles</span>
                        </a>
                    </li>
                    <li class="nav__mobile-menu-item">
                        <a href="{{ route('contact') }}" class="nav__mobile-menu-link {{ request()->routeIs('contact*') ? 'active' : '' }}">
                            <i class="fas fa-envelope"></i>
                            <span>Contact</span>
                        </a>
                    </li>
                </ul>

                <!-- Categories Section -->
                <div class="nav__mobile-menu-categories">
                    <h4 class="nav__mobile-menu-categories-title">
                        <i class="fas fa-th-large"></i>
                        Categories
                    </h4>
                    @if(isset($headerCategories) && $headerCategories->count() > 0)
                        <ul class="nav__mobile-menu-categories-list">
                            @foreach($headerCategories as $category)
                                <li class="nav__mobile-menu-categories-item">
                                    <a href="{{ route('category.show', $category->slug) }}" class="nav__mobile-menu-categories-link">
                                        <span>{{ $category->name }}</span>
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        <div class="nav__mobile-menu-footer">
                            <a href="{{ route('shop') }}" class="nav__mobile-menu-view-all">
                                View All Categories
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    @else
                        <p class="nav__mobile-menu-empty">No categories available</p>
                    @endif
                </div>
            </div>
        </div>
    </header>

