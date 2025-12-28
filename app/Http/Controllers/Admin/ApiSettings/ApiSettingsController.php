<?php

namespace App\Http\Controllers\Admin\ApiSettings;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApiSettingsController extends Controller
{
    // Display the API settings page
    public function index(): View
    {
        $settings = Setting::pluck('value', 'key')->toArray();

        return view('admin.api-settings.index', compact('settings'));
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
            
            // Google OAuth
            'google_client_id' => 'nullable|string|max:255',
            'google_client_secret' => 'nullable|string|max:255',
            'google_login_enabled' => 'nullable|in:0,1',
            
            // Facebook OAuth
            'facebook_client_id' => 'nullable|string|max:255',
            'facebook_client_secret' => 'nullable|string|max:255',
            'facebook_login_enabled' => 'nullable|in:0,1',
        ]);

        // Update EPOSNOW Keys
        $this->updateSetting('eposnow_api_key', $validated['eposnow_api_key'] ?? '');
        $this->updateSetting('eposnow_api_secret', $validated['eposnow_api_secret'] ?? '');
        $this->updateSetting('eposnow_api_base', $validated['eposnow_api_base'] ?? '');

        // Update Stripe Keys
        $this->updateSetting('stripe_key', $validated['stripe_key'] ?? '');
        $this->updateSetting('stripe_secret', $validated['stripe_secret'] ?? '');
        $this->updateSetting('stripe_webhook_secret', $validated['stripe_webhook_secret'] ?? '');

        // Update Google OAuth Settings
        $this->updateSetting('google_client_id', $validated['google_client_id'] ?? '');
        $this->updateSetting('google_client_secret', $validated['google_client_secret'] ?? '');
        $this->updateSetting('google_login_enabled', $validated['google_login_enabled'] ?? '0');

        // Update Facebook OAuth Settings
        $this->updateSetting('facebook_client_id', $validated['facebook_client_id'] ?? '');
        $this->updateSetting('facebook_client_secret', $validated['facebook_client_secret'] ?? '');
        $this->updateSetting('facebook_login_enabled', $validated['facebook_login_enabled'] ?? '0');

        // Clear settings cache after update
        \Illuminate\Support\Facades\Cache::forget('admin_settings');
        \Illuminate\Support\Facades\Cache::forget('app_settings');

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
