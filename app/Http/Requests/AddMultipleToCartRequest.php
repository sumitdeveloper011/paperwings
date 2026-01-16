<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddMultipleToCartRequest extends FormRequest
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
                Rule::exists('products', 'uuid')->where('status', 1)
            ],
            'quantities' => [
                'nullable',
                'array'
            ],
            'quantities.*' => [
                'nullable',
                'integer',
                'min:1',
                'max:99'
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'product_uuids.required' => 'Product UUIDs are required.',
            'product_uuids.array' => 'Product UUIDs must be an array.',
            'product_uuids.min' => 'At least one product UUID is required.',
            'product_uuids.max' => 'Maximum 50 products can be added at once.',
            'product_uuids.*.required' => 'Each product UUID is required.',
            'product_uuids.*.uuid' => 'Invalid product UUID format.',
            'product_uuids.*.exists' => 'One or more products are not available.',
            'quantities.array' => 'Quantities must be an array.',
            'quantities.*.integer' => 'Each quantity must be a number.',
            'quantities.*.min' => 'Each quantity must be at least 1.',
            'quantities.*.max' => 'Each quantity cannot exceed 99.',
        ];
    }
}
