@extends('layouts.frontend.main')
@section('content')
    @include('frontend.partials.page-header', [
        'title' => 'Contact Us',
        'subtitle' => 'Get in touch with us - we\'d love to hear from you!',
        'breadcrumbs' => [
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Contact Us', 'url' => null]
        ]
    ])

    <section class="contact-section">
        <div class="container">
            <div class="row">
                <!-- Contact Information -->
                <div class="col-lg-4 col-md-6 mb-3">
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

                <div class="col-lg-4 col-md-6 mb-3">
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

                <div class="col-lg-4 col-md-6 mb-3">
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
                <div class="col-lg-4 col-md-6 mb-3">
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
                <div class="col-lg-4 col-md-6 mb-3">
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

            <div class="row mt-3">
                <!-- Contact Form -->
                <div class="col-12">
                    <div class="contact-form-wrapper">
                        <h2 class="contact-form__title">
                            <i class="fas fa-paper-plane"></i>
                            Send us a Message
                        </h2>
                        <form method="POST" action="{{ route('contact.store') }}" class="contact-form" id="contactForm" enctype="multipart/form-data">
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
                                               minlength="2"
                                               maxlength="255"
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
                                               value="{{ old('phone') }}"
                                               pattern="[\d\+\s\-]+"
                                               inputmode="numeric"
                                               maxlength="20">
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
                                               minlength="3"
                                               maxlength="255"
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
                                              rows="5"
                                              minlength="10"
                                              maxlength="5000"
                                              required>{{ old('message') }}</textarea>
                                    @error('message')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Minimum 10 characters, maximum 5000 characters</small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-group">
                                    <label for="image" class="form-label">
                                        <i class="fas fa-image"></i> Image (Optional)
                                    </label>
                                    <input type="file"
                                           class="form-control @error('image') is-invalid @enderror"
                                           id="image"
                                           name="image"
                                           accept="image/jpeg,image/jpg,image/png,image/gif,image/webp">
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Supported formats: JPEG, JPG, PNG, GIF, WEBP. Max size: 2MB</small>
                                    <div id="imagePreview" class="mt-2" style="display: none;">
                                        <img id="imagePreviewImg" src="" alt="Preview" style="max-width: 200px; max-height: 200px; border-radius: 5px; border: 1px solid #ddd;">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-0">
                                <button type="submit" class="btn btn-contact-submit">
                                    <i class="fas fa-paper-plane"></i>
                                    Send Message
                                </button>
                            </div>
                        </form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (window.FormSubmissionHandler) {
        window.FormSubmissionHandler.init('contactForm', {
            loadingText: 'Sending Message...',
            timeout: 15000
        });
    }
});
</script>
@endpush
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- Google Map - Full Width (Outside Container) -->
    <section class="contact-map-section">
        <div class="container-fluid px-0">
            <div class="contact-map-wrapper">
                <div class="contact-map-container">
                    @if($googleMap)
                        <div class="contact-map" id="contactMap">
                            {!! $googleMap !!}
                        </div>
                    @elseif($googleMapApiKey && $address)
                        <div class="contact-map" id="contactMap" style="height: 450px;"></div>
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
    </section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Phone number input restrictions - allow numbers, spaces, +, and hyphens only
        const phoneInput = document.getElementById('phone');
        if (phoneInput) {
            // Restrict input to numbers, spaces, +, and hyphens
            phoneInput.addEventListener('input', function(e) {
                let value = this.value;
                value = value.replace(/[^\d\+\s\-]/g, '');
                this.value = value;
            });

            // Prevent paste of invalid characters
            phoneInput.addEventListener('paste', function(e) {
                const paste = (e.clipboardData || window.clipboardData).getData('text');
                if (!/^[\d\+\s\-]*$/.test(paste)) {
                    e.preventDefault();
                }
            });
        }

        // Image preview
        const imageInput = document.getElementById('image');
        const imagePreview = document.getElementById('imagePreview');
        const imagePreviewImg = document.getElementById('imagePreviewImg');
        
        if (imageInput && imagePreview && imagePreviewImg) {
            imageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreviewImg.src = e.target.result;
                        imagePreview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                } else {
                    imagePreview.style.display = 'none';
                }
            });
        }

        // Initialize native form validation
        function initContactFormValidation() {
            if (typeof window.initFormValidationNative === 'undefined') {
                setTimeout(initContactFormValidation, 100);
                return;
            }

            const form = document.getElementById('contactForm');
            if (!form) {
                setTimeout(initContactFormValidation, 100);
                return;
            }

            const validationRules = {
                'name': {
                    required: true,
                    minlength: 2,
                    maxlength: 255,
                    regex: '^[a-zA-Z\\s\\-\\\'\\.]+$'
                },
                'email': {
                    required: true,
                    email: true,
                    maxlength: 255
                },
                'phone': {
                    nzPhone: true
                },
                'subject': {
                    required: true,
                    minlength: 3,
                    maxlength: 255
                },
                'message': {
                    required: true,
                    minlength: 10,
                    maxlength: 5000
                }
            };

            const validationMessages = {
                'name': {
                    required: 'Please enter your name.',
                    minlength: 'Name must be at least 2 characters.',
                    maxlength: 'Name cannot exceed 255 characters.',
                    regex: 'Name can only contain letters, spaces, hyphens, apostrophes, and periods.'
                },
                'email': {
                    required: 'Please enter your email address.',
                    email: 'Please enter a valid email address.',
                    maxlength: 'Email cannot exceed 255 characters.'
                },
                'phone': {
                    nzPhone: 'Please enter a valid New Zealand phone number (numbers only, e.g., 0211234567 or 091234567).'
                },
                'subject': {
                    required: 'Please enter a subject.',
                    minlength: 'Subject must be at least 3 characters.',
                    maxlength: 'Subject cannot exceed 255 characters.'
                },
                'message': {
                    required: 'Please enter your message.',
                    minlength: 'Message must be at least 10 characters.',
                    maxlength: 'Your message is too long. Maximum 5000 characters allowed.'
                }
            };

            window.initFormValidationNative('#contactForm', {
                rules: validationRules,
                messages: validationMessages,
                onInvalid: function(errors, validator) {
                    validator.scrollToFirstError();
                },
                onValid: function() {
                    if (window.Analytics && window.Analytics.isEnabled()) {
                        window.Analytics.trackContactFormSubmit();
                    }
                }
            });
        }

        initContactFormValidation();
    });
</script>
@endpush

