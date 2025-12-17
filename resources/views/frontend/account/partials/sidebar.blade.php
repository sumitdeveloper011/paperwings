<!-- Account Sidebar -->
<div class="col-lg-3">
    <div class="account-sidebar">
        <div class="account-user">
            <div class="account-user__avatar">
                <i class="fas fa-user"></i>
            </div>
            <div class="account-user__info">
                <h3 class="account-user__name">{{ Auth::user()->first_name ?? Auth::user()->name ?? 'User' }} {{ Auth::user()->last_name ?? '' }}</h3>
                <p class="account-user__email">{{ Auth::user()->email }}</p>
            </div>
        </div>

        <nav class="account-nav">
            <ul class="account-nav__list">
                <li class="account-nav__item">
                    <a href="{{ route('account.view-profile') }}" class="account-nav__link {{ request()->routeIs('account.view-profile') ? 'account-nav__link--active' : '' }}">
                        <i class="fas fa-user-circle"></i>
                        <span>View Profile</span>
                    </a>
                </li>
                <li class="account-nav__item">
                    <a href="{{ route('account.edit-profile') }}" class="account-nav__link {{ request()->routeIs('account.edit-profile') ? 'account-nav__link--active' : '' }}">
                        <i class="fas fa-edit"></i>
                        <span>Edit Profile</span>
                    </a>
                </li>
                <li class="account-nav__item">
                    <a href="{{ route('account.change-password') }}" class="account-nav__link {{ request()->routeIs('account.change-password') ? 'account-nav__link--active' : '' }}">
                        <i class="fas fa-key"></i>
                        <span>Change Password</span>
                    </a>
                </li>
                <li class="account-nav__item">
                    <a href="{{ route('account.manage-addresses') }}" class="account-nav__link {{ request()->routeIs('account.manage-addresses') ? 'account-nav__link--active' : '' }}">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Manage Addresses</span>
                    </a>
                </li>
                <li class="account-nav__item">
                    <a href="{{ route('account.my-orders') }}" class="account-nav__link {{ request()->routeIs('account.my-orders') ? 'account-nav__link--active' : '' }}">
                        <i class="fas fa-shopping-bag"></i>
                        <span>My Orders</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</div>
