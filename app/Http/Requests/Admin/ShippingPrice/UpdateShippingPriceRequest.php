<?php

namespace App\Http\Requests\Admin\ShippingPrice;

use App\Models\ShippingPrice;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateShippingPriceRequest extends FormRequest
{
    // Determine if the user is authorized to make this request
    public function authorize(): bool
    {
        return true;
    }

    // Get the validation rules that apply to the request
    public function rules(): array
    {
        $shippingPrice = $this->route('shipping_price');
        
        // Handle both model instance and ID
        if ($shippingPrice instanceof ShippingPrice) {
            $shippingPriceId = $shippingPrice->id;
        } else {
            $shippingPriceId = $shippingPrice;
        }

        return [
            'region_id' => [
                'required',
                'exists:regions,id',
                Rule::unique('shipping_prices', 'region_id')->ignore($shippingPriceId),
            ],
            'shipping_price' => 'required|numeric|min:0|max:999999.99',
            'free_shipping_minimum' => 'nullable|numeric|min:0|max:999999.99',
            'status' => 'nullable|integer|in:0,1',
        ];
    }

    // Get custom messages for validator errors
    public function messages(): array
    {
        return [
            'region_id.required' => 'Region is required.',
            'region_id.exists' => 'Selected region does not exist.',
            'region_id.unique' => 'Shipping price already exists for this region.',
            'shipping_price.required' => 'Shipping price is required.',
            'shipping_price.numeric' => 'Shipping price must be a number.',
            'shipping_price.min' => 'Shipping price must be at least 0.',
            'shipping_price.max' => 'Shipping price cannot exceed 999999.99.',
            'free_shipping_minimum.numeric' => 'Free shipping minimum must be a number.',
            'free_shipping_minimum.min' => 'Free shipping minimum must be at least 0.',
            'free_shipping_minimum.max' => 'Free shipping minimum cannot exceed 999999.99.',
            'status.in' => 'Status must be either active or inactive.',
        ];
    }
}
