<header class="header">
        <!-- Utility Bar -->
        <div class="header__utility-bar">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <div class="header__utility-contact">
                            Call: (+880) 123 4567
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <!-- Empty center column -->
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="#" class="header__utility-link">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="header__utility-link">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="header__utility-link">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="header__utility-link">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Navigation Bar -->
        <div class="header__main">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <a href="#" class="header__logo">
                            <div class="header__logo-icon">
                                <i class="fas fa-shopping-bag" style="font-size: 2rem; color: var(--lavender);"></i>
                            </div>
                            <h1 class="header__logo-text">stationero</h1>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <div class="header__search position-relative">
                            <input type="text" class="header__search-input" placeholder="Search">
                            <button class="header__search-btn">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="header__actions text-end">
                            @if(Auth::check())
                            <div class="header__user-dropdown" id="userDropdown">
                                <a href="#" class="header__user-trigger" id="userDropdownTrigger">
                                    <div class="header__user-avatar" id="userAvatar">
                                        <span id="userAvatarInitial">JD</span>
                                    </div>
                                    <span class="header__user-name" id="userName">John Doe</span>
                                    <i class="fas fa-chevron-down header__user-arrow"></i>
                                </a>
                                <div class="header__user-menu" id="userMenu">
                                    <div class="header__user-menu-header">
                                        <div class="header__user-menu-avatar" id="userMenuAvatar">
                                            <span id="userMenuAvatarInitial">JD</span>
                                        </div>
                                        <div class="header__user-menu-info">
                                            <p class="header__user-menu-name" id="userMenuName">John Doe</p>
                                            <p class="header__user-menu-email" id="userMenuEmail">john.doe@example.com</p>
                                        </div>
                                    </div>
                                    <ul class="header__user-menu-items">
                                        <li class="header__user-menu-item">
                                            <a href="account.html" class="header__user-menu-link">
                                                <i class="fas fa-user"></i>
                                                <span>My Account</span>
                                            </a>
                                        </li>
                                        <li class="header__user-menu-item">
                                            <a href="account.html#orders" class="header__user-menu-link">
                                                <i class="fas fa-shopping-bag"></i>
                                                <span>Orders</span>
                                            </a>
                                        </li>
                                        <li class="header__user-menu-item header__user-menu-item--logout">
                                            <a href="{{ route('logout') }}" class="header__user-menu-link" id="logoutLink">
                                                <i class="fas fa-sign-out-alt"></i>
                                                <span>Logout</span>
                                            </a>
                                            <form method="POST" action="{{ route('logout') }}" class="header__user-menu-link" id="logoutLink">
                                                @csrf
                                                <button type="submit" class="header__user-menu-link">
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
                                <span class="header__badge">2</span>
                            </a>
                            <a href="#" class="header__icon cart-trigger">
                                <i class="fas fa-shopping-cart"></i>
                                <span class="header__badge">1</span>
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
                                <li class="nav__category-item">
                                    <a href="#" class="nav__category-link">
                                        <i class="fas fa-calculator"></i>
                                        <span>Calculators</span>
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                    <ul class="nav__subcategories">
                                        <li class="nav__subcategory-item">
                                            <a href="#" class="nav__subcategory-link">
                                                <i class="fas fa-calculator"></i>
                                                <span>Basic Calculators</span>
                                            </a>
                                        </li>
                                        <li class="nav__subcategory-item">
                                            <a href="#" class="nav__subcategory-link">
                                                <i class="fas fa-calculator"></i>
                                                <span>Scientific Calculators</span>
                                            </a>
                                        </li>
                                        <li class="nav__subcategory-item">
                                            <a href="#" class="nav__subcategory-link">
                                                <i class="fas fa-calculator"></i>
                                                <span>Financial Calculators</span>
                                            </a>
                                        </li>
                                        <li class="nav__subcategory-item">
                                            <a href="#" class="nav__subcategory-link">
                                                <i class="fas fa-calculator"></i>
                                                <span>Graphing Calculators</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav__category-item">
                                    <a href="#" class="nav__category-link">
                                        <i class="fas fa-clipboard"></i>
                                        <span>Conference Pad</span>
                                    </a>
                                </li>
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