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
use App\Models\Category;
use App\Models\Page;
use App\Models\Setting;
use App\Helpers\SettingHelper;
use App\Helpers\CacheHelper;
use App\Helpers\ViewComposerHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
class AppServiceProvider extends ServiceProvider
{
    // Register any application services
    public function register(): void
    {
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(SubCategoryRepositoryInterface::class, SubCategoryRepository::class);
        $this->app->bind(BrandRepositoryInterface::class, BrandRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(SliderRepositoryInterface::class, SliderRepository::class);
    }

    // Bootstrap any application services
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        if (app()->environment('local')) {
            DB::listen(function ($query) {
                if ($query->time > 100) {
                    Log::warning('Slow Query Detected', [
                        'sql' => $query->sql,
                        'bindings' => $query->bindings,
                        'time' => $query->time . 'ms',
                    ]);
                }

                static $queryCounts = [];
                $queryKey = md5($query->sql);
                $queryCounts[$queryKey] = ($queryCounts[$queryKey] ?? 0) + 1;

                if ($queryCounts[$queryKey] > 5) {
                    Log::info('Potential N+1 Query Pattern', [
                        'sql' => $query->sql,
                        'count' => $queryCounts[$queryKey],
                        'time' => $query->time . 'ms',
                    ]);
                }
            });
        }

        view()->composer('include.frontend.header', function ($view) {
            try {
                $settings = SettingHelper::all();

                $phone = SettingHelper::getFirstFromArraySetting($settings, 'phones');
                $socialLinks = SettingHelper::extractSocialLinks($settings);

                // Optimized: Use activeProducts relationship with whereHas (fetch all for mega menu)
                $categories = Cache::remember('header_categories', 1800, function() {
                    try {
                        return Category::active()
                            ->withCount(['products' => function($query) {
                                $query->where('status', 1);
                            }])
                            ->select('id', 'name', 'slug')
                            ->ordered()
                            ->get();
                    } catch (\Exception $e) {
                        Log::warning('Failed to fetch header categories: ' . $e->getMessage());
                        return collect([]);
                    }
                });

                $userData = null;
                if (Auth::check()) {
                    try {
                        $user = Auth::user();
                        $userData = [
                            'name' => $user->name,
                            'email' => $user->email,
                            'avatar_url' => $user->avatar_url,
                            'has_avatar' => $user->avatar && Storage::disk('public')->exists($user->avatar),
                            'initial' => strtoupper(substr($user->first_name ?? ($user->name ?? 'U'), 0, 1))
                        ];
                    } catch (\Exception $e) {
                        Log::warning('Failed to fetch user data for header: ' . $e->getMessage());
                        $userData = null;
                    }
                }

                $logo = $settings['logo'] ?? null;
                $headerEmail = SettingHelper::getFirstFromArraySetting($settings, 'emails');

                $view->with([
                    'headerPhone' => $phone,
                    'headerEmail' => $headerEmail,
                    'headerLogo' => $logo,
                    'socialLinks' => $socialLinks,
                    'headerCategories' => $categories,
                    'userData' => $userData
                ]);
            } catch (\Exception $e) {
                Log::error('Header view composer error: ' . $e->getMessage(), [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);
                // Provide default values to prevent view errors
                $view->with([
                    'headerPhone' => null,
                    'headerEmail' => null,
                    'headerLogo' => null,
                    'socialLinks' => [],
                    'headerCategories' => collect([]),
                    'userData' => null
                ]);
            }
        });

        view()->composer('include.frontend.footer', function ($view) {
            try {
                $settings = Cache::remember('footer_settings', 3600, function() {
                    try {
                        return SettingHelper::all();
                    } catch (\Exception $e) {
                        Log::warning('Failed to fetch footer settings: ' . $e->getMessage());
                        return [];
                    }
                });

                $logo = $settings['logo'] ?? null;

                $footerTagline = $settings['footer_tagline'] ?? $settings['meta_description'] ?? 'We Promise We\'ll Get Back To You Promptly- Your Gifting Needs Are Always On Our Minds!';

                $footerSocialLinks = SettingHelper::extractSocialLinks($settings);

                $footerPages = Cache::remember('footer_pages', 1800, function() {
                    try {
                        $pageSlugs = ['about-us', 'return-policy', 'privacy-policy', 'delivery-policy', 'terms-and-conditions', 'cookie-policy'];
                        $pages = Page::select('id', 'title', 'slug')
                            ->whereIn('slug', $pageSlugs)
                            ->get();

                        // Sort by custom order (SQLite doesn't support FIELD function)
                        $driverName = DB::getDriverName();
                        if ($driverName === 'sqlite') {
                            // Sort in PHP for SQLite
                            return $pages->sortBy(function($page) use ($pageSlugs) {
                                return array_search($page->slug, $pageSlugs);
                            })->values();
                        } else {
                            // Use FIELD for MySQL
                            return $pages->sortBy(function($page) use ($pageSlugs) {
                                return array_search($page->slug, $pageSlugs);
                            })->values();
                        }
                    } catch (\Exception $e) {
                        Log::warning('Failed to fetch footer pages: ' . $e->getMessage());
                        return collect([]);
                    }
                });

                $footerCategories = Cache::remember('footer_categories', 1800, function() {
                    try {
                        return Category::active()
                            ->whereHas('activeProducts')
                            ->withCount('activeProducts')
                            ->select('id', 'name', 'slug')
                            ->ordered()
                            ->take(5)
                            ->get();
                    } catch (\Exception $e) {
                        Log::warning('Failed to fetch footer categories: ' . $e->getMessage());
                        return collect([]);
                    }
                });

                $primaryPhone = SettingHelper::getFirstFromArraySetting($settings, 'phones');
                $primaryEmail = SettingHelper::getFirstFromArraySetting($settings, 'emails');

                $workingHours = $settings['working_hours'] ?? 'Monday - Friday: 9:00-20:00' . "\n" . 'Saturday: 11:00 - 15:00';
                if ($workingHours) {
                    $workingHours = nl2br(htmlspecialchars(trim($workingHours), ENT_QUOTES, 'UTF-8'), false);
                }

                $address = $settings['address'] ?? null;

                $copyrightText = $settings['copyright_text'] ?? 'Copyright © ' . date('Y') . ' Paper Wings. All rights reserved.';

                $view->with([
                    'footerLogo' => $logo,
                    'footerTagline' => $footerTagline,
                    'footerSocialLinks' => $footerSocialLinks,
                    'footerPages' => $footerPages,
                    'footerCategories' => $footerCategories,
                    'footerPhone' => $primaryPhone,
                    'footerEmail' => $primaryEmail,
                    'footerWorkingHours' => $workingHours,
                    'footerAddress' => $address,
                    'footerCopyright' => $copyrightText,
                ]);
            } catch (\Exception $e) {
                Log::error('Footer view composer error: ' . $e->getMessage(), [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);
                // Provide default values to prevent view errors
                $view->with([
                    'footerLogo' => null,
                    'footerTagline' => 'We Promise We\'ll Get Back To You Promptly- Your Gifting Needs Are Always On Our Minds!',
                    'footerSocialLinks' => [],
                    'footerPages' => collect([]),
                    'footerCategories' => collect([]),
                    'footerPhone' => null,
                    'footerEmail' => null,
                    'footerWorkingHours' => 'Monday - Friday: 9:00-20:00<br>Saturday: 11:00 - 15:00',
                    'footerAddress' => null,
                    'footerCopyright' => 'Copyright © ' . date('Y') . ' Paper Wings. All rights reserved.',
                ]);
            }
        });

        $adminSettingsCallback = function() {
            try {
                return SettingHelper::all();
            } catch (\Exception $e) {
                Log::warning('Failed to fetch admin settings: ' . $e->getMessage());
                return [];
            }
        };

        view()->composer(['layouts.admin.*', 'include.admin.*'], function ($view) use ($adminSettingsCallback) {
            try {
                $settings = Cache::remember('admin_settings', 3600, $adminSettingsCallback);

                $view->with([
                    'settings' => $settings,
                    'siteLogo' => !empty($settings['logo']) ? asset('storage/' . $settings['logo']) : asset('assets/frontend/images/logo.png'),
                    'siteFavicon' => !empty($settings['icon']) ? asset('storage/' . $settings['icon']) : asset('assets/frontend/images/icon.png'),
                    'siteName' => $settings['site_name'] ?? 'PAPERWINGS',
                ]);
            } catch (\Exception $e) {
                Log::error('Admin view composer error: ' . $e->getMessage(), [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);
                // Provide default values to prevent view errors
                $view->with([
                    'settings' => [],
                    'siteLogo' => asset('assets/frontend/images/logo.png'),
                    'siteFavicon' => asset('assets/frontend/images/icon.png'),
                    'siteName' => 'PAPERWINGS',
                ]);
            }
        });
    }
}
