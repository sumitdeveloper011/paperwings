@extends('layouts.frontend.main')
@section('content')
    <section class="page-header">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Contact Us</li>
                        </ol>
                    </nav>
                    <h1 class="page-title">Contact Us</h1>
                    <p class="page-subtitle">Get in touch with us - we'd love to hear from you!</p>
                </div>
            </div>
        </div>
    </section>

    <section class="contact-section">
        <div class="container">
            <div class="row">
                <!-- Contact Information -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="contact-info-card">
                        <div class="contact-info-card__icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <h3 class="contact-info-card__title">Our Address</h3>
                        @if($address)
                            <p class="contact-info-card__text">{{ $address }}</p>
                        @else
                            <p class="contact-info-card__text">Address not available</p>
                        @endif
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="contact-info-card">
                        <div class="contact-info-card__icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <h3 class="contact-info-card__title">Phone Numbers</h3>
                        @if(!empty($phones))
                            <div class="contact-info-card__text">
                                @foreach($phones as $phone)
                                    <p><a href="tel:{{ preg_replace('/[^0-9+]/', '', $phone) }}">{{ $phone }}</a></p>
                                @endforeach
                            </div>
                        @else
                            <p class="contact-info-card__text">Phone not available</p>
                        @endif
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="contact-info-card">
                        <div class="contact-info-card__icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h3 class="contact-info-card__title">Email Address</h3>
                        @if(!empty($emails))
                            <div class="contact-info-card__text">
                                @foreach($emails as $email)
                                    <p><a href="mailto:{{ $email }}">{{ $email }}</a></p>
                                @endforeach
                            </div>
                        @else
                            <p class="contact-info-card__text">Email not available</p>
                        @endif
                    </div>
                </div>

                @if($workingHours)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="contact-info-card">
                        <div class="contact-info-card__icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h3 class="contact-info-card__title">Working Hours</h3>
                        <div class="contact-info-card__text">
                            {!! nl2br(e($workingHours)) !!}
                        </div>
                    </div>
                </div>
                @endif

                @if(!empty($socialLinks))
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="contact-info-card">
                        <div class="contact-info-card__icon">
                            <i class="fas fa-share-alt"></i>
                        </div>
                        <h3 class="contact-info-card__title">Follow Us</h3>
                        <div class="contact-info-card__social">
                            @if(isset($socialLinks['facebook']))
                                <a href="{{ $socialLinks['facebook'] }}" target="_blank" rel="noopener noreferrer" class="contact-social-link">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                            @endif
                            @if(isset($socialLinks['twitter']))
                                <a href="{{ $socialLinks['twitter'] }}" target="_blank" rel="noopener noreferrer" class="contact-social-link">
                                    <i class="fab fa-twitter"></i>
                                </a>
                            @endif
                            @if(isset($socialLinks['instagram']))
                                <a href="{{ $socialLinks['instagram'] }}" target="_blank" rel="noopener noreferrer" class="contact-social-link">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            @endif
                            @if(isset($socialLinks['linkedin']))
                                <a href="{{ $socialLinks['linkedin'] }}" target="_blank" rel="noopener noreferrer" class="contact-social-link">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                            @endif
                            @if(isset($socialLinks['youtube']))
                                <a href="{{ $socialLinks['youtube'] }}" target="_blank" rel="noopener noreferrer" class="contact-social-link">
                                    <i class="fab fa-youtube"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <div class="row mt-5">
                <!-- Contact Form -->
                <div class="col-12">
                    <div class="contact-form-wrapper">
                        <h2 class="contact-form__title">
                            <i class="fas fa-paper-plane"></i>
                            Send us a Message
                        </h2>
                        <form method="POST" action="{{ route('contact.store') }}" class="contact-form" id="contactForm">
                            @csrf

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="name" class="form-label">
                                            <i class="fas fa-user"></i> Full Name <span class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                               class="form-control @error('name') is-invalid @enderror"
                                               id="name"
                                               name="name"
                                               value="{{ old('name') }}"
                                               required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="email" class="form-label">
                                            <i class="fas fa-envelope"></i> Email Address <span class="text-danger">*</span>
                                        </label>
                                        <input type="email"
                                               class="form-control @error('email') is-invalid @enderror"
                                               id="email"
                                               name="email"
                                               value="{{ old('email') }}"
                                               required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="phone" class="form-label">
                                            <i class="fas fa-phone"></i> Phone Number
                                        </label>
                                        <input type="tel"
                                               class="form-control @error('phone') is-invalid @enderror"
                                               id="phone"
                                               name="phone"
                                               value="{{ old('phone') }}">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="subject" class="form-label">
                                            <i class="fas fa-tag"></i> Subject <span class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                               class="form-control @error('subject') is-invalid @enderror"
                                               id="subject"
                                               name="subject"
                                               value="{{ old('subject') }}"
                                               required>
                                        @error('subject')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-group">
                                    <label for="message" class="form-label">
                                        <i class="fas fa-comment-alt"></i> Message <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control @error('message') is-invalid @enderror"
                                              id="message"
                                              name="message"
                                              rows="6"
                                              required>{{ old('message') }}</textarea>
                                    @error('message')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Maximum 5000 characters</small>
                                </div>
                            </div>

                            <div class="form-group mb-0">
                                <button type="submit" class="btn btn-contact-submit">
                                    <i class="fas fa-paper-plane"></i>
                                    Send Message
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Google Map - Full Width -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="contact-map-wrapper">
                        <h3 class="contact-map__title">
                            <i class="fas fa-map-marked-alt"></i>
                            Find Us on Map
                        </h3>
                        @if($googleMap)
                            <div class="contact-map" id="contactMap">
                                {!! $googleMap !!}
                            </div>
                        @elseif($googleMapApiKey && $address)
                            <div class="contact-map" id="contactMap" style="height: 500px;"></div>
                            <script>
                                function initMap() {
                                    const geocoder = new google.maps.Geocoder();
                                    const map = new google.maps.Map(document.getElementById('contactMap'), {
                                        zoom: 15,
                                        center: {lat: -36.8485, lng: 174.7633} // Default to Auckland, NZ
                                    });

                                    geocoder.geocode({ address: "{{ $address }}" }, function(results, status) {
                                        if (status === 'OK') {
                                            map.setCenter(results[0].geometry.location);
                                            new google.maps.Marker({
                                                map: map,
                                                position: results[0].geometry.location
                                            });
                                        }
                                    });
                                }
                            </script>
                            <script async defer src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapApiKey }}&callback=initMap"></script>
                        @else
                            <div class="contact-map-placeholder">
                                <i class="fas fa-map-marked-alt"></i>
                                <p>Map not available</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

