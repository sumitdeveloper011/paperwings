<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    // Display the contact page
    public function index(): View
    {
        $title = 'Contact Us';
        
        $settings = Setting::pluck('value', 'key')->toArray();
        
        $address = $settings['address'] ?? null;
        $googleMap = $settings['google_map'] ?? null;
        $googleMapApiKey = $settings['google_map_api_key'] ?? null;
        
        $phones = [];
        if (isset($settings['phones']) && is_string($settings['phones'])) {
            $phones = json_decode($settings['phones'], true) ?? [];
        } elseif (isset($settings['phones']) && is_array($settings['phones'])) {
            $phones = $settings['phones'];
        }
        
        $emails = [];
        if (isset($settings['emails']) && is_string($settings['emails'])) {
            $emails = json_decode($settings['emails'], true) ?? [];
        } elseif (isset($settings['emails']) && is_array($settings['emails'])) {
            $emails = $settings['emails'];
        }
        
        $workingHours = $settings['working_hours'] ?? null;
        
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

        return view('frontend.contact.contact', compact(
            'title',
            'address',
            'googleMap',
            'googleMapApiKey',
            'phones',
            'emails',
            'workingHours',
            'socialLinks'
        ));
    }

    // Store a contact message
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ], [
            'name.required' => 'Please enter your name.',
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'subject.required' => 'Please enter a subject.',
            'message.required' => 'Please enter your message.',
            'message.max' => 'Your message is too long. Maximum 5000 characters allowed.',
        ]);

        ContactMessage::create($validated);

        return redirect()->route('contact')
            ->with('success', 'Thank you for contacting us! We will get back to you soon.');
    }
}
