<?php

namespace App\Http\Controllers\Admin\Answer;

use App\Http\Controllers\Controller;
use App\Models\ProductAnswer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnswerController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $questionId = $request->get('question_id');

        $query = ProductAnswer::with(['question.product', 'user']);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('answer', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        if ($status !== null) {
            $query->where('status', $status);
        }

        if ($questionId) {
            $query->where('question_id', $questionId);
        }

        $answers = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.answer.index', compact('answers', 'search', 'status', 'questionId'));
    }

    public function show(ProductAnswer $answer): View
    {
        $answer->load(['question.product', 'user']);
        return view('admin.answer.show', compact('answer'));
    }

    public function updateStatus(Request $request, ProductAnswer $answer): RedirectResponse
    {
        $request->validate(['status' => 'required|in:0,1']);
        $answer->update(['status' => $request->status]);
        
        $statusText = $request->status == 1 ? 'approved' : 'rejected';
        return redirect()->route('admin.answers.index')
            ->with('success', "Answer {$statusText} successfully.");
    }

    public function destroy(ProductAnswer $answer): RedirectResponse
    {
        $answer->delete();
        return redirect()->route('admin.answers.index')
            ->with('success', 'Answer deleted successfully.');
    }
}
