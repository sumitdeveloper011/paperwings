<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductFaq;

class ProductFaqSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::active()->take(10)->get();

        if ($products->isEmpty()) {
            $this->command->warn('No active products found. Please create products first.');
            return;
        }

        $faqs = [
            [
                'question' => 'What is the size of this product?',
                'answer' => 'The product dimensions are clearly listed in the product description. Please check the specifications section for detailed measurements.',
            ],
            [
                'question' => 'Is this product suitable for children?',
                'answer' => 'Yes, this product is safe for children to use. However, we recommend adult supervision for younger children.',
            ],
            [
                'question' => 'What materials is this product made from?',
                'answer' => 'This product is made from high-quality, eco-friendly materials. Detailed material information is available in the product description.',
            ],
            [
                'question' => 'Can I return this product if I am not satisfied?',
                'answer' => 'Yes, we offer a 30-day return policy. Please keep your receipt and ensure the product is in its original packaging.',
            ],
            [
                'question' => 'How long does shipping take?',
                'answer' => 'Standard shipping within New Zealand takes 3-5 business days. Express shipping options are also available at checkout.',
            ],
            [
                'question' => 'Is this product available in different colors?',
                'answer' => 'Color options vary by product. Please check the product page for available color variations.',
            ],
        ];

        foreach ($products as $index => $product) {
            // Add 2-3 FAQs per product
            $productFaqs = array_slice($faqs, 0, rand(2, 3));
            
            foreach ($productFaqs as $faqIndex => $faq) {
                ProductFaq::create([
                    'product_id' => $product->id,
                    'question' => $faq['question'],
                    'answer' => $faq['answer'],
                    'sort_order' => $faqIndex + 1,
                    'status' => true,
                ]);
            }
        }

        $this->command->info('Product FAQs seeded successfully!');
    }
}
