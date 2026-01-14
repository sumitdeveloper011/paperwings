<?php

namespace App\Http\Requests\Admin\Slider;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSliderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048|dimensions:width=1920,height=600',
            'heading' => 'required|string|max:255',
            'sub_heading' => 'nullable|string|max:255',
            'button_1_name' => 'nullable|string|max:100',
            'button_1_url' => 'nullable|string|max:500',
            'button_2_name' => 'nullable|string|max:100',
            'button_2_url' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer|min:1',
            'status' => 'required|in:1,0',
        ];
    }

    public function messages(): array
    {
        return [
            'image.image' => 'The uploaded file must be an image.',
            'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif.',
            'image.max' => 'The image size must not exceed 2MB.',
            'image.dimensions' => 'The image must be exactly 1920x600 pixels.',
            'heading.required' => 'Heading is required.',
            'heading.max' => 'Heading cannot exceed 255 characters.',
            'sub_heading.max' => 'Sub heading cannot exceed 255 characters.',
            'button_1_name.max' => 'Button 1 name cannot exceed 100 characters.',
            'button_1_url.max' => 'Button 1 URL cannot exceed 500 characters.',
            'button_2_name.max' => 'Button 2 name cannot exceed 100 characters.',
            'button_2_url.max' => 'Button 2 URL cannot exceed 500 characters.',
            'sort_order.integer' => 'Sort order must be a number.',
            'sort_order.min' => 'Sort order must be at least 1.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be either Active (1) or Inactive (0).',
        ];
    }
}
