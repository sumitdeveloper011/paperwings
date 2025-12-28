<?php

namespace App\Http\Requests;

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
            'product_id' => [
                'required',
                'integer',
                Rule::exists('products', 'id')->where('status', 1)
            ],
        ];
    }

    // Get custom messages for validator errors
    public function messages(): array
    {
        return [
            'product_id.required' => 'Product ID is required.',
            'product_id.exists' => 'The selected product is not available.',
        ];
    }

    // Get the error messages for the defined validation rules
    protected function failedAuthorization(): void
    {
        abort(401, 'Please login to add items to wishlist.');
    }
}

