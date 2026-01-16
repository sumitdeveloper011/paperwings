<?php

namespace App\Jobs;

use App\Mail\NewsletterMail;
use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendNewsletterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $email;
    public string $subject;
    public string $body;
    public ?string $unsubscribeToken;

    public function __construct(string $email, string $subject, string $body, ?string $unsubscribeToken = null)
    {
        $this->email = $email;
        $this->subject = $subject;
        $this->body = $body;
        $this->unsubscribeToken = $unsubscribeToken;
    }

    public function handle(): void
    {
        try {
            $subscription = Subscription::where('email', $this->email)
                ->where('status', 1)
                ->first();

            if (!$subscription) {
                Log::warning('Newsletter: Subscription not found or inactive', [
                    'email' => $this->email
                ]);
                return;
            }

            Mail::to($this->email)->send(
                new NewsletterMail(
                    $this->email,
                    $this->subject,
                    $this->body,
                    $this->unsubscribeToken ?? $subscription->uuid
                )
            );

            Log::info('Newsletter sent successfully', [
                'email' => $this->email
            ]);
        } catch (\Exception $e) {
            Log::error('Newsletter sending failed', [
                'email' => $this->email,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
