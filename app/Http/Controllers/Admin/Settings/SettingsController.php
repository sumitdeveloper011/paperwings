<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
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
        $validated = $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,ico|max:512',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string|max:255',
            'meta_author' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
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
        ]);

        // Handle logo upload
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

        // Update meta tags
        $this->updateSetting('meta_title', $validated['meta_title'] ?? '');
        $this->updateSetting('meta_description', $validated['meta_description'] ?? '');
        $this->updateSetting('meta_keywords', $validated['meta_keywords'] ?? '');
        $this->updateSetting('meta_author', $validated['meta_author'] ?? '');

        // Update contact info
        $this->updateSetting('address', $validated['address'] ?? '');
        
        // Update emails (store as JSON array)
        $emails = array_filter($validated['emails'] ?? []);
        $this->updateSetting('emails', !empty($emails) ? json_encode($emails) : null);
        
        // Update phones (store as JSON array)
        $phones = array_filter($validated['phones'] ?? []);
        $this->updateSetting('phones', !empty($phones) ? json_encode($phones) : null);

        // Update social links
        $this->updateSetting('social_facebook', $validated['social_facebook'] ?? '');
        $this->updateSetting('social_twitter', $validated['social_twitter'] ?? '');
        $this->updateSetting('social_instagram', $validated['social_instagram'] ?? '');
        $this->updateSetting('social_linkedin', $validated['social_linkedin'] ?? '');
        $this->updateSetting('social_youtube', $validated['social_youtube'] ?? '');
        $this->updateSetting('social_pinterest', $validated['social_pinterest'] ?? '');

        return redirect()->route('admin.settings.index')
            ->with('success', 'Settings updated successfully!');
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

