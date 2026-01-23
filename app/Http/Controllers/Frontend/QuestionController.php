<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductQuestion;
use App\Models\ProductAnswer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuestionController extends Controller
{
    // Store a new product question
    public function store(Request $request, $productSlug): JsonResponse
    {
        $request->validate([
            'question' => 'required|string|min:10|max:500',
            'name' => auth()->check() ? 'nullable' : 'required|string|max:255',
            'email' => auth()->check() ? 'nullable' : 'required|email:dns|max:255',
        ]);

        try {
            $product = Product::where('slug', $productSlug)
                ->active()
                ->firstOrFail();

            $question = ProductQuestion::create([
                'product_id' => $product->id,
                'user_id' => Auth::id(),
                'name' => Auth::check() ? Auth::user()->name : $request->name,
                'email' => Auth::check() ? Auth::user()->email : $request->email,
                'question' => $request->question,
                'status' => 0,
            ]);

            return $this->jsonSuccess('Question submitted successfully. It will be published after admin approval.', [
                'question' => $question
            ]);

        } catch (\Exception $e) {
            return $this->jsonError('Failed to submit question. Please try again.', 'QUESTION_SUBMIT_ERROR', null, 500);
        }
    }

    // Store a new answer to a question
    public function storeAnswer(Request $request, $questionId): JsonResponse
    {
        $request->validate([
            'answer' => 'required|string|min:10|max:1000',
            'name' => auth()->check() ? 'nullable' : 'required|string|max:255',
        ]);

        try {
            $question = ProductQuestion::findOrFail($questionId);

            $answer = ProductAnswer::create([
                'question_id' => $question->id,
                'user_id' => Auth::id(),
                'name' => Auth::check() ? Auth::user()->name : $request->name,
                'answer' => $request->answer,
                'status' => 1,
            ]);

            return $this->jsonSuccess('Answer submitted successfully.', [
                'answer' => $answer
            ]);

        } catch (\Exception $e) {
            return $this->jsonError('Failed to submit answer. Please try again.', 'ANSWER_SUBMIT_ERROR', null, 500);
        }
    }

    // Mark answer as helpful
    public function helpful(Request $request, $answerId): JsonResponse
    {
        try {
            $answer = ProductAnswer::findOrFail($answerId);
            $answer->increment('helpful_count');

            return $this->jsonSuccess('Helpful count updated.', [
                'helpful_count' => $answer->helpful_count
            ]);

        } catch (\Exception $e) {
            return $this->jsonError('Failed to update helpful count.', 'ANSWER_HELPFUL_ERROR', null, 500);
        }
    }
}
