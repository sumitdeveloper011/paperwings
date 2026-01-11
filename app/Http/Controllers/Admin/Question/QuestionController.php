<?php

namespace App\Http\Controllers\Admin\Question;

use App\Http\Controllers\Controller;
use App\Models\ProductQuestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class QuestionController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $productId = $request->get('product_id');

        $query = ProductQuestion::with(['product', 'user', 'answers']);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('question', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
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

        // Return JSON for AJAX requests
        if ($request->ajax() || $request->expectsJson() || $request->has('ajax')) {
            return response()->json([
                'success' => true,
                'html' => view('admin.question.partials.table', compact('questions'))->render(),
                'pagination' => $questions->total() > 0 && $questions->hasPages() 
                    ? view('components.pagination', ['paginator' => $questions])->render() 
                    : ''
            ]);
        }

        return view('admin.question.index', compact('questions', 'search', 'status', 'productId'));
    }

    public function show(ProductQuestion $question): View
    {
        $question->load(['product', 'user', 'answers.user']);
        return view('admin.question.show', compact('question'));
    }

    public function updateStatus(Request $request, ProductQuestion $question): RedirectResponse|JsonResponse
    {
        $validated = $request->validate(['status' => 'required|in:0,1']);
        
        // Cast status to integer
        $status = (int) $validated['status'];
        $question->update(['status' => $status]);
        
        $statusText = $status == 1 ? 'approved' : 'pending';

        // Return JSON for AJAX requests
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Question {$statusText} successfully!"
            ]);
        }

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
