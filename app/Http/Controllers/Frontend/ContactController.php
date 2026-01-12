<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Helpers\SettingHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    // Display the contact page
    public function index(): View
    {
        $title = 'Contact Us';
        
        $settings = SettingHelper::all();
        
        $address = $settings['address'] ?? null;
        $googleMap = $settings['google_map'] ?? null;
        $googleMapApiKey = $settings['google_map_api_key'] ?? null;
        
        $phones = SettingHelper::getArraySetting($settings, 'phones');
        $emails = SettingHelper::getArraySetting($settings, 'emails');
        
        $workingHours = $settings['working_hours'] ?? null;
        
        $socialLinks = SettingHelper::extractSocialLinks($settings);

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
