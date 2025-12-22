<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\InstagramService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index(): View
    {
        $settings = Setting::pluck('value', 'key')->toArray();

        // Decode JSON arrays for emails and phones
        if (isset($settings['emails']) && is_string($settings['emails'])) {
            $settings['emails'] = json_decode($settings['emails'], true) ?? [];
        } elseif (!isset($settings['emails'])) {
            $settings['emails'] = [];
        }

        if (isset($settings['phones']) && is_string($settings['phones'])) {
            $settings['phones'] = json_decode($settings['phones'], true) ?? [];
        } elseif (!isset($settings['phones'])) {
            $settings['phones'] = [];
        }

        return view('admin.settings.setting', compact('settings'));
    }

    /**
     * Update settings.
     */
    public function update(Request $request): RedirectResponse
    {
        // Check if logo already exists
        $logoExists = Setting::where('key', 'logo')->whereNotNull('value')->exists();

        $validated = $request->validate([
            'logo' => ($logoExists ? 'nullable' : 'required') . '|image|mimes:jpeg,png,jpg,gif|max:2048',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,ico|max:512',
            'meta_title' => 'required|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords' => 'required|string|max:255',
            'meta_author' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'google_map' => 'required|string',
            'emails' => 'nullable|array',
            'emails.*' => 'nullable|email|max:255',
            'phones' => 'nullable|array',
            'phones.*' => 'nullable|string|max:20',
            'social_facebook' => 'nullable|url|max:255',
            'social_twitter' => 'nullable|url|max:255',
            'social_instagram' => 'nullable|url|max:255',
            'social_linkedin' => 'nullable|url|max:255',
            'social_youtube' => 'nullable|url|max:255',
            'social_pinterest' => 'nullable|url|max:255',
            'instagram_app_id' => 'nullable|string|max:255',
            'instagram_app_secret' => 'nullable|string|max:255',
            'instagram_access_token' => 'nullable|string|max:500',
            'instagram_user_id' => 'nullable|string|max:255',
            'footer_tagline' => 'nullable|string|max:500',
            'working_hours' => 'nullable|string|max:500',
            'copyright_text' => 'nullable|string|max:255',
        ], [
            'logo.required' => 'Logo is required.',
            'logo.image' => 'Logo must be an image.',
            'logo.max' => 'Logo must not exceed 2MB.',
            'meta_title.required' => 'Meta title is required.',
            'meta_keywords.required' => 'Meta keywords are required.',
            'meta_author.required' => 'Meta author is required.',
            'address.required' => 'Address is required.',
            'google_map.required' => 'Google Map embed is required.',
        ]);

        // Filter and validate emails and phones - remove empty values
        $emails = array_values(array_filter(array_map(function($email) {
            return trim($email ?? '');
        }, $validated['emails'] ?? []), function($email) {
            return !empty($email);
        }));

        $phones = array_values(array_filter(array_map(function($phone) {
            return trim($phone ?? '');
        }, $validated['phones'] ?? []), function($phone) {
            return !empty($phone);
        }));

        // Custom validation: At least one email or phone is required
        if (empty($emails) && empty($phones)) {
            return redirect()->route('admin.settings.index')
                ->withErrors(['emails' => 'At least one email address or phone number is required.'])
                ->withInput();
        }

        // Handle logo upload (required field - must be uploaded if doesn't exist)
        if ($request->hasFile('logo')) {
            $image = $request->file('logo');
            $imageName = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('settings', $imageName, 'public');

            // Delete old logo if exists
            $oldLogo = Setting::where('key', 'logo')->first();
            if ($oldLogo && $oldLogo->value) {
                Storage::disk('public')->delete($oldLogo->value);
            }

            $this->updateSetting('logo', $imagePath);
        } elseif (!$logoExists) {
            // If logo doesn't exist and no file uploaded, return error
            return redirect()->route('admin.settings.index')
                ->withErrors(['logo' => 'Logo is required.'])
                ->withInput();
        }

        // Handle icon upload
        if ($request->hasFile('icon')) {
            $image = $request->file('icon');
            $imageName = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('settings', $imageName, 'public');

            // Delete old icon if exists
            $oldIcon = Setting::where('key', 'icon')->first();
            if ($oldIcon && $oldIcon->value) {
                Storage::disk('public')->delete($oldIcon->value);
            }

            $this->updateSetting('icon', $imagePath);
        }

        // Update meta tags (required fields)
        $this->updateSetting('meta_title', $validated['meta_title']);
        $this->updateSetting('meta_description', $validated['meta_description'] ?? '');
        $this->updateSetting('meta_keywords', $validated['meta_keywords']);
        $this->updateSetting('meta_author', $validated['meta_author']);

        // Update contact info (required fields)
        $this->updateSetting('address', $validated['address']);
        $this->updateSetting('google_map', $validated['google_map']);

        // Update emails (store as JSON array) - use the already filtered arrays
        $this->updateSetting('emails', !empty($emails) ? json_encode($emails) : null);

        // Update phones (store as JSON array) - use the already filtered arrays
        $this->updateSetting('phones', !empty($phones) ? json_encode($phones) : null);

        // Update social links
        $this->updateSetting('social_facebook', $validated['social_facebook'] ?? '');
        $this->updateSetting('social_twitter', $validated['social_twitter'] ?? '');
        $this->updateSetting('social_instagram', $validated['social_instagram'] ?? '');
        $this->updateSetting('social_linkedin', $validated['social_linkedin'] ?? '');
        $this->updateSetting('social_youtube', $validated['social_youtube'] ?? '');
        $this->updateSetting('social_pinterest', $validated['social_pinterest'] ?? '');

        // Update Instagram API credentials
        $this->updateSetting('instagram_app_id', $validated['instagram_app_id'] ?? '');
        $this->updateSetting('instagram_app_secret', $validated['instagram_app_secret'] ?? '');
        $this->updateSetting('instagram_access_token', $validated['instagram_access_token'] ?? '');
        $this->updateSetting('instagram_user_id', $validated['instagram_user_id'] ?? '');

        // Clear Instagram cache if credentials were updated
        if (isset($validated['instagram_access_token']) || isset($validated['instagram_user_id'])) {
            $instagramService = new InstagramService();
            $instagramService->clearCache();
        }

        // Update footer settings
        $this->updateSetting('footer_tagline', $validated['footer_tagline'] ?? '');
        $this->updateSetting('working_hours', $validated['working_hours'] ?? '');
        
        // Process copyright text - replace {YEAR} with current year
        $copyrightText = $validated['copyright_text'] ?? '';
        if (!empty($copyrightText)) {
            $copyrightText = str_replace('{YEAR}', date('Y'), $copyrightText);
        }
        $this->updateSetting('copyright_text', $copyrightText);

        return redirect()->route('admin.settings.index')
            ->with('success', 'Settings updated successfully!');
    }

    /**
     * Test Instagram API connection
     */
    public function testInstagram(Request $request): JsonResponse
    {
        $request->validate([
            'instagram_app_id' => 'nullable|string',
            'instagram_app_secret' => 'nullable|string',
            'instagram_access_token' => 'nullable|string',
            'instagram_user_id' => 'nullable|string',
        ]);

        // Temporarily update settings for testing
        if ($request->has('instagram_app_id')) {
            Setting::updateOrCreate(['key' => 'instagram_app_id'], ['value' => $request->instagram_app_id]);
        }
        if ($request->has('instagram_app_secret')) {
            Setting::updateOrCreate(['key' => 'instagram_app_secret'], ['value' => $request->instagram_app_secret]);
        }
        if ($request->has('instagram_access_token')) {
            Setting::updateOrCreate(['key' => 'instagram_access_token'], ['value' => $request->instagram_access_token]);
        }
        if ($request->has('instagram_user_id')) {
            Setting::updateOrCreate(['key' => 'instagram_user_id'], ['value' => $request->instagram_user_id]);
        }

        $instagramService = new InstagramService();
        $result = $instagramService->testConnection();

        return response()->json($result);
    }

    /**
     * Update or create a setting.
     */
    private function updateSetting(string $key, ?string $value): void
    {
        Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}

