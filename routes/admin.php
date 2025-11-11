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
use App\Http\Controllers\Admin\Settings\SettingsController;

// Guest routes (login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->name('authenticate');
});

// Protected admin routes
Route::middleware(['auth', 'admin.auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Category routes
    Route::get('categories/get-categories-for-epos-now', [CategoryController::class, 'getCategoriesForEposNow'])->name('categories.getCategoriesForEposNow');
    Route::resource('categories', CategoryController::class);
    Route::patch('categories/{category}/status', [CategoryController::class, 'updateStatus'])->name('categories.updateStatus');
    
    // SubCategory routes
    Route::resource('subcategories', SubCategoryController::class);
    Route::patch('subcategories/{subcategory}/status', [SubCategoryController::class, 'updateStatus'])->name('subcategories.updateStatus');
    
    // Brand routes
    Route::resource('brands', BrandController::class);
    
    // Product routes
    Route::get('products/get-products-for-epos-now', [ProductController::class, 'getProductsForEposNow'])->name('products.getProductsForEposNow');
    Route::resource('products', ProductController::class);
    Route::patch('products/{product}/status', [ProductController::class, 'updateStatus'])->name('products.updateStatus');
    Route::get('products/subcategories/get', [ProductController::class, 'getSubCategories'])->name('products.getSubCategories');
    
    // Slider routes
    Route::resource('sliders', SliderController::class);
    Route::patch('sliders/{slider}/status', [SliderController::class, 'updateStatus'])->name('sliders.updateStatus');
    Route::patch('sliders/{slider}/move-up', [SliderController::class, 'moveUp'])->name('sliders.moveUp');
    Route::patch('sliders/{slider}/move-down', [SliderController::class, 'moveDown'])->name('sliders.moveDown');
    Route::post('sliders/update-order', [SliderController::class, 'updateOrder'])->name('sliders.updateOrder');
    Route::post('sliders/{slider}/duplicate', [SliderController::class, 'duplicate'])->name('sliders.duplicate');
    
    // Page routes
    Route::resource('pages', PageController::class);
    Route::post('pages/upload-image', [PageController::class, 'uploadImage'])->name('pages.uploadImage');
    
    // Settings routes
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');
});

