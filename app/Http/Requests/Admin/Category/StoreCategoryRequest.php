<?php

namespace App\Http\Requests\Admin\Category;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    // Determine if the user is authorized to make this request
    public function authorize(): bool
    {
        return true;
    }

    // Get the validation rules that apply to the request
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:categories,name',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'description' => 'nullable|string',
            'status' => 'required|in:1,0',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048|dimensions:ratio=1/1',
        ];
    }

    // Get custom messages for validator errors
    public function messages(): array
    {
        return [
            'name.required' => 'Category name is required.',
            'name.unique' => 'A category with this name already exists.',
            'name.max' => 'Category name cannot exceed 255 characters.',
            'slug.unique' => 'A category with this slug already exists.',
            'slug.max' => 'Slug cannot exceed 255 characters.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be either Active (1) or Inactive (0).',
            'image.required' => 'Category image is required.',
            'image.image' => 'The uploaded file must be an image.',
            'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif.',
            'image.max' => 'The image size must not exceed 2MB.',
            'image.dimensions' => 'The image must have a 1:1 aspect ratio (square format).',
        ];
    }
}

