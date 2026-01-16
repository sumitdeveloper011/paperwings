<?php

namespace App\Http\Controllers\Admin\Subscription;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Subscription\SendNewsletterRequest;
use App\Jobs\SendNewsletterJob;
use App\Models\Subscription;
use App\Models\EmailTemplate;
use App\Services\EmailTemplateService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    // Display a listing of subscriptions
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $page = $request->get('page', 1);

        $query = Subscription::query();

        if ($search) {
            $query->where('email', 'like', "{$search}%");
        }

        $subscriptions = $query->orderBy('created_at', 'desc')->paginate(15, ['*'], 'page', $page);

        if ($request->ajax() || $request->expectsJson() || $request->has('ajax')) {
            return response()->json([
                'html' => view('admin.subscription.partials.table', compact('subscriptions', 'search'))->render(),
                'pagination' => view('admin.subscription.partials.pagination', compact('subscriptions', 'search'))->render(),
                'total' => $subscriptions->total()
            ]);
        }

        return view('admin.subscription.index', compact('subscriptions', 'search'));
    }

    // Display the specified subscription
    public function show(Subscription $subscription): View
    {
        return view('admin.subscription.show', compact('subscription'));
    }

    // Update subscription status
    public function updateStatus(Request $request, Subscription $subscription): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:0,1'
        ]);

        $oldStatus = $subscription->status;
        $subscription->update([
            'status' => $request->status,
            'unsubscribed_at' => $request->status == 0 ? now() : null,
        ]);

        Log::info('Subscription status updated', [
            'subscription_id' => $subscription->id,
            'email' => $subscription->email,
            'old_status' => $oldStatus,
            'new_status' => $request->status,
            'updated_by' => auth()->id()
        ]);

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Subscription status updated successfully.');
    }

    // Delete subscription
    public function destroy(Subscription $subscription): RedirectResponse
    {
        $email = $subscription->email;
        $subscription->delete();

        Log::info('Subscription deleted', [
            'email' => $email,
            'deleted_by' => auth()->id()
        ]);

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Subscription deleted successfully.');
    }

    // Export subscriptions to CSV
    public function export(Request $request): BinaryFileResponse|RedirectResponse
    {
        try {
            $subscriptions = Subscription::active()->orderBy('created_at', 'desc')->get();

            $filename = 'subscriptions_' . date('Y-m-d_His') . '.csv';
            $filepath = storage_path('app/public/' . $filename);

            $file = fopen($filepath, 'w');
            
            // Add CSV headers
            fputcsv($file, ['Email', 'Status', 'Subscribed At', 'Created At']);

            // Add data
            foreach ($subscriptions as $subscription) {
                fputcsv($file, [
                    $subscription->email,
                    $subscription->status == 1 ? 'Active' : 'Inactive',
                    $subscription->subscribed_at ? $subscription->subscribed_at->format('Y-m-d H:i:s') : '',
                    $subscription->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);

            Log::info('Subscriptions exported', [
                'count' => $subscriptions->count(),
                'exported_by' => auth()->id()
            ]);

            return response()->download($filepath, $filename)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            Log::error('Subscription export error', [
                'error' => $e->getMessage(),
                'exported_by' => auth()->id()
            ]);

            return redirect()->route('admin.subscriptions.index')
                ->with('error', 'Failed to export subscriptions: ' . $e->getMessage());
        }
    }

    public function createNewsletter(): View
    {
        $templates = EmailTemplate::where('category', 'newsletter')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $activeCount = Subscription::active()->count();
        $totalCount = Subscription::count();

        return view('admin.subscription.create-newsletter', compact('templates', 'activeCount', 'totalCount'));
    }

    public function sendNewsletter(SendNewsletterRequest $request): RedirectResponse
    {
        try {
            $subject = $request->subject;
            $body = $request->body;

            if ($request->filled('email_template_id')) {
                $template = EmailTemplate::find($request->email_template_id);
                if ($template) {
                    $subject = $template->subject;
                    $body = $template->body;
                }
            }

            if ($request->filled('test_email')) {
                SendNewsletterJob::dispatch(
                    $request->test_email,
                    $subject,
                    $body,
                    null
                );

                Log::info('Newsletter test email sent', [
                    'test_email' => $request->test_email,
                    'sent_by' => auth()->id()
                ]);

                return redirect()->route('admin.subscriptions.create-newsletter')
                    ->with('success', 'Test email sent successfully!');
            }

            $query = Subscription::query();
            if ($request->send_to === 'active') {
                $query->where('status', 1);
            }

            $subscriptions = $query->get();
            $count = $subscriptions->count();

            if ($count === 0) {
                return redirect()->route('admin.subscriptions.create-newsletter')
                    ->with('error', 'No subscribers found for the selected criteria.');
            }

            foreach ($subscriptions as $subscription) {
                SendNewsletterJob::dispatch(
                    $subscription->email,
                    $subject,
                    $body,
                    $subscription->uuid
                );
            }

            Log::info('Newsletter queued for sending', [
                'count' => $count,
                'send_to' => $request->send_to,
                'sent_by' => auth()->id()
            ]);

            return redirect()->route('admin.subscriptions.index')
                ->with('success', "Newsletter queued successfully! {$count} emails will be sent.");

        } catch (\Exception $e) {
            Log::error('Newsletter sending error', [
                'error' => $e->getMessage(),
                'sent_by' => auth()->id()
            ]);

            return redirect()->route('admin.subscriptions.create-newsletter')
                ->with('error', 'Failed to send newsletter: ' . $e->getMessage());
        }
    }

    public function previewTemplate(Request $request): JsonResponse
    {
        $request->validate([
            'template_id' => 'required|exists:email_templates,id'
        ]);

        $template = EmailTemplate::find($request->template_id);

        if (!$template) {
            return response()->json([
                'success' => false,
                'message' => 'Template not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'subject' => $template->subject,
            'body' => $template->body,
        ]);
    }
}
