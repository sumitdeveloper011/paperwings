<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\AuthController;
use App\Http\Controllers\Frontend\ProductController;
use App\Http\Controllers\Frontend\WishlistController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\CheckoutController;
use App\Http\Controllers\Frontend\AccountController;
use App\Http\Controllers\Frontend\SubscriptionController;
use App\Http\Controllers\Frontend\PageController;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\ReviewController;
use App\Http\Controllers\Frontend\QuestionController;
use App\Http\Controllers\Frontend\BundleController;
use App\Http\Controllers\Auth\SocialAuthController;

Route::middleware('prevent.admin')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
});

Route::middleware('prevent.admin')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->name('login.authenticate');
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'store'])->name('register.store');
    Route::get('/forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');

    Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verify'])
        ->name('verification.verify');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/auth/google', [SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
    Route::get('/auth/facebook', [SocialAuthController::class, 'redirectToFacebook'])->name('auth.facebook');
    Route::get('/auth/facebook/callback', [SocialAuthController::class, 'handleFacebookCallback'])->name('auth.facebook.callback');
});

Route::middleware('prevent.admin')->group(function () {
    Route::get('/search', [\App\Http\Controllers\Frontend\SearchController::class, 'index'])->name('search');
    Route::get('/search/autocomplete', [\App\Http\Controllers\Frontend\SearchController::class, 'autocomplete'])->name('search.autocomplete');
    Route::get('/search/autocomplete/render', [\App\Http\Controllers\Frontend\SearchController::class, 'renderAutocomplete'])->name('search.autocomplete.render');
    Route::get('/search/results/render', [\App\Http\Controllers\Frontend\SearchController::class, 'renderResults'])->name('search.results.render');
});

Route::middleware('prevent.admin')->group(function () {
    Route::post('/subscription', [SubscriptionController::class, 'store'])->name('subscription.store');
    Route::get('/unsubscribe/{uuid}', [SubscriptionController::class, 'unsubscribe'])->name('subscription.unsubscribe');
});

Route::middleware('prevent.admin')->group(function () {
    // Products - new clean URLs
    Route::get('/products/{slug}', [ProductController::class, 'productDetail'])->name('product.detail');
    Route::post('/products/{slug}/review', [ReviewController::class, 'store'])->name('review.store');
    Route::post('/products/{slug}/question', [QuestionController::class, 'store'])->name('question.store');

    // Categories - new clean URLs
    Route::get('/categories/{slug}', [ProductController::class, 'productByCategory'])->name('category.show');

    // Shop
    Route::get('/shop', [ProductController::class, 'shop'])->name('shop');

    // Reviews and Questions (keep as is for API endpoints)
    Route::post('/review/{review}/helpful', [ReviewController::class, 'helpful'])->name('review.helpful');
    Route::post('/question/{question}/answer', [QuestionController::class, 'storeAnswer'])->name('question.answer');
    Route::post('/answer/{answer}/helpful', [QuestionController::class, 'helpful'])->name('answer.helpful');

    // Bundles - fix singular to plural
    Route::get('/bundles', [BundleController::class, 'index'])->name('bundles.index');
    Route::get('/bundles/{slug}', [BundleController::class, 'show'])->name('bundle.show');

    // Redirects from old URLs to new URLs (301 permanent redirects for SEO)
    Route::get('/product/{slug}', function($slug) {
        return redirect()->route('product.detail', $slug, 301);
    });
    Route::get('/category/{slug}', function($slug) {
        return redirect()->route('category.show', $slug, 301);
    });
    Route::get('/bundle/{slug}', function($slug) {
        return redirect()->route('bundle.show', $slug, 301);
    });
});

Route::middleware('prevent.admin')->group(function () {
    // Direct routes for common pages (clean URLs)
    Route::get('/about-us', function() {
        return app(PageController::class)->showBySlug('about-us');
    })->name('about');
    Route::get('/privacy-policy', function() {
        return app(PageController::class)->showBySlug('privacy-policy');
    })->name('privacy');
    Route::get('/terms-and-conditions', function() {
        return app(PageController::class)->showBySlug('terms-and-conditions');
    })->name('terms');
    Route::get('/delivery-policy', function() {
        return app(PageController::class)->showBySlug('delivery-policy');
    })->name('delivery');
    Route::get('/return-policy', function() {
        return app(PageController::class)->showBySlug('return-policy');
    })->name('returns');
    Route::get('/cookie-policy', function() {
        return app(PageController::class)->showBySlug('cookie-policy');
    })->name('cookies');

    // Redirect from old /page/{slug} to new direct slug (301 for SEO) - must be before catch-all
    Route::get('/page/{slug}', function($slug) {
        return redirect('/' . $slug, 301);
    });

    // Fallback for other pages (must be last with proper exclusions)
    Route::get('/{slug}', [PageController::class, 'showBySlug'])
        ->where('slug', '^(?!admin|api|cart|checkout|account|login|register|search|shop|products|categories|bundles|contact|faq|wishlist|unsubscribe|auth|stripe|reset-password|forgot-password|email|about-us|privacy-policy|terms-and-conditions|delivery-policy|return-policy|cookie-policy|page).*')
        ->name('page.show');

    Route::get('/faq', [\App\Http\Controllers\Frontend\FaqController::class, 'index'])->name('faq.index');
});

Route::middleware('prevent.admin')->group(function () {
    Route::get('/contact', [ContactController::class, 'index'])->name('contact');
    Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
});

Route::middleware('prevent.admin')->group(function () {
    Route::post('/wishlist/add', [WishlistController::class, 'add'])->name('wishlist.add');
    Route::post('/wishlist/remove', [WishlistController::class, 'remove'])->name('wishlist.remove');
    Route::get('/wishlist/list', [WishlistController::class, 'list'])->name('wishlist.list');
    Route::get('/wishlist/render', [WishlistController::class, 'render'])->name('wishlist.render');
    Route::get('/wishlist/count', [WishlistController::class, 'count'])->name('wishlist.count');
    Route::post('/wishlist/check', [WishlistController::class, 'check'])->name('wishlist.check');
});

Route::middleware(['auth', 'prevent.admin'])->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::get('/cart/list', [CartController::class, 'index'])->name('cart.list');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::put('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::get('/cart/api/list', [CartController::class, 'list'])->name('cart.api.list');
    Route::get('/cart/render', [CartController::class, 'render'])->name('cart.render');
    Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');
    Route::post('/cart/check', [CartController::class, 'check'])->name('cart.check');
});

Route::middleware(['auth', 'prevent.admin'])->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/apply-coupon', [CheckoutController::class, 'applyCoupon'])->name('checkout.apply-coupon');
    Route::post('/checkout/remove-coupon', [CheckoutController::class, 'removeCoupon'])->name('checkout.remove-coupon');
    Route::post('/checkout/calculate-shipping', [CheckoutController::class, 'calculateShipping'])->name('checkout.calculate-shipping');
    Route::post('/checkout/create-payment-intent', [CheckoutController::class, 'createPaymentIntent'])->name('checkout.create-payment-intent');
    Route::post('/checkout/process-order', [CheckoutController::class, 'processOrder'])->name('checkout.process-order');
    Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');
});

Route::post('/stripe/webhook', [\App\Http\Controllers\StripeWebhookController::class, 'handleWebhook'])
    ->name('stripe.webhook')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class]);

Route::middleware(['auth', 'prevent.admin'])->group(function () {
    Route::get('/account', [AccountController::class, 'index'])->name('account.index');
    Route::get('/account/view-profile', [AccountController::class, 'viewProfile'])->name('account.view-profile');
    Route::get('/account/edit-profile', [AccountController::class, 'editProfile'])->name('account.edit-profile');
    Route::put('/account/update-profile', [AccountController::class, 'updateProfile'])->name('account.update-profile');
    Route::get('/account/change-password', [AccountController::class, 'changePassword'])->name('account.change-password');
    Route::put('/account/update-password', [AccountController::class, 'updatePassword'])->name('account.update-password');
    Route::get('/account/manage-addresses', [AccountController::class, 'manageAddresses'])->name('account.manage-addresses');
    Route::post('/account/addresses', [AccountController::class, 'storeAddress'])->name('account.addresses.store');
    Route::get('/account/addresses/{id}/edit', [AccountController::class, 'editAddress'])->name('account.addresses.edit');
    Route::put('/account/addresses/{id}', [AccountController::class, 'updateAddress'])->name('account.addresses.update');
    Route::delete('/account/addresses/{id}', [AccountController::class, 'destroyAddress'])->name('account.addresses.destroy');
    Route::put('/account/addresses/{id}/set-default', [AccountController::class, 'setDefaultAddress'])->name('account.addresses.set-default');
    Route::get('/account/my-orders', [AccountController::class, 'myOrders'])->name('account.my-orders');
    Route::get('/account/orders/{orderNumber}', [AccountController::class, 'orderDetails'])->name('account.order-details');
});
