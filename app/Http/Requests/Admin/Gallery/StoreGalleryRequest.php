<?php

namespace App\Http\Requests\Admin\Gallery;

use Illuminate\Foundation\Http\FormRequest;

class StoreGalleryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'category' => 'required|in:general,products,events,portfolio,other',
            'status' => 'nullable|in:active,inactive',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Gallery name is required.',
            'name.max' => 'Gallery name cannot exceed 255 characters.',
            'description.max' => 'Description cannot exceed 2000 characters.',
            'category.required' => 'Category is required.',
            'category.in' => 'Category must be one of: general, products, events, portfolio, other.',
            'status.in' => 'Status must be either active or inactive.',
        ];
    }
}
