<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductQuestion;
use App\Models\ProductAnswer;
use App\Models\User;

class ProductQuestionSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::active()->take(10)->get();
        $users = User::take(5)->get();

        if ($products->isEmpty()) {
            $this->command->warn('No active products found. Please create products first.');
            return;
        }

        $questions = [
            'What are the dimensions of this product?',
            'Is this suitable for left-handed people?',
            'Can this be used by children?',
            'What colors are available?',
            'How long does shipping take to Auckland?',
            'Is this product eco-friendly?',
            'Can I get a refund if I am not satisfied?',
            'What is the warranty period?',
        ];

        $answers = [
            'Yes, this product is suitable for left-handed users.',
            'Shipping typically takes 3-5 business days within New Zealand.',
            'Yes, this product is made from eco-friendly materials.',
            'We offer a 30-day return policy for unused items.',
            'The warranty period is 12 months from purchase.',
        ];

        foreach ($products as $product) {
            // Add 1-3 questions per product
            $numQuestions = rand(1, 3);
            $selectedQuestions = array_rand($questions, min($numQuestions, count($questions)));
            
            if (!is_array($selectedQuestions)) {
                $selectedQuestions = [$selectedQuestions];
            }

            foreach ($selectedQuestions as $questionIndex) {
                $user = $users->isNotEmpty() ? $users->random() : null;
                
                $question = ProductQuestion::create([
                    'product_id' => $product->id,
                    'user_id' => $user?->id,
                    'name' => $user ? $user->name : 'John Doe',
                    'email' => $user ? $user->email : 'john.doe@example.com',
                    'question' => $questions[$questionIndex],
                    'status' => rand(0, 10) > 2 ? 1 : 0, // 80% approved
                ]);

                // Add 1-2 answers to some questions
                if (rand(0, 10) > 4) { // 60% chance
                    $numAnswers = rand(1, 2);
                    for ($i = 0; $i < $numAnswers; $i++) {
                        $answerUser = $users->isNotEmpty() ? $users->random() : null;
                        
                        ProductAnswer::create([
                            'question_id' => $question->id,
                            'user_id' => $answerUser?->id,
                            'name' => $answerUser ? $answerUser->name : 'Jane Smith',
                            'answer' => $answers[array_rand($answers)],
                            'helpful_count' => rand(0, 10),
                            'status' => 1, // Auto-approve answers
                        ]);
                    }
                }
            }
        }

        $this->command->info('Product Questions & Answers seeded successfully!');
    }
}
