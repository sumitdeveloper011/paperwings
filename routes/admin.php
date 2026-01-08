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
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/chart-data', [DashboardController::class, 'getChartData'])->name('dashboard.chartData');

    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('get-categories-for-epos-now', [CategoryController::class, 'getCategoriesForEposNow'])->name('getCategoriesForEposNow');
        Route::get('import-status', [CategoryController::class, 'checkImportStatus'])->name('importStatus');
    });
    Route::patch('categories/{category}/status', [CategoryController::class, 'updateStatus'])->name('categories.updateStatus');
    Route::resource('categories', CategoryController::class);

    Route::resource('subcategories', SubCategoryController::class);
    Route::patch('subcategories/{subcategory}/status', [SubCategoryController::class, 'updateStatus'])->name('subcategories.updateStatus');

    Route::resource('brands', BrandController::class);

    Route::prefix('products')->name('products.')->group(function () {
        Route::get('get-products-for-epos-now', [ProductController::class, 'getProductsForEposNow'])->name('getProductsForEposNow');
        Route::get('import-status', [ProductController::class, 'checkImportStatus'])->name('importStatus');
        Route::post('retry-failed-products', [ProductController::class, 'retryFailedProducts'])->name('retryFailedProducts');
    });
    Route::get('products/import-all-images', [ProductController::class, 'importAllProductImages'])->name('products.importAllImages');
    Route::patch('products/{product}/status', [ProductController::class, 'updateStatus'])->name('products.updateStatus');
    Route::get('products/subcategories/get', [ProductController::class, 'getSubCategories'])->name('products.getSubCategories');
    Route::resource('products', ProductController::class);

    Route::resource('sliders', SliderController::class);
    Route::patch('sliders/{slider}/status', [SliderController::class, 'updateStatus'])->name('sliders.updateStatus');
    Route::patch('sliders/{slider}/move-up', [SliderController::class, 'moveUp'])->name('sliders.moveUp');
    Route::patch('sliders/{slider}/move-down', [SliderController::class, 'moveDown'])->name('sliders.moveDown');
    Route::post('sliders/update-order', [SliderController::class, 'updateOrder'])->name('sliders.updateOrder');
    Route::post('sliders/{slider}/duplicate', [SliderController::class, 'duplicate'])->name('sliders.duplicate');

    Route::resource('pages', PageController::class);
    Route::post('pages/upload-image', [PageController::class, 'uploadImage'])->name('pages.uploadImage');

    Route::resource('about-sections', AboutSectionController::class);
    Route::patch('about-sections/{about_section}/status', [AboutSectionController::class, 'updateStatus'])->name('about-sections.updateStatus');

    Route::resource('contacts', ContactController::class)->except(['create', 'store']);

    Route::resource('coupons', CouponController::class);
    Route::patch('coupons/{coupon}/status', [CouponController::class, 'updateStatus'])->name('coupons.updateStatus');

    Route::resource('regions', RegionController::class);
    Route::patch('regions/{region}/status', [RegionController::class, 'updateStatus'])->name('regions.updateStatus');

    Route::resource('shipping-prices', ShippingPriceController::class);
    Route::patch('shipping-prices/{shipping_price}/status', [ShippingPriceController::class, 'updateStatus'])->name('shipping-prices.updateStatus');

    Route::resource('testimonials', TestimonialController::class);
    Route::patch('testimonials/{testimonial}/status', [TestimonialController::class, 'updateStatus'])->name('testimonials.updateStatus');

    Route::resource('special-offers-banners', SpecialOffersBannerController::class);
    Route::patch('special-offers-banners/{special_offers_banner}/status', [SpecialOffersBannerController::class, 'updateStatus'])->name('special-offers-banners.updateStatus');

    Route::resource('faqs', FaqController::class);
    Route::patch('faqs/{faq}/status', [FaqController::class, 'updateStatus'])->name('faqs.updateStatus');

    Route::resource('reviews', ReviewController::class)->except(['create', 'store', 'edit', 'update']);
    Route::patch('reviews/{review}/status', [ReviewController::class, 'updateStatus'])->name('reviews.updateStatus');

    Route::prefix('product-faqs')->name('product-faqs.')->group(function () {
        Route::get('search-products', [ProductFaqController::class, 'searchProducts'])->name('searchProducts');
    });
    Route::resource('product-faqs', ProductFaqController::class);
    Route::patch('product-faqs/{product_faq}/status', [ProductFaqController::class, 'updateStatus'])->name('product-faqs.updateStatus');

    Route::resource('tags', TagController::class);

    Route::resource('questions', QuestionController::class)->except(['create', 'store', 'edit', 'update']);
    Route::patch('questions/{question}/status', [QuestionController::class, 'updateStatus'])->name('questions.updateStatus');
    Route::resource('answers', AnswerController::class)->except(['create', 'store', 'edit', 'update']);
    Route::patch('answers/{answer}/status', [AnswerController::class, 'updateStatus'])->name('answers.updateStatus');

    Route::prefix('bundles')->name('bundles.')->group(function () {
        Route::get('search-products', [BundleController::class, 'searchProducts'])->name('searchProducts');
    });
    Route::resource('bundles', BundleController::class);
    Route::patch('bundles/{bundle}/status', [BundleController::class, 'updateStatus'])->name('bundles.updateStatus');

    Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('analytics/product-views', [AnalyticsController::class, 'productViews'])->name('analytics.productViews');
    Route::get('analytics/conversion', [AnalyticsController::class, 'conversion'])->name('analytics.conversion');
    Route::get('analytics/sales', [AnalyticsController::class, 'sales'])->name('analytics.sales');

    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::post('settings/test-instagram', [SettingsController::class, 'testInstagram'])->name('settings.test-instagram');

    Route::get('api-settings', [ApiSettingsController::class, 'index'])->name('api-settings.index');
    Route::put('api-settings', [ApiSettingsController::class, 'update'])->name('api-settings.update');

    // Roles and Permissions Management
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);

    Route::resource('users', UserController::class);
    Route::patch('users/{user}/status', [UserController::class, 'updateStatus'])->name('users.updateStatus');

    Route::resource('orders', OrderController::class)->except(['create', 'store', 'edit', 'update']);
    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::patch('orders/{order}/payment-status', [OrderController::class, 'updatePaymentStatus'])->name('orders.updatePaymentStatus');

    Route::resource('subscriptions', SubscriptionController::class)->except(['create', 'store', 'edit', 'update']);
    Route::patch('subscriptions/{subscription}/status', [SubscriptionController::class, 'updateStatus'])->name('subscriptions.updateStatus');
    Route::get('subscriptions/export', [SubscriptionController::class, 'export'])->name('subscriptions.export');

    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('notifications/render', [NotificationController::class, 'render'])->name('notifications.render');
    Route::post('notifications/{order}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');

    Route::get('profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
    Route::put('profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.updateAvatar');
    Route::put('profile/two-factor', [ProfileController::class, 'updateTwoFactor'])->name('profile.updateTwoFactor');
});

