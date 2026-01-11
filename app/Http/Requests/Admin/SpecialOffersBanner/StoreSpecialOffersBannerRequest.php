<?php

namespace App\Http\Requests\Admin\SpecialOffersBanner;

use Illuminate\Foundation\Http\FormRequest;

class StoreSpecialOffersBannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255|min:2',
            'description' => 'nullable|string|max:5000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'button_text' => 'nullable|string|max:100',
            'button_link' => 'nullable|string|max:500',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'show_countdown' => 'nullable|boolean',
            'status' => 'required|integer|in:0,1',
            'sort_order' => 'nullable|integer|min:0|max:9999',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Title is required.',
            'title.string' => 'Title must be a valid string.',
            'title.max' => 'Title must not exceed 255 characters.',
            'title.min' => 'Title must be at least 2 characters.',
            'description.max' => 'Description must not exceed 5000 characters.',
            'image.image' => 'The file must be an image.',
            'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif.',
            'image.max' => 'The image must not be larger than 2MB.',
            'button_text.max' => 'Button text must not exceed 100 characters.',
            'button_link.max' => 'Button link must not exceed 500 characters.',
            'start_date.date' => 'Start date must be a valid date.',
            'end_date.date' => 'End date must be a valid date.',
            'end_date.after_or_equal' => 'End date must be after or equal to start date.',
            'status.required' => 'Status is required.',
            'status.integer' => 'Status must be an integer.',
            'status.in' => 'Status must be either active or inactive.',
            'sort_order.integer' => 'Sort order must be a number.',
            'sort_order.min' => 'Sort order must be at least 0.',
            'sort_order.max' => 'Sort order must not exceed 9999.',
        ];
    }
}
