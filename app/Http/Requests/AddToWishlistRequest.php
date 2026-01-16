<?php

namespace App\Http\Requests;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddToWishlistRequest extends FormRequest
{
    // Determine if the user is authorized to make this request
    public function authorize(): bool
    {
        return auth()->check();
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
        ];
    }

    // Get custom messages for validator errors
    public function messages(): array
    {
        return [
            'product_uuid.required' => 'Product UUID is required.',
            'product_uuid.uuid' => 'Invalid product UUID format.',
            'product_uuid.exists' => 'The selected product is not available.',
        ];
    }

    // Get the error messages for the defined validation rules
    protected function failedAuthorization(): void
    {
        throw new AuthenticationException('Please login to add items to wishlist.');
    }
}

