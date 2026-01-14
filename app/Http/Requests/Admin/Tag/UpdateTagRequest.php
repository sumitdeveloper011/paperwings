<?php

namespace App\Http\Requests\Admin\Tag;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tag = $this->route('tag');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'min:2',
                Rule::unique('tags', 'name')->ignore($tag->id)
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tag name is required.',
            'name.string' => 'Tag name must be a valid string.',
            'name.max' => 'Tag name must not exceed 255 characters.',
            'name.min' => 'Tag name must be at least 2 characters.',
            'name.unique' => 'This tag name already exists.',
        ];
    }
}
