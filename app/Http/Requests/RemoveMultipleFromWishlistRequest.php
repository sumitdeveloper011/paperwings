<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RemoveMultipleFromWishlistRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_uuids' => [
                'required',
                'array',
                'min:1',
                'max:50'
            ],
            'product_uuids.*' => [
                'required',
                'uuid',
                Rule::exists('products', 'uuid')
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'product_uuids.required' => 'Product UUIDs are required.',
            'product_uuids.array' => 'Product UUIDs must be an array.',
            'product_uuids.min' => 'At least one product UUID is required.',
            'product_uuids.max' => 'Maximum 50 products can be removed at once.',
            'product_uuids.*.required' => 'Each product UUID is required.',
            'product_uuids.*.uuid' => 'Invalid product UUID format.',
            'product_uuids.*.exists' => 'One or more products not found.',
        ];
    }
}
