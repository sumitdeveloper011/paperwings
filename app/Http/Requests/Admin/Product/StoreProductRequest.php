<?php

namespace App\Http\Requests\Admin\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'name' => 'required|string|max:255|unique:products,name',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'total_price' => 'required|numeric|min:0',
            'discount_type' => 'nullable|in:none,direct,percentage',
            'discount_price' => 'nullable|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'barcode' => 'nullable|string|max:255',
            'stock' => 'nullable|integer|min:0',
            'product_type' => 'nullable|integer|in:1,2,3',
            'description' => 'required|string',
            'short_description' => 'required|string|max:500',
            'accordion_data' => 'nullable|array',
            'accordion_data.*.heading' => 'required_with:accordion_data|string|max:255',
            'accordion_data.*.content' => 'required_with:accordion_data|string',
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'exists:tags,id',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'status' => 'required|in:1,0',
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Category is required.',
            'category_id.exists' => 'Selected category does not exist.',
            'brand_id.required' => 'Brand is required.',
            'brand_id.exists' => 'Selected brand does not exist.',
            'name.required' => 'Product name is required.',
            'name.unique' => 'A product with this name already exists.',
            'name.max' => 'Product name cannot exceed 255 characters.',
            'slug.unique' => 'A product with this slug already exists.',
            'slug.max' => 'Slug cannot exceed 255 characters.',
            'total_price.required' => 'Price is required.',
            'total_price.numeric' => 'Price must be a valid number.',
            'total_price.min' => 'Price must be at least 0.',
            'discount_price.numeric' => 'Discount price must be a valid number.',
            'discount_price.min' => 'Discount price must be at least 0.',
            'barcode.max' => 'Barcode cannot exceed 255 characters.',
            'stock.integer' => 'Stock must be a valid integer.',
            'stock.min' => 'Stock must be at least 0.',
            'product_type.integer' => 'Product type must be a valid integer.',
            'product_type.in' => 'Product type must be Featured (1), On Sale (2), or Top Rated (3).',
            'description.required' => 'Description is required.',
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
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be either Active (1) or Inactive (0).',
        ];
    }
}

