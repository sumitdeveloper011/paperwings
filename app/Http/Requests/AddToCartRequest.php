<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddToCartRequest extends FormRequest
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
            'product_uuid' => [
                'required',
                'uuid',
                Rule::exists('products', 'uuid')->where('status', 1)
            ],
            'quantity' => [
                'nullable',
                'integer',
                'min:1',
                'max:99'
            ],
        ];
    }

    // Get custom messages for validator errors
    public function messages(): array
    {
        return [
            'product_uuid.required' => 'Product UUID is required.',
            'product_uuid.uuid' => 'Invalid product UUID format.',
            'product_uuid.exists' => 'The selected product is not available.',
            'quantity.integer' => 'Quantity must be a number.',
            'quantity.min' => 'Quantity must be at least 1.',
            'quantity.max' => 'Quantity cannot exceed 99.',
        ];
    }
}

