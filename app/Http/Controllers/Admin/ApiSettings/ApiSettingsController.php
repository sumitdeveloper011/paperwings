<?php

namespace App\Http\Controllers\Admin\ApiSettings;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Helpers\SettingHelper;
use App\Helpers\CacheHelper;
use App\Services\ApiKeyEncryptionService;
use App\Services\InstagramService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class ApiSettingsController extends Controller
{
    // Display the API settings page
    public function index(): View
    {
        $settings = SettingHelper::all();

        // Mask sensitive API keys for display
        $maskedSettings = [];
        foreach ($settings as $key => $value) {
            if (ApiKeyEncryptionService::shouldEncrypt($key) && !empty($value)) {
                $maskedSettings[$key] = ApiKeyEncryptionService::mask($value);
            } else {
                $maskedSettings[$key] = $value;
            }
        }

        return view('admin.api-settings.index', [
            'settings' => $settings,
            'maskedSettings' => $maskedSettings
        ]);
    }

    // Update API settings
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            // EPOSNOW Keys
            'eposnow_api_key' => 'nullable|string|max:500',
            'eposnow_api_secret' => 'nullable|string|max:500',
            'eposnow_api_base' => 'nullable|string|max:500',

            // Stripe Keys
            'stripe_key' => 'nullable|string|max:500',
            'stripe_secret' => 'nullable|string|max:500',
            'stripe_webhook_secret' => 'nullable|string|max:500',
            
            // Platform Fee Settings
            'platform_fee_enabled' => 'nullable|in:0,1',
            'platform_fee_percentage' => 'nullable|numeric|min:0|max:100',
            'pass_stripe_fee_to_customer' => 'nullable|in:0,1',

            // Google OAuth
            'google_client_id' => 'nullable|string|max:500',
            'google_client_secret' => 'nullable|string|max:500',
            'google_login_enabled' => 'nullable|in:0,1',

            // Facebook OAuth
            'facebook_client_id' => 'nullable|string|max:500',
            'facebook_client_secret' => 'nullable|string|max:500',
            'facebook_login_enabled' => 'nullable|in:0,1',

            // NZ Post
            'nzpost_api_key' => 'nullable|string|max:500',

            // Google Maps
            'google_map_api_key' => 'nullable|string|max:500',

            // Instagram
            'instagram_app_id' => 'nullable|string|max:255',
            'instagram_app_secret' => 'nullable|string|max:255',
            'instagram_access_token' => 'nullable|string|max:500',
            'instagram_user_id' => 'nullable|string|max:255',
        ]);

        // Update EPOSNOW Keys
        $this->updateSetting('eposnow_api_key', $validated['eposnow_api_key'] ?? '');
        $this->updateSetting('eposnow_api_secret', $validated['eposnow_api_secret'] ?? '');
        $this->updateSetting('eposnow_api_base', $validated['eposnow_api_base'] ?? '');

        // Update Stripe Keys
        $this->updateSetting('stripe_key', $validated['stripe_key'] ?? '');
        $this->updateSetting('stripe_secret', $validated['stripe_secret'] ?? '');
        $this->updateSetting('stripe_webhook_secret', $validated['stripe_webhook_secret'] ?? '');
        
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

        $this->updateSetting('pass_stripe_fee_to_customer', $validated['pass_stripe_fee_to_customer'] ?? '0');

        // Update Google OAuth Settings
        $this->updateSetting('google_client_id', $validated['google_client_id'] ?? '');
        $this->updateSetting('google_client_secret', $validated['google_client_secret'] ?? '');
        $this->updateSetting('google_login_enabled', $validated['google_login_enabled'] ?? '0');

        // Update Facebook OAuth Settings
        $this->updateSetting('facebook_client_id', $validated['facebook_client_id'] ?? '');
        $this->updateSetting('facebook_client_secret', $validated['facebook_client_secret'] ?? '');
        $this->updateSetting('facebook_login_enabled', $validated['facebook_login_enabled'] ?? '0');

        // Update NZ Post Settings
        $this->updateSetting('nzpost_api_key', $validated['nzpost_api_key'] ?? '');

        // Update Google Maps Settings
        $this->updateSetting('google_map_api_key', $validated['google_map_api_key'] ?? '');

        // Update Instagram Settings
        $this->updateSetting('instagram_app_id', $validated['instagram_app_id'] ?? '');
        $this->updateSetting('instagram_app_secret', $validated['instagram_app_secret'] ?? '');
        $this->updateSetting('instagram_access_token', $validated['instagram_access_token'] ?? '');
        $this->updateSetting('instagram_user_id', $validated['instagram_user_id'] ?? '');

        // Clear settings cache after update
        CacheHelper::forgetAllSettings();

        // Clear Instagram cache if credentials were updated
        if (isset($validated['instagram_access_token']) || isset($validated['instagram_user_id'])) {
            $instagramService = new InstagramService();
            $instagramService->clearCache();
        }

        return redirect()->route('admin.api-settings.index')
            ->with('success', 'API Settings updated successfully!');
    }

    // Test Instagram API connection
    public function testInstagram(Request $request): JsonResponse
    {
        $request->validate([
            'instagram_app_id' => 'nullable|string',
            'instagram_app_secret' => 'nullable|string',
            'instagram_access_token' => 'nullable|string',
            'instagram_user_id' => 'nullable|string',
        ]);

        // Temporarily update settings for testing (with encryption)
        if ($request->has('instagram_app_id')) {
            $value = ApiKeyEncryptionService::encrypt('instagram_app_id', $request->instagram_app_id);
            Setting::updateOrCreate(['key' => 'instagram_app_id'], ['value' => $value]);
        }
        if ($request->has('instagram_app_secret')) {
            $value = ApiKeyEncryptionService::encrypt('instagram_app_secret', $request->instagram_app_secret);
            Setting::updateOrCreate(['key' => 'instagram_app_secret'], ['value' => $value]);
        }
        if ($request->has('instagram_access_token')) {
            $value = ApiKeyEncryptionService::encrypt('instagram_access_token', $request->instagram_access_token);
            Setting::updateOrCreate(['key' => 'instagram_access_token'], ['value' => $value]);
        }
        if ($request->has('instagram_user_id')) {
            Setting::updateOrCreate(['key' => 'instagram_user_id'], ['value' => $request->instagram_user_id]);
        }

        $instagramService = new InstagramService();
        $result = $instagramService->testConnection();

        return response()->json($result);
    }

    // Update or create a setting
    private function updateSetting(string $key, ?string $value): void
    {
        // If value is masked (unchanged), don't update it
        if (ApiKeyEncryptionService::isMasked($value)) {
            return;
        }

        // Encrypt sensitive keys before storing
        $encryptedValue = ApiKeyEncryptionService::encrypt($key, $value);
        
        Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $encryptedValue]
        );
    }
}
