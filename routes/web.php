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
use App\Http\Controllers\Frontend\CookiePreferencesController;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\GalleryController;
use App\Http\Controllers\Frontend\ReviewController;
use App\Http\Controllers\Frontend\QuestionController;
use App\Http\Controllers\Frontend\UtilityController;
use App\Http\Controllers\Auth\SocialAuthController;

Route::middleware('prevent.admin')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
});

Route::middleware('prevent.admin')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->name('login.authenticate');
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    // Registration with rate limiting (3 per hour per IP to prevent spam)
    Route::post('/register', [AuthController::class, 'store'])
        ->middleware('throttle:3,60')
        ->name('register.store');
    Route::get('/forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password');
    // Password reset with rate limiting (3 per hour per IP to prevent email abuse)
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])
        ->middleware('throttle:3,60')
        ->name('password.email');

    Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verify'])
        ->name('verification.verify');
    // Email verification resend with rate limiting (5 per hour to prevent email abuse)
    Route::post('/email/verification/resend', [AuthController::class, 'resendVerification'])
        ->middleware('throttle:5,60')
        ->name('verification.resend');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/auth/google', [SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
    Route::get('/auth/facebook', [SocialAuthController::class, 'redirectToFacebook'])->name('auth.facebook');
    Route::get('/auth/facebook/callback', [SocialAuthController::class, 'handleFacebookCallback'])->name('auth.facebook.callback');
});

Route::middleware(['prevent.admin', 'throttle:60,1'])->group(function () {
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
    // Review and question submissions with rate limiting (5 per minute to prevent spam)
    Route::post('/products/{slug}/review', [ReviewController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('review.store');
    Route::post('/products/{slug}/question', [QuestionController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('question.store');

    // Categories - new clean URLs
    Route::get('/categories/{slug}', [ProductController::class, 'productByCategory'])->name('category.show');

    // Shop
    Route::get('/shop', [ProductController::class, 'shop'])->name('shop');

    // Reviews and Questions with rate limiting (keep as is for API endpoints)
    Route::post('/review/{review}/helpful', [ReviewController::class, 'helpful'])
        ->middleware('throttle:10,1')
        ->name('review.helpful');
    Route::post('/question/{question}/answer', [QuestionController::class, 'storeAnswer'])
        ->middleware('throttle:5,1')
        ->name('question.answer');
    Route::post('/answer/{answer}/helpful', [QuestionController::class, 'helpful'])
        ->middleware('throttle:10,1')
        ->name('answer.helpful');

    // Bundles - fix singular to plural
    Route::get('/bundles', [ProductController::class, 'bundles'])->name('bundles.index');
    Route::get('/bundles/{slug}', function($slug) {
        return redirect()->route('product.detail', $slug, 301);
    });

    // Redirects from old URLs to new URLs (301 permanent redirects for SEO)
    Route::get('/product/{slug}', function($slug) {
        return redirect()->route('product.detail', $slug, 301);
    });
    Route::get('/category/{slug}', function($slug) {
        return redirect()->route('category.show', $slug, 301);
    });
    Route::get('/bundle/{slug}', function($slug) {
        return redirect()->route('product.detail', $slug, 301);
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
    
    Route::get('/cookie-preferences', [CookiePreferencesController::class, 'index'])->name('cookie-preferences.index');
    Route::post('/cookie-preferences/update', [CookiePreferencesController::class, 'update'])->name('cookie-preferences.update');

    // Galleries
    Route::get('/galleries', [GalleryController::class, 'index'])->name('galleries.index');
    Route::get('/gallery/{slug}', [GalleryController::class, 'show'])->name('gallery.show');

    // Redirect from old /page/{slug} to new direct slug (301 for SEO) - must be before catch-all
    Route::get('/page/{slug}', function($slug) {
        return redirect('/' . $slug, 301);
    });

    // Fallback for other pages (must be last with proper exclusions)
    Route::get('/{slug}', [PageController::class, 'showBySlug'])
        ->where('slug', '^(?!admin|api|cart|checkout|account|login|register|search|shop|products|categories|bundles|contact|faq|wishlist|unsubscribe|auth|stripe|reset-password|forgot-password|email|about-us|privacy-policy|terms-and-conditions|delivery-policy|return-policy|cookie-policy|page|galleries|gallery).*')
        ->name('page.show');

    Route::get('/faq', [\App\Http\Controllers\Frontend\FaqController::class, 'index'])->name('faq.index');
});

Route::middleware('prevent.admin')->group(function () {
    Route::get('/contact', [ContactController::class, 'index'])->name('contact');
    // Contact form with rate limiting - 5 submissions per minute to prevent spam
    Route::post('/contact', [ContactController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('contact.store');
});

Route::middleware(['prevent.admin', 'throttle:30,1'])->group(function () {
    Route::post('/wishlist/add', [WishlistController::class, 'add'])->name('wishlist.add');
    Route::post('/wishlist/remove', [WishlistController::class, 'remove'])->name('wishlist.remove');
    Route::post('/wishlist/remove-multiple', [WishlistController::class, 'removeMultiple'])->name('wishlist.remove-multiple');
    Route::get('/wishlist/list', [WishlistController::class, 'list'])->name('wishlist.list');
    Route::get('/wishlist/render', [WishlistController::class, 'render'])->name('wishlist.render');
    Route::get('/wishlist/count', [WishlistController::class, 'count'])->name('wishlist.count');
    Route::post('/wishlist/check', [WishlistController::class, 'check'])->name('wishlist.check');
});

// Cart operations with rate limiting (60 requests per minute)
Route::middleware(['auth', 'prevent.admin', 'throttle:60,1'])->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::get('/cart/list', [CartController::class, 'index'])->name('cart.list');
    Route::get('/cart/api/list', [CartController::class, 'list'])->name('cart.api.list');
    Route::get('/cart/render', [CartController::class, 'render'])->name('cart.render');
    Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');
});

Route::middleware(['auth', 'prevent.admin', 'throttle:30,1'])->group(function () {
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/add-multiple', [CartController::class, 'addMultiple'])->name('cart.add-multiple');
    Route::put('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/check', [CartController::class, 'check'])->name('cart.check');
});

Route::middleware(['auth', 'prevent.admin', 'throttle:60,1'])->group(function () {
    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::get('/details', [CheckoutController::class, 'details'])->name('details');
        Route::get('/review', [CheckoutController::class, 'review'])->name('review');
        Route::get('/payment', [CheckoutController::class, 'payment'])->name('payment');
    });
});

// Checkout endpoints with rate limiting
Route::middleware(['auth', 'prevent.admin', 'throttle:30,1'])->group(function () {
    Route::prefix('checkout')->name('checkout.')->group(function () {
        // General checkout actions - 30 requests per minute
        Route::post('/details', [CheckoutController::class, 'storeDetails'])->name('store-details');
        Route::post('/review', [CheckoutController::class, 'confirmReview'])->name('confirm-review');
        Route::post('/save-form-data', [CheckoutController::class, 'saveFormData'])->name('save-form-data');
        Route::post('/apply-coupon', [CheckoutController::class, 'applyCoupon'])->name('apply-coupon');
        Route::post('/remove-coupon', [CheckoutController::class, 'removeCoupon'])->name('remove-coupon');
        Route::post('/calculate-shipping', [CheckoutController::class, 'calculateShipping'])->name('calculate-shipping');
        Route::post('/search-address', [CheckoutController::class, 'searchAddress'])->name('search-address');
        Route::get('/get-address/{id}', [CheckoutController::class, 'getAddress'])->name('get-address');
        Route::get('/success/{order}', [CheckoutController::class, 'success'])->name('success');
        Route::get('/processing/{paymentIntent}', [CheckoutController::class, 'processing'])->name('processing');
        Route::get('/order-status/{orderNumber}', [CheckoutController::class, 'checkOrderStatus'])->name('check-order-status');
        Route::get('/payment-status/{paymentIntentId}', [CheckoutController::class, 'checkPaymentStatus'])
            ->where('paymentIntentId', 'pi_[a-zA-Z0-9_]+')
            ->name('check-payment-status');
    });
});

// Order payment processing - higher rate limit (60 per minute) for critical action
Route::middleware(['auth', 'prevent.admin', 'throttle:60,1'])->group(function () {
    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::post('/payment', [CheckoutController::class, 'processPayment'])->name('process-payment');
    });
});

// Payment intent creation - increased limit (20 per minute for better user experience)
Route::middleware(['auth', 'prevent.admin', 'throttle:20,1'])->group(function () {
    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::post('/create-payment-intent', [CheckoutController::class, 'createPaymentIntent'])->name('create-payment-intent');
    });
});

// Order processing - increased limit (10 per minute to prevent abuse while allowing legitimate retries)
Route::middleware(['auth', 'prevent.admin', 'throttle:10,1'])->group(function () {
    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::post('/process-order', [CheckoutController::class, 'processOrder'])->name('process-order');
    });
});

// Stripe webhook endpoint - NO CSRF protection (Stripe signs requests)
// POST: For Stripe to send webhook events
Route::post('/stripe/webhook', [\App\Http\Controllers\StripeWebhookController::class, 'handleWebhook'])
    ->name('stripe.webhook')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// GET: Informational endpoint (for testing/verification)
Route::get('/stripe/webhook', function() {
    return response()->json([
        'status' => 'ok',
        'message' => 'Stripe webhook endpoint is active',
        'method' => 'This endpoint accepts POST requests only',
        'webhook_url' => url('/stripe/webhook'),
        'test_endpoint' => route('stripe.webhook.test'),
        'instructions' => [
            'This is a POST-only endpoint for Stripe webhooks',
            'Stripe will send POST requests to this URL when payment events occur',
            'To test webhook configuration, visit: ' . route('stripe.webhook.test'),
            'Configure this URL in Stripe Dashboard → Developers → Webhooks'
        ]
    ], 200);
});

// Webhook test endpoint (for debugging)
Route::get('/stripe/webhook/test', function() {
    try {
        $webhookUrl = url('/stripe/webhook');
        return response()->json([
            'status' => 'ok',
            'webhook_url' => $webhookUrl,
            'webhook_secret_configured' => !empty(config('services.stripe.webhook_secret')),
            'stripe_secret_configured' => !empty(config('services.stripe.secret')),
            'environment' => app()->environment(),
            'app_url' => config('app.url'),
            'message' => 'Webhook endpoint is accessible. Configure this URL in Stripe Dashboard: ' . $webhookUrl,
            'instructions' => [
                '1. Go to Stripe Dashboard → Developers → Webhooks',
                '2. Click "Add endpoint"',
                '3. Enter URL: ' . $webhookUrl,
                '4. Select events: payment_intent.succeeded, payment_intent.payment_failed',
                '5. Copy the webhook signing secret to your .env file as STRIPE_WEBHOOK_SECRET'
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'error' => $e->getMessage(),
            'webhook_url' => url('/stripe/webhook'),
            'webhook_secret_configured' => !empty(config('services.stripe.webhook_secret')),
            'stripe_secret_configured' => !empty(config('services.stripe.secret'))
        ], 500);
    }
})->name('stripe.webhook.test');

// Account read operations (no rate limit needed for GET requests)
Route::middleware(['auth', 'prevent.admin'])->group(function () {
    Route::get('/account', [AccountController::class, 'index'])->name('account.index');
    Route::get('/account/view-profile', [AccountController::class, 'viewProfile'])->name('account.view-profile');
    Route::get('/account/edit-profile', [AccountController::class, 'editProfile'])->name('account.edit-profile');
    Route::get('/account/change-password', [AccountController::class, 'changePassword'])->name('account.change-password');
    Route::get('/account/manage-addresses', [AccountController::class, 'manageAddresses'])->name('account.manage-addresses');
    Route::get('/account/addresses/{id}/edit', [AccountController::class, 'editAddress'])->name('account.addresses.edit');
    Route::get('/account/get-address/{id}', [AccountController::class, 'getAddress'])->name('account.get-address');
    Route::get('/account/my-orders', [AccountController::class, 'myOrders'])->name('account.my-orders');
    Route::get('/account/orders/{orderNumber}', [AccountController::class, 'orderDetails'])->name('account.order-details');
});

// Account write operations with rate limiting
Route::middleware(['auth', 'prevent.admin', 'throttle:30,1'])->group(function () {
    Route::put('/account/update-profile', [AccountController::class, 'updateProfile'])->name('account.update-profile');
});

Route::middleware(['auth', 'prevent.admin', 'throttle:20,1'])->group(function () {
    Route::post('/account/addresses', [AccountController::class, 'storeAddress'])->name('account.addresses.store');
    Route::put('/account/addresses/{id}', [AccountController::class, 'updateAddress'])->name('account.addresses.update');
    Route::delete('/account/addresses/{id}', [AccountController::class, 'destroyAddress'])->name('account.addresses.destroy');
    Route::put('/account/addresses/{id}/set-default', [AccountController::class, 'setDefaultAddress'])->name('account.addresses.set-default');
    Route::post('/account/search-address', [AccountController::class, 'searchAddress'])->name('account.search-address');
});

Route::middleware(['auth', 'prevent.admin', 'throttle:10,1'])->group(function () {
    Route::put('/account/update-password', [AccountController::class, 'updatePassword'])->name('account.update-password');
});

// Order actions with rate limiting
Route::middleware(['auth', 'prevent.admin', 'throttle:10,1'])->group(function () {
    Route::post('/account/orders/{orderNumber}/reorder', [AccountController::class, 'reorder'])->name('account.orders.reorder');
    Route::post('/account/orders/{orderNumber}/cancel', [AccountController::class, 'cancelOrder'])->name('account.orders.cancel');
});

Route::post('/api/log-client-error', [UtilityController::class, 'logClientError'])
    ->name('api.log-client-error')
    ->middleware('web');
