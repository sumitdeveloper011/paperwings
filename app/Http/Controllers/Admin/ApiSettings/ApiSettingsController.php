<?php

namespace App\Http\Controllers\Admin\ApiSettings;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Helpers\SettingHelper;
use App\Helpers\CacheHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApiSettingsController extends Controller
{
    // Display the API settings page
    public function index(): View
    {
        $settings = SettingHelper::all();

        return view('admin.api-settings.index', [
            'settings' => $settings
        ]);
    }

    // Update API settings
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            // Platform Fee Settings
            'platform_fee_enabled' => 'nullable|in:0,1',
            'platform_fee_percentage' => 'nullable|numeric|min:0|max:100',
            'pass_stripe_fee_to_customer' => 'nullable|in:0,1',

            // Google OAuth
            'google_login_enabled' => 'nullable|in:0,1',

            // Facebook OAuth
            'facebook_login_enabled' => 'nullable|in:0,1',
        ]);
        
        // Update Platform Fee Settings (default: disabled - recommended for direct sellers)
        $platformFeeEnabled = $validated['platform_fee_enabled'] ?? '0';
        $this->updateSetting('platform_fee_enabled', $platformFeeEnabled);
        
        // If platform fee is disabled, reset percentage to 0
        if ($platformFeeEnabled == '0') {
            $this->updateSetting('platform_fee_percentage', '0');
        } else {
            $this->updateSetting('platform_fee_percentage', $validated['platform_fee_percentage'] ?? '0');
        }

        $this->updateSetting('pass_stripe_fee_to_customer', $validated['pass_stripe_fee_to_customer'] ?? '0');

        // Update Google OAuth Settings
        $this->updateSetting('google_login_enabled', $validated['google_login_enabled'] ?? '0');

        // Update Facebook OAuth Settings
        $this->updateSetting('facebook_login_enabled', $validated['facebook_login_enabled'] ?? '0');

        // Clear settings cache after update
        CacheHelper::forgetAllSettings();

        return redirect()->route('admin.api-settings.index')
            ->with('success', 'API Settings updated successfully!');
    }

    // Update or create a setting
    private function updateSetting(string $key, ?string $value): void
    {
        Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}
