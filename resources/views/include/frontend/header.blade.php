    <header class="header">
        <!-- Utility Bar -->
        <div class="header__utility-bar">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <div class="header__utility-contact">
                            @if(isset($headerPhone) && $headerPhone)
                                Call: {{ $headerPhone }}
                            @else
                                Call: (+880) 123 4567
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <!-- Empty center column -->
                    </div>
                    <div class="col-md-4 text-end">
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
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <a href="{{ route('home') }}" class="header__logo">
                            <div class="header__logo-icon">
                                <i class="fas fa-shopping-bag" style="font-size: 2rem; color: var(--lavender);"></i>
                            </div>
                            <h1 class="header__logo-text">Paper Wings</h1>
                        </a>
                    </div>
                    <div class="col-md-6">
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
                    <div class="col-md-3">
                        <div class="header__actions">
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
                                <span class="header__badge" id="wishlist-header-badge">{{ auth()->check() ? auth()->user()->wishlists->count() : 0 }}</span>
                            </a>
                            <a href="#" class="header__icon cart-trigger" id="cart-trigger">
                                <i class="fas fa-shopping-cart"></i>
                                <span class="header__badge" id="cart-header-badge" style="display: absolute;">0</span>
                            </a>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Section -->
        <nav class="nav-section">
            <div class="container">
                <div class="row align-items-center">
                                    <div class="col-md-3">
                    <div class="nav__categories-dropdown">
                        <button class="nav__categories-btn">
                            <i class="fas fa-bars"></i>
                            Browse Categories
                        </button>
                        <div class="nav__categories-menu">
                            <ul class="nav__categories-list">
                                @if(isset($headerCategories) && $headerCategories->count() > 0)
                                    @foreach($headerCategories as $category)
                                        <li class="nav__category-item">
                                            <a href="{{ route('product.by.category', $category->slug) }}" class="nav__category-link">
                                                <i class="fas fa-tag"></i>
                                                <span>{{ $category->name }}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                @else
                                    <li class="nav__category-item">
                                        <a href="#" class="nav__category-link">
                                            <i class="fas fa-tag"></i>
                                            <span>No Categories Available</span>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
                    <div class="col-md-9">
                        <ul class="nav__menu">
                            <li class="nav__item">
                                <a href="#" class="nav__link">
                                    Home
                                </a>
                            </li>
                            <li class="nav__item">
                                <a href="#" class="nav__link">
                                    Shop
                                </a>
                            </li>
                            <li class="nav__item">
                                <a href="#" class="nav__link">
                                    Pages
                                </a>
                            </li>
                            <li class="nav__item">
                                <a href="#" class="nav__link">Blog</a>
                            </li>
                            <li class="nav__item">
                                <a href="#" class="nav__link">Contact</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
    </header>
