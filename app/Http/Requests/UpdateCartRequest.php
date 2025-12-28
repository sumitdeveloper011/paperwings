<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCartRequest extends FormRequest
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
            'cart_item_id' => [
                'required',
                'integer',
                Rule::exists('cart_items', 'id')
            ],
            'quantity' => [
                'required',
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
            'cart_item_id.required' => 'Cart item ID is required.',
            'cart_item_id.exists' => 'The selected cart item does not exist.',
            'quantity.required' => 'Quantity is required.',
            'quantity.integer' => 'Quantity must be a number.',
            'quantity.min' => 'Quantity must be at least 1.',
            'quantity.max' => 'Quantity cannot exceed 99.',
        ];
    }
}

