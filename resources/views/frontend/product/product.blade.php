@extends('layouts.frontend.main')
@section('content')
    @include('include.frontend.breadcrumb')
    <section class="products-section">
        <div class="container">
            <!-- Products Grid -->
            <div class="row">
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="product-item">
                        <div class="product__image">
                            <img src="{{ asset('assets/frontend/images/product-1.jpg') }}" alt="Premium Notebook" class="product__img">
                            <div class="product__actions">
                                <button class="product__action" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
                                <button class="product__action product__add-cart" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                            </div>
                        </div>
                        <div class="product__info">
                            <h3 class="product__name">Premium Notebook</h3>
                            <div class="product__price">
                                <span class="product__price-current">$24.99</span>
                                <span class="product__price-old">$34.99</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="product-item">
                        <div class="product__image">
                            <img src="{{ asset('assets/frontend/images/product-2.jpg') }}" alt="Professional Pen Set" class="product__img">
                            <div class="product__actions">
                                <button class="product__action" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
                                <button class="product__action product__add-cart" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                            </div>
                        </div>
                        <div class="product__info">
                            <h3 class="product__name">Professional Pen Set</h3>
                            <div class="product__price">
                                <span class="product__price-current">$19.99</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="product-item">
                        <div class="product__image">
                            <img src="{{ asset('assets/frontend/images/product-3.jpg') }}" alt="Art Supply Kit" class="product__img">
                            <div class="product__actions">
                                <button class="product__action" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
                                <button class="product__action product__add-cart" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                            </div>
                        </div>
                        <div class="product__info">
                            <h3 class="product__name">Art Supply Kit</h3>
                            <div class="product__price">
                                <span class="product__price-current">$45.99</span>
                                <span class="product__price-old">$59.99</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="product-item">
                        <div class="product__image">
                            <img src="{{ asset('assets/frontend/images/product-1.jpg') }}" alt="Office Organizer" class="product__img">
                            <div class="product__actions">
                                <button class="product__action" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
                                <button class="product__action product__add-cart" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                            </div>
                        </div>
                        <div class="product__info">
                            <h3 class="product__name">Office Organizer</h3>
                            <div class="product__price">
                                <span class="product__price-current">$32.99</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="product-item">
                        <div class="product__image">
                            <img src="{{ asset('assets/frontend/images/product-2.jpg') }}" alt="Desk Calendar" class="product__img">
                            <div class="product__actions">
                                <button class="product__action" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
                                <button class="product__action product__add-cart" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                            </div>
                        </div>
                        <div class="product__info">
                            <h3 class="product__name">Desk Calendar</h3>
                            <div class="product__price">
                                <span class="product__price-current">$15.99</span>
                                <span class="product__price-old">$22.99</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="product-item">
                        <div class="product__image">
                            <img src="{{ asset('assets/frontend/images/product-3.jpg') }}" alt="Sticky Notes Pack" class="product__img">
                            <div class="product__actions">
                                <button class="product__action" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
                                <button class="product__action product__add-cart" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                            </div>
                        </div>
                        <div class="product__info">
                            <h3 class="product__name">Sticky Notes Pack</h3>
                            <div class="product__price">
                                <span class="product__price-current">$8.99</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="product-item">
                        <div class="product__image">
                            <img src="{{ asset('assets/frontend/images/product-1.jpg') }}" alt="File Folders" class="product__img">
                            <div class="product__actions">
                                <button class="product__action" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
                                <button class="product__action product__add-cart" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                            </div>
                        </div>
                        <div class="product__info">
                            <h3 class="product__name">File Folders</h3>
                            <div class="product__price">
                                <span class="product__price-current">$12.99</span>
                                <span class="product__price-old">$18.99</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="product-item">
                        <div class="product__image">
                            <img src="{{ asset('assets/frontend/images/product-2.jpg') }}" alt="Paper Clips" class="product__img">
                            <div class="product__actions">
                                <button class="product__action" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
                                <button class="product__action product__add-cart" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                            </div>
                        </div>
                        <div class="product__info">
                            <h3 class="product__name">Paper Clips</h3>
                            <div class="product__price">
                                <span class="product__price-current">$5.99</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="product-item">
                        <div class="product__image">
                            <img src="{{ asset('assets/frontend/images/product-3.jpg') }}" alt="Whiteboard Marker" class="product__img">
                            <div class="product__actions">
                                <button class="product__action" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
                                <button class="product__action product__add-cart" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                            </div>
                        </div>
                        <div class="product__info">
                            <h3 class="product__name">Whiteboard Marker</h3>
                            <div class="product__price">
                                <span class="product__price-current">$3.99</span>
                                <span class="product__price-old">$6.99</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="product-item">
                        <div class="product__image">
                            <img src="{{ asset('assets/frontend/images/product-1.jpg') }}" alt="Desk Lamp" class="product__img">
                            <div class="product__actions">
                                <button class="product__action" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
                                <button class="product__action product__add-cart" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                            </div>
                        </div>
                        <div class="product__info">
                            <h3 class="product__name">Desk Lamp</h3>
                            <div class="product__price">
                                <span class="product__price-current">$89.99</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="product-item">
                        <div class="product__image">
                            <img src="{{ asset('assets/frontend/images/product-2.jpg') }}" alt="Calculator" class="product__img">
                            <div class="product__actions">
                                <button class="product__action" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
                                <button class="product__action product__add-cart" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                            </div>
                        </div>
                        <div class="product__info">
                            <h3 class="product__name">Calculator</h3>
                            <div class="product__price">
                                <span class="product__price-current">$25.99</span>
                                <span class="product__price-old">$35.99</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="product-item">
                        <div class="product__image">
                            <img src="{{ asset('assets/frontend/images/product-3.jpg') }}" alt="Printer Paper" class="product__img">
                            <div class="product__actions">
                                <button class="product__action" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
                                <button class="product__action product__add-cart" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                            </div>
                        </div>
                        <div class="product__info">
                            <h3 class="product__name">Printer Paper</h3>
                            <div class="product__price">
                                <span class="product__price-current">$18.99</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div class="row">
                <div class="col-12">
                    <div class="pagination-wrapper text-center">
                        <nav aria-label="Products pagination">
                            <ul class="pagination">
                                <li class="page-item disabled">
                                    <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                                </li>
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                <li class="page-item">
                                    <a class="page-link" href="#">Next</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection