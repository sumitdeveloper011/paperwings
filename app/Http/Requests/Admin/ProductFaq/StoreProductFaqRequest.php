<?php

namespace App\Http\Requests\Admin\ProductFaq;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductFaqRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'category_id' => 'nullable|exists:categories,id',
            'faqs' => 'required|array|min:1',
            'faqs.*.question' => 'required|string|max:500',
            'faqs.*.answer' => 'required|string|max:5000',
            'faqs.*.sort_order' => 'nullable|integer|min:0|max:9999',
            'faqs.*.status' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Please select a product.',
            'product_id.exists' => 'The selected product does not exist.',
            'category_id.exists' => 'The selected category does not exist.',
            'faqs.required' => 'At least one FAQ is required.',
            'faqs.min' => 'At least one FAQ is required.',
            'faqs.*.question.required' => 'FAQ question is required.',
            'faqs.*.question.max' => 'FAQ question must not exceed 500 characters.',
            'faqs.*.answer.required' => 'FAQ answer is required.',
            'faqs.*.answer.max' => 'FAQ answer must not exceed 5000 characters.',
            'faqs.*.sort_order.integer' => 'Sort order must be a number.',
            'faqs.*.sort_order.min' => 'Sort order must be at least 0.',
            'faqs.*.sort_order.max' => 'Sort order must not exceed 9999.',
        ];
    }
}
