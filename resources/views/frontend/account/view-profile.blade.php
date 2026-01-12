@extends('layouts.frontend.main')
@section('content')
@include('frontend.partials.page-header', [
    'title' => 'View Profile',
    'breadcrumbs' => [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'My Account', 'url' => route('account.view-profile')],
        ['label' => 'View Profile', 'url' => null]
    ]
])
<section class="account-section">
    <div class="container">
        <div class="row">
            @include('frontend.account.partials.sidebar')

            <!-- Account Content -->
            <div class="col-lg-9">
                <!-- View Profile -->
                <div class="account-content">
                    <div class="account-block">
                        <h2 class="account-block__title">View Profile</h2>

                        <div class="profile-view">
                            <div class="profile-view__header">
                                <div class="profile-view__avatar-large">
                                    <img src="{{ $user->avatar && \Storage::disk('public')->exists($user->avatar) ? asset('storage/' . $user->avatar) : asset('assets/images/profile.png') }}" alt="{{ $user->name ?? 'User' }}" id="profileViewImage" class="profile-view__avatar-img" onerror="this.src='{{ asset('assets/images/profile.png') }}'">
                                    <div class="profile-view__avatar-placeholder" id="profileViewPlaceholder" style="display: none;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                </div>
                                <div class="profile-view__info">
                                    <h3 class="profile-view__name">{{ $user->first_name ?? $user->name ?? 'User' }} {{ $user->last_name ?? '' }}</h3>
                                    <p class="profile-view__email">{{ $user->email }}</p>
                                    <p class="profile-view__phone">{{ $user->userDetail->phone ?? 'N/A' }}</p>
                                </div>
                            </div>

                            <div class="profile-view__details">
                                <div class="profile-detail-row">
                                    <div class="profile-detail-label">First Name</div>
                                    <div class="profile-detail-value">{{ $user->first_name ?? 'N/A' }}</div>
                                </div>
                                <div class="profile-detail-row">
                                    <div class="profile-detail-label">Last Name</div>
                                    <div class="profile-detail-value">{{ $user->last_name ?? 'N/A' }}</div>
                                </div>
                                <div class="profile-detail-row">
                                    <div class="profile-detail-label">Email Address</div>
                                    <div class="profile-detail-value">{{ $user->email }}</div>
                                </div>
                                <div class="profile-detail-row">
                                    <div class="profile-detail-label">Phone Number</div>
                                    <div class="profile-detail-value">{{ $user->userDetail->phone ?? 'N/A' }}</div>
                                </div>
                                <div class="profile-detail-row">
                                    <div class="profile-detail-label">Date of Birth</div>
                                    <div class="profile-detail-value">{{ $user->userDetail && $user->userDetail->date_of_birth ? \Carbon\Carbon::parse($user->userDetail->date_of_birth)->format('F d, Y') : 'N/A' }}</div>
                                </div>
                                <div class="profile-detail-row">
                                    <div class="profile-detail-label">Gender</div>
                                    <div class="profile-detail-value">{{ $user->userDetail && $user->userDetail->gender ? ucfirst($user->userDetail->gender) : 'N/A' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
