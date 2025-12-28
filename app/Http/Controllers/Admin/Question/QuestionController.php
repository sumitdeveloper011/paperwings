<?php

namespace App\Http\Controllers\Admin\Question;

use App\Http\Controllers\Controller;
use App\Models\ProductQuestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuestionController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $productId = $request->get('product_id');

        $query = ProductQuestion::with(['product', 'user', 'answers']);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('question', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhereHas('product', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($status !== null) {
            $query->where('status', $status);
        }

        if ($productId) {
            $query->where('product_id', $productId);
        }

        $questions = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.question.index', compact('questions', 'search', 'status', 'productId'));
    }

    public function show(ProductQuestion $question): View
    {
        $question->load(['product', 'user', 'answers.user']);
        return view('admin.question.show', compact('question'));
    }

    public function updateStatus(Request $request, ProductQuestion $question): RedirectResponse
    {
        $request->validate(['status' => 'required|in:0,1']);
        $question->update(['status' => $request->status]);
        
        $statusText = $request->status == 1 ? 'approved' : 'pending';
        return redirect()->route('admin.questions.index')
            ->with('success', "Question {$statusText} successfully.");
    }

    public function destroy(ProductQuestion $question): RedirectResponse
    {
        $question->delete();
        return redirect()->route('admin.questions.index')
            ->with('success', 'Question deleted successfully.');
    }
}
