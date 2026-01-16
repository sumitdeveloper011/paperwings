<?php

namespace App\Http\Requests\Admin\Gallery;

use Illuminate\Foundation\Http\FormRequest;

class StoreGalleryItemRequest extends FormRequest
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
        $type = $this->input('type', 'image');

        $rules = [
            'type' => 'required|in:image,video',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:2000',
            'alt_text' => 'nullable|string|max:255',
            'is_featured' => 'nullable|boolean',
        ];

        if ($type === 'image') {
            $rules['image'] = 'required|image|mimes:jpeg,jpg,png,gif,webp|max:5120';
        } else {
            $rules['video_embed_code'] = 'required_without:video_url|nullable|string|max:2000';
            $rules['video_url'] = 'required_without:video_embed_code|nullable|url|max:500';
            $rules['thumbnail'] = 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:2048';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Item type is required.',
            'type.in' => 'Item type must be either image or video.',
            'image.required' => 'Image file is required for image type.',
            'image.image' => 'Uploaded file must be an image.',
            'image.mimes' => 'Image must be jpeg, jpg, png, gif, or webp format.',
            'image.max' => 'Image size cannot exceed 5MB.',
            'video_embed_code.required_without' => 'Either video embed code or video URL is required.',
            'video_url.required_without' => 'Either video URL or video embed code is required.',
            'video_url.url' => 'Video URL must be a valid URL.',
            'thumbnail.image' => 'Thumbnail must be an image file.',
            'thumbnail.max' => 'Thumbnail size cannot exceed 2MB.',
        ];
    }
}
