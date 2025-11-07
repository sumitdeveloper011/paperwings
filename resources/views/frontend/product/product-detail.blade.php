@extends('layouts.frontend.main')
@section('content')
    @include('include.frontend.breadcrumb')
    <section class="product-details-section">
        <div class="container">
            <div class="row">
                <!-- Product Images -->
                <div class="col-lg-6">
                    <div class="product-images">
                        <div class="product-main-image">
                            <img src="{{ asset('assets/frontend/images/product-1.jpg') }}" alt="Premium Notebook" class="main-img" id="mainImage">
                        </div>
                        <div class="product-thumbnails">
                            <div class="thumbnail-item active" data-image="assets/images/product-1.jpg">
                                <img src="{{ asset('assets/frontend/images/product-1.jpg') }}" alt="Premium Notebook">
                            </div>
                            <div class="thumbnail-item" data-image="assets/images/product-2.jpg">
                                <img src="{{ asset('assets/frontend/images/product-2.jpg') }}" alt="Premium Notebook">
                            </div>
                            <div class="thumbnail-item" data-image="assets/images/product-3.jpg">
                                <img src="{{ asset('assets/frontend/images/product-3.jpg') }}" alt="Premium Notebook">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Info -->
                <div class="col-lg-6">
                    <div class="product-info">
                        <h1 class="product-title">Premium Notebook</h1>
                        <div class="product-rating">
                            <div class="stars">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                            <span class="rating-text">4.5 out of 5</span>
                            <span class="reviews-count">(24 reviews)</span>
                        </div>
                        
                        <div class="product-price">
                            <span class="current-price">$24.99</span>
                            <span class="old-price">$34.99</span>
                            <span class="discount">Save 29%</span>
                        </div>

                        <div class="product-description">
                            <p>High-quality premium notebook with 200 lined pages, perfect for students, professionals, and anyone who loves to write. Features a durable hardcover, acid-free paper, and elegant design.</p>
                        </div>

                        <div class="product-options">
                            <div class="option-group">
                                <label class="option-label">Color:</label>
                                <div class="color-options">
                                    <button class="color-option active" data-color="Black" style="background-color: #000;"></button>
                                    <button class="color-option" data-color="Blue" style="background-color: #0066cc;"></button>
                                    <button class="color-option" data-color="Red" style="background-color: #cc0000;"></button>
                                </div>
                            </div>
                            
                            <div class="option-group">
                                <label class="option-label">Quantity:</label>
                                <div class="quantity-selector">
                                    <button class="qty-btn" id="decreaseQty">-</button>
                                    <input type="number" value="1" min="1" max="99" id="quantity">
                                    <button class="qty-btn" id="increaseQty">+</button>
                                </div>
                            </div>
                        </div>

                        <div class="product-actions">
                            <button class="btn btn-primary add-to-cart">
                                <i class="fas fa-shopping-cart"></i>
                                Add to Cart
                            </button>
                            <button class="btn btn-outline-primary add-to-wishlist">
                                <i class="fas fa-heart"></i>
                                Add to Wishlist
                            </button>
                        </div>

                        <div class="product-meta">
                            <div class="meta-item">
                                <span class="meta-label">SKU:</span>
                                <span class="meta-value">NB-001</span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Category:</span>
                                <span class="meta-value">Notebooks</span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Tags:</span>
                                <span class="meta-value">Premium, Professional, Student</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Tabs -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="product-tabs">
                        <ul class="nav nav-tabs" id="productTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button" role="tab">Description</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="specifications-tab" data-bs-toggle="tab" data-bs-target="#specifications" type="button" role="tab">Specifications</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab">Reviews</button>
                            </li>
                        </ul>
                        
                        <div class="tab-content" id="productTabsContent">
                            <div class="tab-pane fade show active" id="description" role="tabpanel">
                                <div class="tab-content-body">
                                    <h3>Product Description</h3>
                                    <p>Our Premium Notebook is crafted with the finest materials to provide an exceptional writing experience. The 200 lined pages are made from acid-free paper that prevents yellowing and deterioration over time.</p>
                                    
                                    <h4>Key Features:</h4>
                                    <ul>
                                        <li>200 lined pages for extensive note-taking</li>
                                        <li>Durable hardcover with elegant design</li>
                                        <li>Acid-free paper prevents yellowing</li>
                                        <li>Perfect for students and professionals</li>
                                        <li>Multiple color options available</li>
                                        <li>Lay-flat binding for easy writing</li>
                                    </ul>
                                    
                                    <p>Whether you're a student taking notes in class, a professional in meetings, or someone who loves to journal, this premium notebook will meet all your writing needs with style and durability.</p>
                                </div>
                            </div>
                            
                            <div class="tab-pane fade" id="specifications" role="tabpanel">
                                <div class="tab-content-body">
                                    <h3>Product Specifications</h3>
                                    <div class="specs-table">
                                        <div class="spec-row">
                                            <span class="spec-label">Page Count:</span>
                                            <span class="spec-value">200 pages</span>
                                        </div>
                                        <div class="spec-row">
                                            <span class="spec-label">Paper Type:</span>
                                            <span class="spec-value">Lined, Acid-free</span>
                                        </div>
                                        <div class="spec-row">
                                            <span class="spec-label">Cover:</span>
                                            <span class="spec-value">Hardcover</span>
                                        </div>
                                        <div class="spec-row">
                                            <span class="spec-label">Binding:</span>
                                            <span class="spec-value">Lay-flat</span>
                                        </div>
                                        <div class="spec-row">
                                            <span class="spec-label">Dimensions:</span>
                                            <span class="spec-value">8.5" x 11" (A4)</span>
                                        </div>
                                        <div class="spec-row">
                                            <span class="spec-label">Weight:</span>
                                            <span class="spec-value">1.2 lbs</span>
                                        </div>
                                        <div class="spec-row">
                                            <span class="spec-label">Material:</span>
                                            <span class="spec-value">Premium paper, Cardboard cover</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="tab-pane fade" id="reviews" role="tabpanel">
                                <div class="tab-content-body">
                                    <h3>Customer Reviews</h3>
                                    <div class="reviews-summary">
                                        <div class="overall-rating">
                                            <div class="rating-number">4.5</div>
                                            <div class="rating-stars">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star-half-alt"></i>
                                            </div>
                                            <div class="total-reviews">Based on 24 reviews</div>
                                        </div>
                                    </div>
                                    
                                    <div class="review-item">
                                        <div class="review-header">
                                            <div class="reviewer-name">Sarah M.</div>
                                            <div class="review-rating">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                            </div>
                                            <div class="review-date">March 15, 2024</div>
                                        </div>
                                        <div class="review-content">
                                            <p>Excellent quality notebook! The paper is smooth and the cover is very durable. Perfect for my daily journaling needs.</p>
                                        </div>
                                    </div>
                                    
                                    <div class="review-item">
                                        <div class="review-header">
                                            <div class="reviewer-name">John D.</div>
                                            <div class="review-rating">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="far fa-star"></i>
                                            </div>
                                            <div class="review-date">March 10, 2024</div>
                                        </div>
                                        <div class="review-content">
                                            <p>Great notebook for taking notes in meetings. The lay-flat binding makes it easy to write on every page.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Products -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="related-products">
                        <h3 class="section-title">Related Products</h3>
                        <div class="row">
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection