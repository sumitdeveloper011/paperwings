<?php

namespace App\Http\Controllers\Admin\Contact;

use App\Http\Controllers\Controller;
use App\Mail\ContactStatusUpdateMail;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ContactController extends Controller
{
    // Display a listing of contact messages
    public function index(Request $request): View|JsonResponse
    {
        $search = trim($request->get('search', ''));
        $status = $request->get('status');

        $query = ContactMessage::query();

        if ($search !== '') {
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

        // Return JSON for AJAX requests
        if ($request->ajax() || $request->expectsJson() || $request->has('ajax')) {
            $paginationHtml = '';
            if ($messages->total() > 0 && $messages->hasPages()) {
                $paginationHtml = '<div class="pagination-wrapper">' .
                    view('components.pagination', [
                        'paginator' => $messages
                    ])->render() .
                    '</div>';
            }

            return response()->json([
                'success' => true,
                'html' => view('admin.contact.partials.table', compact('messages'))->render(),
                'pagination' => $paginationHtml
            ]);
        }

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

        $oldStatus = $contact->status;
        $newStatus = $validated['status'];

        $contact->update($validated);

        // Send email notification to user only when status is changed to "solved"
        if ($oldStatus !== $newStatus && $newStatus === 'solved') {
            try {
                Mail::to($contact->email)->send(new ContactStatusUpdateMail($contact, $oldStatus, $newStatus));
                
                Log::info('Contact message status update email sent (solved)', [
                    'contact_message_id' => $contact->id,
                    'email' => $contact->email,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send contact status update email', [
                    'contact_message_id' => $contact->id,
                    'email' => $contact->email,
                    'error' => $e->getMessage(),
                ]);
                // Don't fail the redirect if email fails
            }
        }

        Log::info('Contact message updated', [
            'contact_message_id' => $contact->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('admin.contacts.index')
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
