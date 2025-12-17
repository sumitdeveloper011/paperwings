<?php

namespace App\Providers;

use App\Repositories\BrandRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\Interfaces\BrandRepositoryInterface;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Repositories\Interfaces\SliderRepositoryInterface;
use App\Repositories\Interfaces\SubCategoryRepositoryInterface;
use App\Repositories\ProductRepository;
use App\Repositories\SliderRepository;
use App\Repositories\SubCategoryRepository;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Repository bindings
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(SubCategoryRepositoryInterface::class, SubCategoryRepository::class);
        $this->app->bind(BrandRepositoryInterface::class, BrandRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(SliderRepositoryInterface::class, SliderRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        
        // Share settings with frontend header
        view()->composer('include.frontend.header', function ($view) {
            $settings = \App\Models\Setting::pluck('value', 'key')->toArray();
            
            // Get first phone number
            $phone = null;
            if (isset($settings['phones']) && is_string($settings['phones'])) {
                $phones = json_decode($settings['phones'], true) ?? [];
                $phone = !empty($phones) ? $phones[0] : null;
            } elseif (isset($settings['phones']) && is_array($settings['phones'])) {
                $phone = !empty($settings['phones']) ? $settings['phones'][0] : null;
            }
            
            // Get social links (only those with URLs)
            $socialLinks = [];
            if (!empty($settings['social_facebook'])) {
                $socialLinks['facebook'] = $settings['social_facebook'];
            }
            if (!empty($settings['social_twitter'])) {
                $socialLinks['twitter'] = $settings['social_twitter'];
            }
            if (!empty($settings['social_instagram'])) {
                $socialLinks['instagram'] = $settings['social_instagram'];
            }
            if (!empty($settings['social_linkedin'])) {
                $socialLinks['linkedin'] = $settings['social_linkedin'];
            }
            if (!empty($settings['social_youtube'])) {
                $socialLinks['youtube'] = $settings['social_youtube'];
            }
            if (!empty($settings['social_pinterest'])) {
                $socialLinks['pinterest'] = $settings['social_pinterest'];
            }
            
            // Get active categories for menu
            $categories = \App\Models\Category::active()
                ->ordered()
                ->withCount(['products' => function($query) {
                    $query->where('status', 1);
                }])
                ->having('products_count', '>', 0) // Only categories with products
                ->take(10) // Limit to 10 categories
                ->get();
            
            // Get user data if authenticated
            $userData = null;
            if (auth()->check()) {
                $user = auth()->user();
                $userData = [
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar_url' => $user->avatar_url,
                    'has_avatar' => $user->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar),
                    'initial' => strtoupper(substr($user->first_name ?? ($user->name ?? 'U'), 0, 1))
                ];
            }
            
            $view->with([
                'headerPhone' => $phone,
                'socialLinks' => $socialLinks,
                'headerCategories' => $categories,
                'userData' => $userData
            ]);
        });
    }
}
