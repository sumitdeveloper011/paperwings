<footer class="footer">
        <div class="footer__main">
            <div class="container">
                <div class="row">
                    <!-- Company Information Column -->
                    <div class="col-lg-3 col-md-6">
                        <div class="footer__column">
                            <div class="footer__logo">
                                @if(isset($footerLogo) && $footerLogo)
                                    <img src="{{ asset('storage/' . $footerLogo) }}" alt="Paper Wings Logo" class="footer__logo-image">
                                @else
                                    <div class="footer__logo-icon">P</div>
                                    <span class="footer__logo-text">Paper Wings</span>
                                @endif
                            </div>
                            <p class="footer__tagline">{{ $footerTagline ?? 'We Promise We\'ll Get Back To You Promptly- Your Gifting Needs Are Always On Our Minds!' }}</p>
                            <div class="footer__social">
                                @if(isset($footerSocialLinks['facebook']))
                                    <a href="{{ $footerSocialLinks['facebook'] }}" target="_blank" rel="noopener noreferrer" class="footer__social-link">
                                        <i class="fab fa-facebook-f"></i>
                                    </a>
                                @endif
                                @if(isset($footerSocialLinks['twitter']))
                                    <a href="{{ $footerSocialLinks['twitter'] }}" target="_blank" rel="noopener noreferrer" class="footer__social-link">
                                        <i class="fab fa-twitter"></i>
                                    </a>
                                @endif
                                @if(isset($footerSocialLinks['instagram']))
                                    <a href="{{ $footerSocialLinks['instagram'] }}" target="_blank" rel="noopener noreferrer" class="footer__social-link">
                                        <i class="fab fa-instagram"></i>
                                    </a>
                                @endif
                                @if(isset($footerSocialLinks['linkedin']))
                                    <a href="{{ $footerSocialLinks['linkedin'] }}" target="_blank" rel="noopener noreferrer" class="footer__social-link">
                                        <i class="fab fa-linkedin-in"></i>
                                    </a>
                                @endif
                                @if(isset($footerSocialLinks['youtube']))
                                    <a href="{{ $footerSocialLinks['youtube'] }}" target="_blank" rel="noopener noreferrer" class="footer__social-link">
                                        <i class="fab fa-youtube"></i>
                                    </a>
                                @endif
                                @if(isset($footerSocialLinks['pinterest']))
                                    <a href="{{ $footerSocialLinks['pinterest'] }}" target="_blank" rel="noopener noreferrer" class="footer__social-link">
                                        <i class="fab fa-pinterest"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Useful Links Column -->
                    <div class="col-lg-3 col-md-6">
                        <div class="footer__column">
                            <h3 class="footer__column-title">USEFUL LINKS</h3>
                            <ul class="footer__links">
                                <li><a href="{{ route('contact') }}" class="footer__link">Contact Us</a></li>
                                <li><a href="{{ route('faq.index') }}" class="footer__link">FAQs</a></li>
                                @if(isset($footerPages) && $footerPages->count() > 0)
                                    @foreach($footerPages as $page)
                                        <li>
                                            <a href="{{ url('/' . $page->slug) }}" class="footer__link">
                                                {{ $page->title }}
                                            </a>
                                        </li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>

                    <!-- Shop Column -->
                    <div class="col-lg-3 col-md-6">
                        <div class="footer__column">
                            <h3 class="footer__column-title">SHOP</h3>
                            <ul class="footer__links">
                                <li><a href="{{ route('shop') }}" class="footer__link">Shop</a></li>
                                @if(isset($footerCategories) && $footerCategories->count() > 0)
                                    @foreach($footerCategories as $category)
                                        <li>
                                            <a href="{{ route('category.show', $category->slug) }}" class="footer__link">
                                                {{ $category->name }}
                                            </a>
                                        </li>
                                    @endforeach
                                @else
                                    <li><a href="{{ route('home') }}" class="footer__link">All Products</a></li>
                                @endif
                            </ul>
                        </div>
                    </div>

                    <!-- Need Help Column -->
                    <div class="col-lg-3 col-md-6">
                        <div class="footer__column">
                            <h3 class="footer__column-title">NEED HELP</h3>
                            <div class="footer__contact">
                                @if(isset($footerPhone) && $footerPhone)
                                    <div class="footer__contact-item">
                                        <i class="fas fa-phone footer__contact-icon"></i>
                                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $footerPhone) }}" class="footer__contact-text footer__contact-text--highlight">
                                            {{ $footerPhone }}
                                        </a>
                                    </div>
                                @endif
                                @if(isset($footerWorkingHours) && $footerWorkingHours)
                                    <div class="footer__contact-item">
                                        <i class="fas fa-clock footer__contact-icon"></i>
                                        <div class="footer__contact-text">
                                            {!! $footerWorkingHours !!}
                                        </div>
                                    </div>
                                @endif
                                @if(isset($footerEmail) && $footerEmail)
                                    <div class="footer__contact-item">
                                        <i class="fas fa-envelope footer__contact-icon"></i>
                                        <a href="mailto:{{ $footerEmail }}" class="footer__contact-text">
                                            {{ $footerEmail }}
                                        </a>
                                    </div>
                                @endif
                                @if(isset($footerAddress) && $footerAddress)
                                    <div class="footer__contact-item">
                                        <i class="fas fa-map-marker-alt footer__contact-icon"></i>
                                        <span class="footer__contact-text">{{ $footerAddress }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="footer__bottom">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center">
                        <div class="footer__copyright">
                            {!! $footerCopyright ?? 'Copyright © ' . date('Y') . ' Paper Wings. All rights reserved.' !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scroll to Top Button -->
        <button class="scroll-to-top" id="scrollToTop">
            <i class="fas fa-arrow-up"></i>
        </button>
    </footer>
