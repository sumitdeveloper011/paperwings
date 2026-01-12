<?php

namespace App\Http\Requests\Admin\Bundle;

use Illuminate\Foundation\Http\FormRequest;

class StoreBundleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'short_description' => 'required|string|max:500',
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'bundle_price' => 'required|numeric|min:0',
            'discount_type' => 'nullable|in:none,direct,percentage',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_price' => 'nullable|numeric|min:0',
            'product_ids' => 'required|array|min:2',
            'product_ids.*' => 'exists:products,id',
            'quantities' => 'nullable|array',
            'quantities.*' => 'integer|min:1',
            'status' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
            'accordion_data' => 'nullable|array',
            'accordion_data.*.heading' => 'required_with:accordion_data|string|max:255',
            'accordion_data.*.content' => 'required_with:accordion_data|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Bundle name is required.',
            'name.max' => 'Bundle name cannot exceed 255 characters.',
            'description.string' => 'Description must be a valid string.',
            'short_description.required' => 'Short description is required.',
            'short_description.max' => 'Short description cannot exceed 500 characters.',
            'accordion_data.array' => 'Accordion data must be an array.',
            'accordion_data.*.heading.required_with' => 'Accordion heading is required.',
            'accordion_data.*.heading.max' => 'Accordion heading cannot exceed 255 characters.',
            'accordion_data.*.content.required_with' => 'Accordion content is required.',
            'images.array' => 'Images must be an array.',
            'images.max' => 'You can upload a maximum of 10 images.',
            'images.*.image' => 'Each uploaded file must be an image.',
            'images.*.mimes' => 'Each image must be a file of type: jpeg, png, jpg, gif.',
            'images.*.max' => 'Each image size must not exceed 2MB.',
            'bundle_price.required' => 'Bundle price is required.',
            'bundle_price.numeric' => 'Bundle price must be a valid number.',
            'bundle_price.min' => 'Bundle price must be at least 0.',
            'discount_percentage.numeric' => 'Discount percentage must be a valid number.',
            'discount_percentage.min' => 'Discount percentage must be at least 0.',
            'discount_percentage.max' => 'Discount percentage cannot exceed 100.',
            'product_ids.required' => 'Please select at least 2 products.',
            'product_ids.array' => 'Products must be selected as an array.',
            'product_ids.min' => 'Please select at least 2 products.',
            'product_ids.*.exists' => 'One or more selected products do not exist.',
            'quantities.array' => 'Quantities must be an array.',
            'quantities.*.integer' => 'Each quantity must be a valid integer.',
            'quantities.*.min' => 'Each quantity must be at least 1.',
            'status.boolean' => 'Status must be either active or inactive.',
            'sort_order.integer' => 'Sort order must be a valid integer.',
            'sort_order.min' => 'Sort order must be at least 0.',
        ];
    }
}
