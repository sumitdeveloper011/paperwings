@php
    use Illuminate\Support\Facades\Storage;
    $user = Auth::user();
    $hasAvatar = $user && $user->avatar && Storage::disk('public')->exists($user->avatar);
    $avatarUrl = $hasAvatar ? asset('storage/' . $user->avatar) : null;
    $firstLetter = strtoupper(substr($user->first_name ?? 'A', 0, 1));
@endphp
<!-- New Admin Topbar -->
<header class="topbar">
    <div class="topbar-container">
        <!-- Left Section -->
        <div class="topbar-left">
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <div class="topbar-brand">
                <h2>{{ $title ?? 'Paper Wings' }}</h2>
            </div>
        </div>

        <!-- Right Section -->
        <div class="topbar-right">
            <div class="topbar-notifications">
                <button class="notification-btn">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">3</span>
                </button>
            </div>
            <div class="topbar-user">
                <div class="user-info" id="userDropdown">
                    <div class="user-avatar">
                        @if($hasAvatar)
                            <img src="{{ $avatarUrl }}" alt="{{ $user->name }}" class="user-avatar-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <span class="user-avatar-initial" style="display: none;">{{ $firstLetter }}</span>
                        @else
                            <span class="user-avatar-initial">{{ $firstLetter }}</span>
                        @endif
                    </div>
                    <div class="user-details">
                        <span class="user-name">{{ $user->name ?? 'Admin' }}</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
                <div class="user-dropdown" id="userDropdownMenu">
                    <div class="dropdown-header">
                        <div class="dropdown-avatar">
                            @if($hasAvatar)
                                <img src="{{ $avatarUrl }}" alt="{{ $user->name }}" class="dropdown-avatar-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <span class="dropdown-avatar-initial" style="display: none;">{{ $firstLetter }}</span>
                            @else
                                <span class="dropdown-avatar-initial">{{ $firstLetter }}</span>
                            @endif
                        </div>
                        <div class="dropdown-info">
                            <span class="dropdown-name">{{ $user->name ?? 'Admin User' }}</span>
                            <span class="dropdown-email">{{ $user->email ?? 'admin@paperwings.com' }}</span>
                        </div>
                    </div>
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('admin.dashboard') }}" class="dropdown-item">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('admin.profile.index') }}" class="dropdown-item">
                        <i class="fas fa-user-cog"></i>
                        <span>Profile</span>
                    </a>
                    <a href="{{ route('admin.settings.index') }}" class="dropdown-item">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('admin.logout') }}" class="dropdown-form">
                        @csrf
                        <button type="submit" class="dropdown-item logout-btn">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
