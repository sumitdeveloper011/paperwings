<!-- New Admin Sidebar -->
<aside class="sidebar" id="sidebar">
    <!-- Sidebar Header -->
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <img src="{{ asset('assets/images/logo.svg') }}" alt="PAPERWINGS" class="sidebar-logo">
            <div class="sidebar-brand-text">
                <h3>PAPERWINGS</h3>
                <span>Admin Panel</span>
            </div>
        </div>
        <button class="sidebar-close" id="sidebarClose">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- Sidebar Navigation -->
    <nav class="sidebar-nav">
        <ul class="sidebar-menu">
            <li class="sidebar-item">
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" data-tooltip="Dashboard">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('admin.categories.index') }}" class="sidebar-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" data-tooltip="Categories">
                    <i class="fas fa-tags"></i>
                    <span>Categories</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('admin.subcategories.index') }}" class="sidebar-link {{ request()->routeIs('admin.subcategories.*') ? 'active' : '' }}" data-tooltip="Sub Categories">
                    <i class="fas fa-layer-group"></i>
                    <span>Sub Categories</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('admin.brands.index') }}" class="sidebar-link {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}" data-tooltip="Brands">
                    <i class="fas fa-award"></i>
                    <span>Brands</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('admin.products.index') }}" class="sidebar-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" data-tooltip="Products">
                    <i class="fas fa-box"></i>
                    <span>Products</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('admin.sliders.index') }}" class="sidebar-link {{ request()->routeIs('admin.sliders.*') ? 'active' : '' }}" data-tooltip="Sliders">
                    <i class="fas fa-images"></i>
                    <span>Sliders</span>
                </a>
            </li>
        </ul>
    </nav>
</aside> 