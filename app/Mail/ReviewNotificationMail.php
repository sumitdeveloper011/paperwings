<?php

namespace App\Mail;

use App\Models\ProductReview;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Helpers\SettingHelper;

class ReviewNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $review;
    public $product;

    public function __construct(ProductReview $review)
    {
        $this->review = $review;
        $this->review->load('product');
        $this->product = $this->review->product;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Thank You for Your Review - ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        $settings = SettingHelper::all();

        $logoUrl = url('assets/frontend/images/logo.png');
        if (!filter_var($logoUrl, FILTER_VALIDATE_URL)) {
            $logoUrl = config('app.url') . '/assets/frontend/images/logo.png';
        }

        $socialLinks = SettingHelper::extractSocialLinks($settings);

        $contactPhone = SettingHelper::getFirstFromArraySetting($settings, 'phones') ?? '+880 123 4567';
        $contactEmail = SettingHelper::getFirstFromArraySetting($settings, 'emails') ?? 'info@paperwings.com';

        $productUrl = route('product.detail', $this->product->slug);

        return new Content(
            view: 'emails.review-notification',
            with: [
                'review' => $this->review,
                'product' => $this->product,
                'logoUrl' => $logoUrl,
                'contactPhone' => $contactPhone,
                'contactEmail' => $contactEmail,
                'socialLinks' => $socialLinks,
                'productUrl' => $productUrl,
            ],
        );
    }
}
