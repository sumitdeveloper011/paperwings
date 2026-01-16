<?php

namespace App\Http\Requests\Admin\Gallery;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGalleryItemRequest extends FormRequest
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
        $rules = [
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:2000',
            'alt_text' => 'nullable|string|max:255',
            'order' => 'nullable|integer|min:0',
            'is_featured' => 'nullable|boolean',
        ];

        $item = $this->route('galleryItem');
        
        if ($item && $item->type === 'video') {
            $rules['video_embed_code'] = 'nullable|string|max:2000';
            $rules['video_url'] = 'nullable|url|max:500';
            $rules['thumbnail'] = 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:2048';
        } elseif ($item && $item->type === 'image') {
            $rules['image'] = 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:5120';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'image.image' => 'Uploaded file must be an image.',
            'image.mimes' => 'Image must be jpeg, jpg, png, gif, or webp format.',
            'image.max' => 'Image size cannot exceed 5MB.',
            'video_url.url' => 'Video URL must be a valid URL.',
            'thumbnail.image' => 'Thumbnail must be an image file.',
            'thumbnail.max' => 'Thumbnail size cannot exceed 2MB.',
            'order.integer' => 'Order must be a number.',
            'order.min' => 'Order cannot be negative.',
        ];
    }
}
