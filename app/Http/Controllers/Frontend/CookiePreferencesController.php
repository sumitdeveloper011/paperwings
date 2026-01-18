<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\CookieConsentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CookiePreferencesController extends Controller
{
    public function __construct(
        private CookieConsentService $cookieConsentService
    ) {}

    public function index()
    {
        $preferences = $this->cookieConsentService->getPreferences();
        
        return view('frontend.cookie-preferences.index', compact('preferences'));
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'analytics_cookies' => 'boolean',
            'marketing_cookies' => 'boolean',
            'functionality_cookies' => 'boolean',
        ]);

        $this->cookieConsentService->savePreferences($validated, auth()->id());

        return $this->jsonSuccess('Cookie preferences saved successfully.', [
            'preferences' => $this->cookieConsentService->getPreferences()
        ]);
    }
}
