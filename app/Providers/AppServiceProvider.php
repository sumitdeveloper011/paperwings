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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
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
                $settings = \Illuminate\Support\Facades\Cache::remember('header_settings', 3600, function() {
                    try {
                        return \App\Models\Setting::pluck('value', 'key')->toArray();
                    } catch (\Exception $e) {
                        Log::warning('Failed to fetch header settings: ' . $e->getMessage());
                        return [];
                    }
                });

                $phone = null;
                if (isset($settings['phones']) && is_string($settings['phones'])) {
                    $phones = json_decode($settings['phones'], true) ?? [];
                    $phone = !empty($phones) ? $phones[0] : null;
                } elseif (isset($settings['phones']) && is_array($settings['phones'])) {
                    $phone = !empty($settings['phones']) ? $settings['phones'][0] : null;
                }

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

                // Optimized: Use activeProducts relationship with whereHas (fetch all for mega menu)
                $categories = \Illuminate\Support\Facades\Cache::remember('header_categories', 1800, function() {
                    try {
                        return \App\Models\Category::active()
                            ->whereHas('activeProducts')
                            ->withCount('activeProducts')
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
                            'has_avatar' => $user->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar),
                            'initial' => strtoupper(substr($user->first_name ?? ($user->name ?? 'U'), 0, 1))
                        ];
                    } catch (\Exception $e) {
                        Log::warning('Failed to fetch user data for header: ' . $e->getMessage());
                        $userData = null;
                    }
                }

                $logo = $settings['logo'] ?? null;
                $headerEmail = null;
                if (isset($settings['emails']) && is_string($settings['emails'])) {
                    $emails = json_decode($settings['emails'], true) ?? [];
                    $headerEmail = !empty($emails) ? $emails[0] : null;
                } elseif (isset($settings['emails']) && is_array($settings['emails'])) {
                    $headerEmail = !empty($settings['emails']) ? $settings['emails'][0] : null;
                }

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
                $settings = \Illuminate\Support\Facades\Cache::remember('footer_settings', 3600, function() {
                    try {
                        return \App\Models\Setting::pluck('value', 'key')->toArray();
                    } catch (\Exception $e) {
                        Log::warning('Failed to fetch footer settings: ' . $e->getMessage());
                        return [];
                    }
                });

                $logo = $settings['logo'] ?? null;

                $footerTagline = $settings['footer_tagline'] ?? $settings['meta_description'] ?? 'We Promise We\'ll Get Back To You Promptly- Your Gifting Needs Are Always On Our Minds!';

                $footerSocialLinks = [];
                if (!empty($settings['social_facebook'])) {
                    $footerSocialLinks['facebook'] = $settings['social_facebook'];
                }
                if (!empty($settings['social_twitter'])) {
                    $footerSocialLinks['twitter'] = $settings['social_twitter'];
                }
                if (!empty($settings['social_instagram'])) {
                    $footerSocialLinks['instagram'] = $settings['social_instagram'];
                }
                if (!empty($settings['social_linkedin'])) {
                    $footerSocialLinks['linkedin'] = $settings['social_linkedin'];
                }
                if (!empty($settings['social_youtube'])) {
                    $footerSocialLinks['youtube'] = $settings['social_youtube'];
                }
                if (!empty($settings['social_pinterest'])) {
                    $footerSocialLinks['pinterest'] = $settings['social_pinterest'];
                }

                $footerPages = \Illuminate\Support\Facades\Cache::remember('footer_pages', 1800, function() {
                    try {
                        $pageSlugs = ['about-us', 'return-policy', 'privacy-policy', 'delivery-policy', 'terms-and-conditions', 'cookie-policy'];
                        $pages = \App\Models\Page::select('id', 'title', 'slug')
                            ->whereIn('slug', $pageSlugs)
                            ->get();

                        // Sort by custom order (SQLite doesn't support FIELD function)
                        $driverName = \Illuminate\Support\Facades\DB::getDriverName();
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

                $aboutSection = \Illuminate\Support\Facades\Cache::remember('footer_about_section', 1800, function() {
                    try {
                        return \App\Models\AboutSection::active()
                            ->ordered()
                            ->first();
                    } catch (\Exception $e) {
                        Log::warning('Failed to fetch footer about section: ' . $e->getMessage());
                        return null;
                    }
                });

                $footerCategories = \Illuminate\Support\Facades\Cache::remember('footer_categories', 1800, function() {
                    try {
                        return \App\Models\Category::active()
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

                $phones = [];
                if (isset($settings['phones']) && is_string($settings['phones'])) {
                    $phones = json_decode($settings['phones'], true) ?? [];
                } elseif (isset($settings['phones']) && is_array($settings['phones'])) {
                    $phones = $settings['phones'];
                }
                $primaryPhone = !empty($phones) ? $phones[0] : null;

                $emails = [];
                if (isset($settings['emails']) && is_string($settings['emails'])) {
                    $emails = json_decode($settings['emails'], true) ?? [];
                } elseif (isset($settings['emails']) && is_array($settings['emails'])) {
                    $emails = $settings['emails'];
                }
                $primaryEmail = !empty($emails) ? $emails[0] : null;

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
                    'footerAboutSection' => $aboutSection,
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
                    'footerAboutSection' => null,
                ]);
            }
        });

        view()->composer('layouts.admin.*', function ($view) {
            try {
                $settings = \Illuminate\Support\Facades\Cache::remember('admin_settings', 3600, function() {
                    try {
                        return \App\Models\Setting::pluck('value', 'key')->toArray();
                    } catch (\Exception $e) {
                        Log::warning('Failed to fetch admin settings: ' . $e->getMessage());
                        return [];
                    }
                });

                $view->with([
                    'settings' => $settings,
                    'siteLogo' => !empty($settings['logo']) ? asset('storage/' . $settings['logo']) : asset('assets/images/logo.svg'),
                    'siteFavicon' => !empty($settings['icon']) ? asset('storage/' . $settings['icon']) : asset('assets/images/favicon.ico'),
                    'siteName' => $settings['site_name'] ?? 'PAPERWINGS',
                ]);
            } catch (\Exception $e) {
                Log::error('Admin layout view composer error: ' . $e->getMessage(), [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);
                // Provide default values to prevent view errors
                $view->with([
                    'settings' => [],
                    'siteLogo' => asset('assets/images/logo.svg'),
                    'siteFavicon' => asset('assets/images/favicon.ico'),
                    'siteName' => 'PAPERWINGS',
                ]);
            }
        });

        view()->composer('include.admin.*', function ($view) {
            try {
                $settings = \Illuminate\Support\Facades\Cache::remember('admin_settings', 3600, function() {
                    try {
                        return \App\Models\Setting::pluck('value', 'key')->toArray();
                    } catch (\Exception $e) {
                        Log::warning('Failed to fetch admin settings: ' . $e->getMessage());
                        return [];
                    }
                });

                $view->with([
                    'settings' => $settings,
                    'siteLogo' => !empty($settings['logo']) ? asset('storage/' . $settings['logo']) : asset('assets/images/logo.svg'),
                    'siteFavicon' => !empty($settings['icon']) ? asset('storage/' . $settings['icon']) : asset('assets/images/favicon.ico'),
                    'siteName' => $settings['site_name'] ?? 'PAPERWINGS',
                ]);
            } catch (\Exception $e) {
                Log::error('Admin include view composer error: ' . $e->getMessage(), [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);
                // Provide default values to prevent view errors
                $view->with([
                    'settings' => [],
                    'siteLogo' => asset('assets/images/logo.svg'),
                    'siteFavicon' => asset('assets/images/favicon.ico'),
                    'siteName' => 'PAPERWINGS',
                ]);
            }
        });
    }
}
