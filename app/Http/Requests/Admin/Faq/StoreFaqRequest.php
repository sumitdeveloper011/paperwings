<?php

namespace App\Http\Requests\Admin\Faq;

use Illuminate\Foundation\Http\FormRequest;

class StoreFaqRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'question' => 'required|string|max:500|min:5',
            'answer' => 'required|string|min:10',
            'category' => 'nullable|string|max:100',
            'status' => 'required|integer|in:0,1',
            'sort_order' => 'nullable|integer|min:0|max:9999',
        ];
    }

    public function messages(): array
    {
        return [
            'question.required' => 'Question is required.',
            'question.string' => 'Question must be a valid string.',
            'question.max' => 'Question must not exceed 500 characters.',
            'question.min' => 'Question must be at least 5 characters.',
            'answer.required' => 'Answer is required.',
            'answer.string' => 'Answer must be a valid string.',
            'answer.min' => 'Answer must be at least 10 characters.',
            'category.max' => 'Category must not exceed 100 characters.',
            'status.required' => 'Status is required.',
            'status.integer' => 'Status must be an integer.',
            'status.in' => 'Status must be either active or inactive.',
            'sort_order.integer' => 'Sort order must be a number.',
            'sort_order.min' => 'Sort order must be at least 0.',
            'sort_order.max' => 'Sort order must not exceed 9999.',
        ];
    }
}
