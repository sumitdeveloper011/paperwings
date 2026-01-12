<?php

namespace App\Http\Controllers\Admin\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use App\Services\ImageService;

class ProfileController extends Controller
{
    protected ImageService $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    // Display the profile page
    public function index(): View
    {
        $user = Auth::user();
        
        return view('admin.profile.index', compact('user'));
    }

    // Update the user's profile
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'Name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already taken.',
        ]);

        // Split name into first_name and last_name
        $nameParts = explode(' ', $validated['name'], 2);
        $updateData = [
            'first_name' => $nameParts[0] ?? '',
            'last_name' => $nameParts[1] ?? '',
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'bio' => $validated['bio'] ?? null,
        ];

        $user->update($updateData);

        return redirect()->route('admin.profile.index')
            ->with('success', 'Profile updated successfully!');
    }

    // Update the user's password
    public function updatePassword(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ], [
            'current_password.required' => 'Current password is required.',
            'new_password.required' => 'New password is required.',
            'new_password.min' => 'Password must be at least 8 characters.',
            'new_password.confirmed' => 'Password confirmation does not match.',
        ]);

        // Verify current password
        if (!Hash::check($validated['current_password'], $user->password)) {
            return redirect()->route('admin.profile.index')
                ->withErrors(['current_password' => 'Current password is incorrect.'])
                ->withInput();
        }

        // Update password
        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        return redirect()->route('admin.profile.index')
            ->with('success', 'Password updated successfully!');
    }

    // Update the user's avatar
    public function updateAvatar(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'avatar.required' => 'Please select an image.',
            'avatar.image' => 'The file must be an image.',
            'avatar.max' => 'The image must not exceed 2MB.',
        ]);

        if ($request->hasFile('avatar')) {
            $imagePath = $this->imageService->uploadSimple(
                $request->file('avatar'),
                'avatars',
                $user->avatar
            );
            if ($imagePath) {
                $user->update(['avatar' => $imagePath]);
            }
        }

        return redirect()->route('admin.profile.index')
            ->with('success', 'Profile picture updated successfully!');
    }

    // Update two-factor authentication status
    public function updateTwoFactor(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'two_factor_enabled' => 'nullable|boolean',
        ]);

        $user->update([
            'two_factor_enabled' => $request->has('two_factor_enabled') && $request->two_factor_enabled == '1',
        ]);

        $status = $user->two_factor_enabled ? 'enabled' : 'disabled';
        
        return redirect()->route('admin.profile.index')
            ->with('success', "Two-factor authentication {$status} successfully!");
    }
}

