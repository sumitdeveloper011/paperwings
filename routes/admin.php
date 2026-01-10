<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\AuthController;
use App\Http\Controllers\Admin\Dashboard\DashboardController;
use App\Http\Controllers\Admin\Category\CategoryController;
use App\Http\Controllers\Admin\SubCategory\SubCategoryController;
use App\Http\Controllers\Admin\Brand\BrandController;
use App\Http\Controllers\Admin\Product\ProductController;
use App\Http\Controllers\Admin\Slider\SliderController;
use App\Http\Controllers\Admin\Page\PageController;
use App\Http\Controllers\Admin\Setting\SettingsController;
use App\Http\Controllers\Admin\Profile\ProfileController;
use App\Http\Controllers\Admin\Coupon\CouponController;
use App\Http\Controllers\Admin\User\UserController;
use App\Http\Controllers\Admin\Order\OrderController;
use App\Http\Controllers\Admin\Subscription\SubscriptionController;
use App\Http\Controllers\Admin\Notification\NotificationController;
use App\Http\Controllers\Admin\Contact\ContactController;
use App\Http\Controllers\Admin\ShippingPrice\ShippingPriceController;
use App\Http\Controllers\Admin\Region\RegionController;
use App\Http\Controllers\Admin\Testimonial\TestimonialController;
use App\Http\Controllers\Admin\SpecialOffersBanner\SpecialOffersBannerController;
use App\Http\Controllers\Admin\Faq\FaqController;
use App\Http\Controllers\Admin\Review\ReviewController;
use App\Http\Controllers\Admin\ProductFaq\ProductFaqController;
use App\Http\Controllers\Admin\Tag\TagController;
use App\Http\Controllers\Admin\Question\QuestionController;
use App\Http\Controllers\Admin\Answer\AnswerController;
use App\Http\Controllers\Admin\Bundle\BundleController;
use App\Http\Controllers\Admin\Analytics\AnalyticsController;
use App\Http\Controllers\Admin\AboutSection\AboutSectionController;
use App\Http\Controllers\Admin\ApiSettings\ApiSettingsController;
use App\Http\Controllers\Admin\Role\RoleController;
use App\Http\Controllers\Admin\Permission\PermissionController;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->name('authenticate');
});

Route::middleware(['auth', 'admin.auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard - requires dashboard.view permission
    Route::middleware('permission:dashboard.view')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/chart-data', [DashboardController::class, 'getChartData'])->name('dashboard.chartData');
    });

    // Categories - requires categories permissions
    Route::middleware('permission:categories.view')->group(function () {
        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('get-categories-for-epos-now', [CategoryController::class, 'getCategoriesForEposNow'])->name('getCategoriesForEposNow');
            Route::get('import-status', [CategoryController::class, 'checkImportStatus'])->name('importStatus');
        });
        Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
    });
    Route::middleware('permission:categories.create')->group(function () {
        Route::get('categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
    });
    Route::middleware('permission:categories.edit')->group(function () {
        Route::get('categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::patch('categories/{category}/status', [CategoryController::class, 'updateStatus'])->name('categories.updateStatus');
    });
    Route::middleware('permission:categories.delete')->group(function () {
        Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    });

    // Subcategories - Hidden for now, kept for future use
    // Route::resource('subcategories', SubCategoryController::class);
    // Route::patch('subcategories/{subcategory}/status', [SubCategoryController::class, 'updateStatus'])->name('subcategories.updateStatus');

    // Brands - no permissions in seeder, accessible to all admin roles
    Route::resource('brands', BrandController::class);

    // Products - requires products permissions
    Route::middleware('permission:products.view')->group(function () {
        Route::prefix('products')->name('products.')->group(function () {
            Route::get('get-products-for-epos-now', [ProductController::class, 'getProductsForEposNow'])->name('getProductsForEposNow');
            Route::get('import-status', [ProductController::class, 'checkImportStatus'])->name('importStatus');
        });
        Route::get('products', [ProductController::class, 'index'])->name('products.index');
        Route::get('products/{product}', [ProductController::class, 'show'])->name('products.show');
        Route::get('products/subcategories/get', [ProductController::class, 'getSubCategories'])->name('products.getSubCategories');
    });
    Route::middleware('permission:products.create')->group(function () {
        Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('products', [ProductController::class, 'store'])->name('products.store');
        Route::get('products/import-all-images', [ProductController::class, 'importAllProductImages'])->name('products.importAllImages');
        Route::post('products/retry-failed-products', [ProductController::class, 'retryFailedProducts'])->name('products.retryFailedProducts');
    });
    Route::middleware('permission:products.edit')->group(function () {
        Route::get('products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::patch('products/{product}/status', [ProductController::class, 'updateStatus'])->name('products.updateStatus');
    });
    Route::middleware('permission:products.delete')->group(function () {
        Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    });

    // Sliders - requires sliders permissions
    Route::middleware('permission:sliders.view')->group(function () {
        Route::get('sliders', [SliderController::class, 'index'])->name('sliders.index');
        Route::get('sliders/{slider}', [SliderController::class, 'show'])->name('sliders.show');
    });
    Route::middleware('permission:sliders.create')->group(function () {
        Route::get('sliders/create', [SliderController::class, 'create'])->name('sliders.create');
        Route::post('sliders', [SliderController::class, 'store'])->name('sliders.store');
        Route::post('sliders/update-order', [SliderController::class, 'updateOrder'])->name('sliders.updateOrder');
        Route::post('sliders/{slider}/duplicate', [SliderController::class, 'duplicate'])->name('sliders.duplicate');
    });
    Route::middleware('permission:sliders.edit')->group(function () {
        Route::get('sliders/{slider}/edit', [SliderController::class, 'edit'])->name('sliders.edit');
        Route::put('sliders/{slider}', [SliderController::class, 'update'])->name('sliders.update');
        Route::patch('sliders/{slider}/status', [SliderController::class, 'updateStatus'])->name('sliders.updateStatus');
        Route::patch('sliders/{slider}/move-up', [SliderController::class, 'moveUp'])->name('sliders.moveUp');
        Route::patch('sliders/{slider}/move-down', [SliderController::class, 'moveDown'])->name('sliders.moveDown');
    });
    Route::middleware('permission:sliders.delete')->group(function () {
        Route::delete('sliders/{slider}', [SliderController::class, 'destroy'])->name('sliders.destroy');
    });

    // Pages - requires pages permissions
    Route::middleware('permission:pages.view')->group(function () {
        Route::get('pages', [PageController::class, 'index'])->name('pages.index');
        Route::get('pages/{page}', [PageController::class, 'show'])->name('pages.show');
    });
    Route::middleware('permission:pages.create')->group(function () {
        Route::get('pages/create', [PageController::class, 'create'])->name('pages.create');
        Route::post('pages', [PageController::class, 'store'])->name('pages.store');
        Route::post('pages/upload-image', [PageController::class, 'uploadImage'])->name('pages.uploadImage');
    });
    Route::middleware('permission:pages.edit')->group(function () {
        Route::get('pages/{page}/edit', [PageController::class, 'edit'])->name('pages.edit');
        Route::put('pages/{page}', [PageController::class, 'update'])->name('pages.update');
    });
    Route::middleware('permission:pages.delete')->group(function () {
        Route::delete('pages/{page}', [PageController::class, 'destroy'])->name('pages.destroy');
    });

    // About Sections - requires about-sections permissions
    Route::middleware('permission:about-sections.view')->group(function () {
        Route::get('about-sections', [AboutSectionController::class, 'index'])->name('about-sections.index');
        Route::get('about-sections/{about_section}', [AboutSectionController::class, 'show'])->name('about-sections.show');
    });
    Route::middleware('permission:about-sections.create')->group(function () {
        Route::get('about-sections/create', [AboutSectionController::class, 'create'])->name('about-sections.create');
        Route::post('about-sections', [AboutSectionController::class, 'store'])->name('about-sections.store');
    });
    Route::middleware('permission:about-sections.edit')->group(function () {
        Route::get('about-sections/{about_section}/edit', [AboutSectionController::class, 'edit'])->name('about-sections.edit');
        Route::put('about-sections/{about_section}', [AboutSectionController::class, 'update'])->name('about-sections.update');
        Route::patch('about-sections/{about_section}/status', [AboutSectionController::class, 'updateStatus'])->name('about-sections.updateStatus');
    });
    Route::middleware('permission:about-sections.delete')->group(function () {
        Route::delete('about-sections/{about_section}', [AboutSectionController::class, 'destroy'])->name('about-sections.destroy');
    });

    // Contacts - no permissions in seeder, accessible to all admin roles
    Route::resource('contacts', ContactController::class)->except(['create', 'store']);

    // Coupons - requires coupons permissions
    Route::middleware('permission:coupons.view')->group(function () {
        Route::get('coupons', [CouponController::class, 'index'])->name('coupons.index');
        Route::get('coupons/{coupon}', [CouponController::class, 'show'])->name('coupons.show');
    });
    Route::middleware('permission:coupons.create')->group(function () {
        Route::get('coupons/create', [CouponController::class, 'create'])->name('coupons.create');
        Route::post('coupons', [CouponController::class, 'store'])->name('coupons.store');
    });
    Route::middleware('permission:coupons.edit')->group(function () {
        Route::get('coupons/{coupon}/edit', [CouponController::class, 'edit'])->name('coupons.edit');
        Route::put('coupons/{coupon}', [CouponController::class, 'update'])->name('coupons.update');
        Route::patch('coupons/{coupon}/status', [CouponController::class, 'updateStatus'])->name('coupons.updateStatus');
    });
    Route::middleware('permission:coupons.delete')->group(function () {
        Route::delete('coupons/{coupon}', [CouponController::class, 'destroy'])->name('coupons.destroy');
    });

    // Regions - no permissions in seeder, accessible to all admin roles
    Route::resource('regions', RegionController::class);
    Route::patch('regions/{region}/status', [RegionController::class, 'updateStatus'])->name('regions.updateStatus');

    // Shipping Prices - Only SuperAdmin can access
    Route::middleware('role:SuperAdmin')->group(function () {
        Route::resource('shipping-prices', ShippingPriceController::class);
        Route::patch('shipping-prices/{shipping_price}/status', [ShippingPriceController::class, 'updateStatus'])->name('shipping-prices.updateStatus');
    });

    // Testimonials - requires testimonials permissions
    Route::middleware('permission:testimonials.view')->group(function () {
        Route::get('testimonials', [TestimonialController::class, 'index'])->name('testimonials.index');
        Route::get('testimonials/{testimonial}', [TestimonialController::class, 'show'])->name('testimonials.show');
    });
    Route::middleware('permission:testimonials.create')->group(function () {
        Route::get('testimonials/create', [TestimonialController::class, 'create'])->name('testimonials.create');
        Route::post('testimonials', [TestimonialController::class, 'store'])->name('testimonials.store');
    });
    Route::middleware('permission:testimonials.edit')->group(function () {
        Route::get('testimonials/{testimonial}/edit', [TestimonialController::class, 'edit'])->name('testimonials.edit');
        Route::put('testimonials/{testimonial}', [TestimonialController::class, 'update'])->name('testimonials.update');
        Route::patch('testimonials/{testimonial}/status', [TestimonialController::class, 'updateStatus'])->name('testimonials.updateStatus');
    });
    Route::middleware('permission:testimonials.delete')->group(function () {
        Route::delete('testimonials/{testimonial}', [TestimonialController::class, 'destroy'])->name('testimonials.destroy');
    });

    // Special Offers - requires special-offers permissions
    Route::middleware('permission:special-offers.view')->group(function () {
        Route::get('special-offers-banners', [SpecialOffersBannerController::class, 'index'])->name('special-offers-banners.index');
        Route::get('special-offers-banners/{special_offers_banner}', [SpecialOffersBannerController::class, 'show'])->name('special-offers-banners.show');
    });
    Route::middleware('permission:special-offers.create')->group(function () {
        Route::get('special-offers-banners/create', [SpecialOffersBannerController::class, 'create'])->name('special-offers-banners.create');
        Route::post('special-offers-banners', [SpecialOffersBannerController::class, 'store'])->name('special-offers-banners.store');
    });
    Route::middleware('permission:special-offers.edit')->group(function () {
        Route::get('special-offers-banners/{special_offers_banner}/edit', [SpecialOffersBannerController::class, 'edit'])->name('special-offers-banners.edit');
        Route::put('special-offers-banners/{special_offers_banner}', [SpecialOffersBannerController::class, 'update'])->name('special-offers-banners.update');
        Route::patch('special-offers-banners/{special_offers_banner}/status', [SpecialOffersBannerController::class, 'updateStatus'])->name('special-offers-banners.updateStatus');
    });
    Route::middleware('permission:special-offers.delete')->group(function () {
        Route::delete('special-offers-banners/{special_offers_banner}', [SpecialOffersBannerController::class, 'destroy'])->name('special-offers-banners.destroy');
    });

    // FAQs - no permissions in seeder, accessible to all admin roles
    Route::resource('faqs', FaqController::class);
    Route::patch('faqs/{faq}/status', [FaqController::class, 'updateStatus'])->name('faqs.updateStatus');

    // Reviews - no permissions in seeder, accessible to all admin roles
    Route::resource('reviews', ReviewController::class)->except(['create', 'store', 'edit', 'update']);
    Route::patch('reviews/{review}/status', [ReviewController::class, 'updateStatus'])->name('reviews.updateStatus');

    // Product FAQs - no permissions in seeder, accessible to all admin roles
    Route::prefix('product-faqs')->name('product-faqs.')->group(function () {
        Route::get('search-products', [ProductFaqController::class, 'searchProducts'])->name('searchProducts');
    });
    Route::resource('product-faqs', ProductFaqController::class);
    Route::patch('product-faqs/{product_faq}/status', [ProductFaqController::class, 'updateStatus'])->name('product-faqs.updateStatus');

    // Tags - no permissions in seeder, accessible to all admin roles
    Route::resource('tags', TagController::class);

    // Questions - no permissions in seeder, accessible to all admin roles
    Route::resource('questions', QuestionController::class)->except(['create', 'store', 'edit', 'update']);
    Route::patch('questions/{question}/status', [QuestionController::class, 'updateStatus'])->name('questions.updateStatus');

    // Answers - no permissions in seeder, accessible to all admin roles
    Route::resource('answers', AnswerController::class)->except(['create', 'store', 'edit', 'update']);
    Route::patch('answers/{answer}/status', [AnswerController::class, 'updateStatus'])->name('answers.updateStatus');

    // Bundles - no permissions in seeder, accessible to all admin roles
    Route::prefix('bundles')->name('bundles.')->group(function () {
        Route::get('search-products', [BundleController::class, 'searchProducts'])->name('searchProducts');
    });
    Route::resource('bundles', BundleController::class);
    Route::patch('bundles/{bundle}/status', [BundleController::class, 'updateStatus'])->name('bundles.updateStatus');

    // Analytics - requires analytics.view permission
    Route::middleware('permission:analytics.view')->group(function () {
        Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
        Route::get('analytics/product-views', [AnalyticsController::class, 'productViews'])->name('analytics.productViews');
        Route::get('analytics/conversion', [AnalyticsController::class, 'conversion'])->name('analytics.conversion');
        Route::get('analytics/sales', [AnalyticsController::class, 'sales'])->name('analytics.sales');
    });

    // Settings - requires settings permissions
    Route::middleware('permission:settings.view')->group(function () {
        Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    });
    Route::middleware('permission:settings.edit')->group(function () {
        Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');
        Route::post('settings/test-instagram', [SettingsController::class, 'testInstagram'])->name('settings.test-instagram');
    });

    // API Settings - Only SuperAdmin can access
    Route::middleware('role:SuperAdmin')->group(function () {
        Route::get('api-settings', [ApiSettingsController::class, 'index'])->name('api-settings.index');
        Route::put('api-settings', [ApiSettingsController::class, 'update'])->name('api-settings.update');
    });

    // Roles and Permissions Management - Only SuperAdmin can access
    Route::middleware('role:SuperAdmin')->group(function () {
        Route::resource('roles', RoleController::class);
        Route::resource('permissions', PermissionController::class);
    });

    // Users - requires users permissions
    // Note: Specific routes (create) must come before parameterized routes ({user})
    Route::middleware('permission:users.create')->group(function () {
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
    });
    Route::middleware('permission:users.view')->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
    });
    Route::middleware('permission:users.edit')->group(function () {
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::patch('users/{user}/status', [UserController::class, 'updateStatus'])->name('users.updateStatus');
    });
    Route::middleware('permission:users.delete')->group(function () {
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // Orders - requires orders permissions
    Route::middleware('permission:orders.view')->group(function () {
        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    });
    Route::middleware('permission:orders.edit')->group(function () {
        Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
        Route::patch('orders/{order}/payment-status', [OrderController::class, 'updatePaymentStatus'])->name('orders.updatePaymentStatus');
    });
    Route::middleware('permission:orders.delete')->group(function () {
        Route::delete('orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
    });

    // Subscriptions - no permissions in seeder, accessible to all admin roles
    Route::resource('subscriptions', SubscriptionController::class)->except(['create', 'store', 'edit', 'update']);
    Route::patch('subscriptions/{subscription}/status', [SubscriptionController::class, 'updateStatus'])->name('subscriptions.updateStatus');
    Route::get('subscriptions/export', [SubscriptionController::class, 'export'])->name('subscriptions.export');

    // Notifications - no permissions in seeder, accessible to all admin roles
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('notifications/render', [NotificationController::class, 'render'])->name('notifications.render');
    Route::post('notifications/{order}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');

    // Profile - accessible to all authenticated admin users
    Route::get('profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
    Route::put('profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.updateAvatar');
    Route::put('profile/two-factor', [ProfileController::class, 'updateTwoFactor'])->name('profile.updateTwoFactor');
});
