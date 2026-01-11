<?php

namespace App\Http\Controllers\Admin\Contact;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    // Display a listing of contact messages
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $status = $request->get('status');

        $query = ContactMessage::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        $messages = $query->orderBy('created_at', 'desc')->paginate(15);

        $pageTitle = 'Contact Messages';
        $pageSubtitle = 'Manage customer inquiries and messages';
        $pageIcon = 'fas fa-envelope';

        return view('admin.contact.index', compact('messages', 'search', 'status', 'pageTitle', 'pageSubtitle', 'pageIcon'));
    }

    // Display the specified contact message
    public function show(ContactMessage $contact): View
    {
        // Mark as viewed if not already viewed
        if (!$contact->admin_viewed_at) {
            $contact->update(['admin_viewed_at' => now()]);
        }

        return view('admin.contact.show', compact('contact'));
    }

    // Update the status and notes of a contact message
    public function update(Request $request, ContactMessage $contact): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,solved,closed',
            'admin_notes' => 'nullable|string|max:5000',
        ]);

        $contact->update($validated);

        return redirect()->route('admin.contacts.show', $contact)
            ->with('success', 'Contact message updated successfully!');
    }

    // Remove the specified contact message
    public function destroy(ContactMessage $contact): RedirectResponse
    {
        $contact->delete();

        return redirect()->route('admin.contacts.index')
            ->with('success', 'Contact message deleted successfully!');
    }
}
