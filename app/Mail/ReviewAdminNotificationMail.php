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
use App\Services\EmailTemplateService;

class ReviewAdminNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $review;
    public $product;

    public function __construct(ProductReview $review)
    {
        $this->review = $review;
        $this->review->load('product', 'user');
        $this->product = $this->review->product;
    }

    public function envelope(): Envelope
    {
        $emailTemplateService = app(EmailTemplateService::class);
        $template = $emailTemplateService->getTemplate('review_admin_notification');

        if (!$template) {
            throw new \Exception('Review admin notification template not found in database');
        }

        $variables = [
            'app_name' => config('app.name'),
        ];
        $subject = $emailTemplateService->getSubject('review_admin_notification', $variables);

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        $emailTemplateService = app(EmailTemplateService::class);
        $template = $emailTemplateService->getTemplate('review_admin_notification');

        if (!$template) {
            throw new \Exception('Review admin notification template not found in database');
        }

        $settings = SettingHelper::all();
        $contactPhone = SettingHelper::getFirstFromArraySetting($settings, 'phones') ?? '+880 123 4567';
        $contactEmail = SettingHelper::getFirstFromArraySetting($settings, 'emails') ?? 'info@paperwings.co.nz';
        $socialLinks = SettingHelper::extractSocialLinks($settings);

        // Get logo URL - prefer thumbnail for emails
        $logoUrl = url('assets/frontend/images/logo.png');
        $logo = SettingHelper::get('logo');
        if ($logo && !empty($logo)) {
            if (strpos($logo, '/original/') !== false) {
                $thumbnailPath = str_replace('/original/', '/thumbnails/', $logo);
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($thumbnailPath)) {
                    $logoUrl = asset('storage/' . $thumbnailPath);
                } else {
                    $mediumPath = str_replace('/original/', '/medium/', $logo);
                    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($mediumPath)) {
                        $logoUrl = asset('storage/' . $mediumPath);
                    } else {
                        $logoUrl = asset('storage/' . $logo);
                    }
                }
            } else {
                $pathParts = explode('/', $logo);
                $fileName = array_pop($pathParts);
                $basePath = implode('/', $pathParts);
                $thumbnailPath = $basePath . '/thumbnails/' . $fileName;
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($thumbnailPath)) {
                    $logoUrl = asset('storage/' . $thumbnailPath);
                } else {
                    $logoUrl = asset('storage/' . $logo);
                }
            }
        }
        if (!filter_var($logoUrl, FILTER_VALIDATE_URL)) {
            $logoUrl = config('app.url') . '/assets/frontend/images/logo.png';
        }

        $reviewerName = $this->review->reviewer_name;
        $reviewerEmail = $this->review->user ? $this->review->user->email : ($this->review->email ?? '');
        $rating = $this->review->rating ?? 0;
        $ratingStars = str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);
        $reviewText = $this->review->review ?? '';
        $productName = $this->product->name ?? 'Product';
        $submissionDate = $this->review->created_at->format('F j, Y \a\t g:i A');
        $adminReviewViewUrl = route('admin.reviews.index');

        $variables = [
            'product_name' => $productName,
            'reviewer_name' => $reviewerName,
            'reviewer_email' => $reviewerEmail,
            'rating' => $rating,
            'rating_stars' => $ratingStars,
            'review_text' => $reviewText,
            'submission_date' => $submissionDate,
            'admin_review_view_url' => $adminReviewViewUrl,
            'app_name' => config('app.name'),
        ];

        $body = $emailTemplateService->getBody('review_admin_notification', $variables);

        return new Content(
            view: 'emails.template-body',
            with: [
                'body' => $body,
                'logoUrl' => $logoUrl,
                'headerSubtitle' => 'NEW REVIEW',
                'headerTitle' => 'New Product Review Submitted',
                'contactPhone' => $contactPhone,
                'contactEmail' => $contactEmail,
                'socialLinks' => $socialLinks,
                'currentYear' => date('Y'),
                'appName' => config('app.name'),
            ],
        );
    }
}
