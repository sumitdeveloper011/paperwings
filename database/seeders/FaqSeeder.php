<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * This seeder creates sample FAQs covering different categories:
     * - Shipping & Delivery
     * - Returns & Refunds
     * - Payment
     * - Products
     * - Account & Orders
     *
     * Will NOT run in production environment for safety.
     */
    public function run(): void
    {
        // Prevent running in production
        if (app()->environment('production')) {
            $this->command->warn('⚠️  FaqSeeder skipped: Cannot run in production environment!');
            return;
        }

        $this->command->info('❓ Seeding FAQs...');

        // Pre-generate UUIDs for each FAQ to use as unique identifier
        $uuids = [];
        for ($i = 0; $i < 20; $i++) {
            $uuids[] = Str::uuid()->toString();
        }

        $faqs = [
            // Shipping & Delivery Category
            [
                'uuid' => $uuids[0],
                'question' => 'How long does shipping take?',
                'answer' => 'Standard delivery takes 3-5 business days within New Zealand. Express delivery (1-2 business days) and same-day delivery options are also available for selected areas. Delivery times are calculated from the date your order is dispatched, not from the date of purchase.',
                'category' => 'Shipping',
                'status' => 1,
                'sort_order' => 1,
            ],
            [
                'uuid' => $uuids[1],
                'question' => 'Do you ship internationally?',
                'answer' => 'Currently, we only deliver within New Zealand. For international delivery inquiries, please contact our customer service team, and we\'ll be happy to discuss options.',
                'category' => 'Shipping',
                'status' => 1,
                'sort_order' => 2,
            ],
            [
                'uuid' => $uuids[2],
                'question' => 'How much does shipping cost?',
                'answer' => 'Shipping costs are calculated at checkout based on the weight, dimensions, and destination of your order. Free delivery may be available for orders over a certain amount. Check our website for current promotions and free shipping thresholds.',
                'category' => 'Shipping',
                'status' => 1,
                'sort_order' => 3,
            ],
            [
                'uuid' => $uuids[3],
                'question' => 'Can I track my order?',
                'answer' => 'Yes! Once your order is dispatched, you\'ll receive an email confirmation with your tracking number. You can track your order in real-time through our website or using the tracking link provided in your email.',
                'category' => 'Shipping',
                'status' => 1,
                'sort_order' => 4,
            ],
            [
                'uuid' => $uuids[4],
                'question' => 'What happens if my package is lost or damaged?',
                'answer' => 'If your package arrives damaged or is lost in transit, please contact us immediately (within 48 hours of delivery). We\'ll investigate and arrange a replacement or full refund. We may request photos of damaged items to process your claim.',
                'category' => 'Shipping',
                'status' => 1,
                'sort_order' => 5,
            ],

            // Returns & Refunds Category
            [
                'uuid' => $uuids[5],
                'question' => 'What is your return policy?',
                'answer' => 'You have 14 days from the date of delivery to return or exchange items purchased from Paper Wings. All returns must be in their original condition, unused, and with all original packaging and tags attached. Please contact us to initiate a return.',
                'category' => 'Returns',
                'status' => 1,
                'sort_order' => 6,
            ],
            [
                'uuid' => $uuids[6],
                'question' => 'How do I return an item?',
                'answer' => 'To return an item, please contact our customer service team with your order number and reason for return. We\'ll provide you with a Return Authorization (RA) number and return instructions. Package the item securely in its original packaging and send it to the address provided.',
                'category' => 'Returns',
                'status' => 1,
                'sort_order' => 7,
            ],
            [
                'uuid' => $uuids[7],
                'question' => 'Who pays for return shipping?',
                'answer' => 'Return shipping costs are the responsibility of the customer unless the item is defective or we made an error with your order. We recommend using a tracked shipping service to ensure your return reaches us safely.',
                'category' => 'Returns',
                'status' => 1,
                'sort_order' => 8,
            ],
            [
                'uuid' => $uuids[8],
                'question' => 'How long does it take to process a refund?',
                'answer' => 'Once we receive and inspect your returned item, we will process your refund within 5-7 business days. Refunds will be issued to the original payment method used for the purchase. Please note that it may take additional time for your bank or credit card company to process the refund.',
                'category' => 'Returns',
                'status' => 1,
                'sort_order' => 9,
            ],
            [
                'uuid' => $uuids[9],
                'question' => 'Can I exchange an item instead of returning it?',
                'answer' => 'Yes! If you need to exchange an item for a different size, color, or style, please follow the return process. Once we receive your return, we\'ll process your exchange and ship the new item to you. If the exchange item is of higher value, you\'ll need to pay the difference.',
                'category' => 'Returns',
                'status' => 1,
                'sort_order' => 10,
            ],

            // Payment Category
            [
                'uuid' => $uuids[10],
                'question' => 'What payment methods do you accept?',
                'answer' => 'We accept various secure payment methods including major credit cards (Visa, Mastercard, American Express), debit cards, and other secure payment gateways. All payments are processed securely through our encrypted payment system.',
                'category' => 'Payment',
                'status' => 1,
                'sort_order' => 11,
            ],
            [
                'uuid' => $uuids[11],
                'question' => 'Is my payment information secure?',
                'answer' => 'Yes, absolutely! We use SSL encryption and secure payment processing to protect your payment information. We do not store your full credit card details on our servers. All payment transactions are processed through secure, PCI-compliant payment gateways.',
                'category' => 'Payment',
                'status' => 1,
                'sort_order' => 12,
            ],
            [
                'uuid' => $uuids[12],
                'question' => 'When will my card be charged?',
                'answer' => 'Your payment method will be charged when you place your order. Payment must be received before we process and ship your order. If your payment fails, we\'ll notify you and you\'ll need to update your payment information.',
                'category' => 'Payment',
                'status' => 1,
                'sort_order' => 13,
            ],

            // Products Category
            [
                'uuid' => $uuids[13],
                'question' => 'Are your products authentic and high quality?',
                'answer' => 'Yes! We handpick each product in our collection to ensure quality and authenticity. We work directly with trusted suppliers and manufacturers to bring you the best products. If you\'re not satisfied with the quality of any product, please contact us.',
                'category' => 'Products',
                'status' => 1,
                'sort_order' => 14,
            ],
            [
                'uuid' => $uuids[14],
                'question' => 'Can I see more photos of a product?',
                'answer' => 'We provide multiple product images on each product page. If you need additional photos or have specific questions about a product, please contact our customer service team, and we\'ll be happy to help.',
                'category' => 'Products',
                'status' => 1,
                'sort_order' => 15,
            ],
            [
                'uuid' => $uuids[15],
                'question' => 'Do you offer gift wrapping?',
                'answer' => 'Yes! If you\'re sending a gift, we can include a gift message (free), use gift packaging (if available), and exclude pricing information from the package. Please specify these options during checkout.',
                'category' => 'Products',
                'status' => 1,
                'sort_order' => 16,
            ],

            // Account & Orders Category
            [
                'uuid' => $uuids[16],
                'question' => 'How do I create an account?',
                'answer' => 'You can create an account during checkout or by clicking the "Sign Up" or "Register" link on our website. Simply provide your name, email address, and create a password. Having an account allows you to track orders, save addresses, and view order history.',
                'category' => 'Account',
                'status' => 1,
                'sort_order' => 17,
            ],
            [
                'uuid' => $uuids[17],
                'question' => 'How do I track my order status?',
                'answer' => 'You can track your order status by logging into your account and viewing your order history. You\'ll also receive email updates at each stage of your order, including confirmation, processing, shipping, and delivery.',
                'category' => 'Account',
                'status' => 1,
                'sort_order' => 18,
            ],
            [
                'uuid' => $uuids[18],
                'question' => 'Can I cancel or modify my order?',
                'answer' => 'Orders can be cancelled or modified within 24 hours of placement, provided they haven\'t been shipped yet. Please contact our customer service team immediately if you need to cancel or modify your order. Once an order has been shipped, you\'ll need to follow our return process.',
                'category' => 'Account',
                'status' => 1,
                'sort_order' => 19,
            ],
            [
                'uuid' => $uuids[19],
                'question' => 'I forgot my password. How do I reset it?',
                'answer' => 'Click on "Forgot Password" on the login page and enter your email address. We\'ll send you a password reset link. If you don\'t receive the email, please check your spam folder or contact our customer service team for assistance.',
                'category' => 'Account',
                'status' => 1,
                'sort_order' => 20,
            ],
        ];

        $created = 0;
        $updated = 0;

        foreach ($faqs as $faqData) {
            // Ensure UUID exists, generate if missing
            if (!isset($faqData['uuid']) || empty($faqData['uuid'])) {
                $faqData['uuid'] = Str::uuid()->toString();
            }

            // Use updateOrCreate with uuid as unique identifier
            $faq = Faq::updateOrCreate(
                ['uuid' => $faqData['uuid']],
                $faqData
            );

            if ($faq->wasRecentlyCreated) {
                $created++;
            } else {
                $updated++;
            }
        }

        $this->command->info("✅ FAQs seeded successfully!");
        $this->command->info("  • Created: {$created} FAQs");
        $this->command->info("  • Updated: {$updated} FAQs");
        $this->command->info("  • Total: " . count($faqs) . " FAQs");
        $this->command->newLine();
        $this->command->info('📌 FAQ Summary by Category:');
        $categories = array_count_values(array_column($faqs, 'category'));
        foreach ($categories as $category => $count) {
            $this->command->line("   • {$category}: {$count} FAQs");
        }
        $this->command->info("   • Active FAQs: " . count(array_filter($faqs, fn($f) => $f['status'] === 1)));
        $this->command->info("   • Inactive FAQs: " . count(array_filter($faqs, fn($f) => $f['status'] === 0)));
    }
}
