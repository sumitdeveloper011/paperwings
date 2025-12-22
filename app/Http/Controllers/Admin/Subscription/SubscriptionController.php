<?php

namespace App\Http\Controllers\Admin\Subscription;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of subscriptions
     */
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $page = $request->get('page', 1);

        $query = Subscription::query();

        if ($search) {
            $query->where('email', 'like', "{$search}%");
        }

        $subscriptions = $query->orderBy('created_at', 'desc')->paginate(15, ['*'], 'page', $page);

        if ($request->ajax()) {
            return response()->json([
                'table' => view('admin.subscription.partials.table', compact('subscriptions', 'search'))->render(),
                'pagination' => view('admin.subscription.partials.pagination', compact('subscriptions', 'search'))->render(),
                'total' => $subscriptions->total()
            ]);
        }

        return view('admin.subscription.index', compact('subscriptions', 'search'));
    }

    /**
     * Display the specified subscription
     */
    public function show(Subscription $subscription): View
    {
        return view('admin.subscription.show', compact('subscription'));
    }

    /**
     * Update subscription status
     */
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

    /**
     * Delete subscription
     */
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

    /**
     * Export subscriptions to CSV
     */
    public function export(Request $request): Response
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
}
