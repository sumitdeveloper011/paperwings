<?php

namespace App\Http\Requests\Admin\SpecialOffersBanner;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSpecialOffersBannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'button_text' => 'nullable|string|max:100',
            'button_link' => 'nullable|url|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'show_countdown' => 'nullable|boolean',
            'status' => 'required|integer|in:0,1',
            'sort_order' => 'nullable|integer|min:0',
        ];
    }
}
