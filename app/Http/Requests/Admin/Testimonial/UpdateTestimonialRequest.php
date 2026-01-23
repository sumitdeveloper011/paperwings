<?php

namespace App\Http\Requests\Admin\Testimonial;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTestimonialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email:dns|max:255',
            'review' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048|dimensions:ratio=1/1',
            'remove_image' => 'nullable|boolean',
            'designation' => 'nullable|string|max:255',
            'status' => 'required|integer|in:0,1',
            'sort_order' => 'nullable|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif, webp.',
            'image.dimensions' => 'The image must have a 1:1 aspect ratio (square, e.g., 200x200 pixels).',
        ];
    }
}
