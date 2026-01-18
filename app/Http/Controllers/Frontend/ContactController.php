<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\StoreContactRequest;
use App\Mail\ContactNotificationMail;
use App\Mail\ContactAdminNotificationMail;
use App\Services\NotificationService;
use App\Models\ContactMessage;
use App\Services\GoogleAnalyticsService;
use App\Helpers\SettingHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ContactController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

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
    public function store(StoreContactRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Handle image upload if provided
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $folderUuid = Str::uuid()->toString();
            $folderPath = 'contact-messages/' . $folderUuid;
            
            // Create directory if it doesn't exist
            if (!Storage::disk('public')->exists($folderPath)) {
                Storage::disk('public')->makeDirectory($folderPath, 0755, true);
            }
            
            $imageName = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $imagePath = Storage::disk('public')->putFileAs($folderPath, $image, $imageName);
            
            $validated['image'] = $imagePath;
        }

        $contactMessage = ContactMessage::create($validated);

        try {
            $analyticsService = app(GoogleAnalyticsService::class);
            $analyticsService->trackEvent('contact_form_submit', [
                'form_type' => 'contact',
                'user_id' => Auth::id() ?? null
            ]);
        } catch (\Exception $e) {
            Log::warning('Analytics tracking failed for contact form', [
                'error' => $e->getMessage()
            ]);
        }

        try {
            // Queue confirmation email to client (ContactNotificationMail implements ShouldQueue)
            Mail::to($contactMessage->email)->queue(new ContactNotificationMail($contactMessage));
        } catch (\Exception $e) {
            Log::error('Failed to queue contact confirmation email', [
                'error' => $e->getMessage(),
                'contact_message_id' => $contactMessage->id
            ]);
        }

        // Create admin notification (this will also send admin email if enabled)
        try {
            $this->notificationService->createContactNotification($contactMessage);
        } catch (\Exception $e) {
            Log::error('Failed to create contact notification: ' . $e->getMessage());
        }

        return redirect()->route('contact')
            ->with('success', 'Thank you for contacting us! We will get back to you soon. A confirmation email has been sent to your email address - please check your inbox and spam folder.');
    }
}
