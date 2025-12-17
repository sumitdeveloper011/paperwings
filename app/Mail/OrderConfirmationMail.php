<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order)
    {
        // Load relationships for email
        $order->load(['items.product', 'billingRegion', 'shippingRegion']);
        $this->order = $order;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Order Confirmation - Order #' . $this->order->order_number,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.order-confirmation',
            with: [
                'order' => $this->order,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        // Generate PDF invoice
        $pdf = Pdf::loadView('emails.order-invoice-pdf', ['order' => $this->order]);
        $pdfPath = storage_path('app/temp/invoice_' . $this->order->order_number . '_' . time() . '.pdf');

        // Ensure directory exists
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $pdf->save($pdfPath);

        // Clean up PDF after email is sent (using __destruct or after sending)
        register_shutdown_function(function() use ($pdfPath) {
            if (file_exists($pdfPath)) {
                @unlink($pdfPath);
            }
        });

        return [
            Attachment::fromPath($pdfPath)
                ->as('Invoice_' . $this->order->order_number . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
