<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;

class StoreCheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Billing Address
            'billing_first_name' => ['required', 'string', 'max:255', 'min:2', 'regex:/^[a-zA-Z\s\-\'\.]+$/'],
            'billing_last_name' => ['required', 'string', 'max:255', 'min:2', 'regex:/^[a-zA-Z\s\-\'\.]+$/'],
            'billing_email' => ['required', 'email', 'max:255'],
            'billing_phone' => ['required', 'string', 'max:20', 'regex:/^[\d\s\-\+\(\)]+$/'],
            'billing_street_address' => ['required', 'string', 'max:255', 'min:5'],
            'billing_city' => ['required', 'string', 'max:255', 'min:2'],
            'billing_suburb' => ['nullable', 'string', 'max:255'],
            'billing_region_id' => ['required', 'exists:regions,id'],
            'billing_zip_code' => ['required', 'string', 'regex:/^\d{4}$/', 'max:4'],
            'billing_country' => ['required', 'string', 'max:255'],
            
            // Shipping Address
            'shipping_first_name' => ['required', 'string', 'max:255', 'min:2', 'regex:/^[a-zA-Z\s\-\'\.]+$/'],
            'shipping_last_name' => ['required', 'string', 'max:255', 'min:2', 'regex:/^[a-zA-Z\s\-\'\.]+$/'],
            'shipping_email' => ['required', 'email', 'max:255'],
            'shipping_phone' => ['required', 'string', 'max:20', 'regex:/^[\d\s\-\+\(\)]+$/'],
            'shipping_street_address' => ['required', 'string', 'max:255', 'min:5'],
            'shipping_city' => ['required', 'string', 'max:255', 'min:2'],
            'shipping_suburb' => ['nullable', 'string', 'max:255'],
            'shipping_region_id' => ['required', 'exists:regions,id'],
            'shipping_zip_code' => ['required', 'string', 'regex:/^\d{4}$/', 'max:4'],
            'shipping_country' => ['required', 'string', 'max:255'],
            
            // Payment
            'payment_intent_id' => ['required', 'string'],
            
            // Optional
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            // Billing Address Messages
            'billing_first_name.required' => 'Please enter your billing first name.',
            'billing_first_name.min' => 'First name must be at least 2 characters.',
            'billing_first_name.max' => 'First name cannot exceed 255 characters.',
            'billing_first_name.regex' => 'First name can only contain letters, spaces, hyphens, apostrophes, and periods.',
            
            'billing_last_name.required' => 'Please enter your billing last name.',
            'billing_last_name.min' => 'Last name must be at least 2 characters.',
            'billing_last_name.max' => 'Last name cannot exceed 255 characters.',
            'billing_last_name.regex' => 'Last name can only contain letters, spaces, hyphens, apostrophes, and periods.',
            
            'billing_email.required' => 'Please enter your billing email address.',
            'billing_email.email' => 'Please enter a valid email address.',
            'billing_email.max' => 'Email cannot exceed 255 characters.',
            
            'billing_phone.required' => 'Please enter your billing phone number.',
            'billing_phone.max' => 'Phone number cannot exceed 20 characters.',
            'billing_phone.regex' => 'Please enter a valid phone number format.',
            
            'billing_street_address.required' => 'Please enter your billing street address.',
            'billing_street_address.min' => 'Street address must be at least 5 characters.',
            'billing_street_address.max' => 'Street address cannot exceed 255 characters.',
            
            'billing_city.required' => 'Please enter your billing city.',
            'billing_city.min' => 'City must be at least 2 characters.',
            'billing_city.max' => 'City cannot exceed 255 characters.',
            
            'billing_region_id.required' => 'Please select a billing region.',
            'billing_region_id.exists' => 'The selected billing region is invalid.',
            
            'billing_zip_code.required' => 'Please enter your billing postcode.',
            'billing_zip_code.regex' => 'Please enter a valid 4-digit New Zealand postcode.',
            'billing_zip_code.max' => 'Postcode must be 4 digits.',
            
            'billing_country.required' => 'Country is required.',
            
            // Shipping Address Messages
            'shipping_first_name.required' => 'Please enter your shipping first name.',
            'shipping_first_name.min' => 'First name must be at least 2 characters.',
            'shipping_first_name.max' => 'First name cannot exceed 255 characters.',
            'shipping_first_name.regex' => 'First name can only contain letters, spaces, hyphens, apostrophes, and periods.',
            
            'shipping_last_name.required' => 'Please enter your shipping last name.',
            'shipping_last_name.min' => 'Last name must be at least 2 characters.',
            'shipping_last_name.max' => 'Last name cannot exceed 255 characters.',
            'shipping_last_name.regex' => 'Last name can only contain letters, spaces, hyphens, apostrophes, and periods.',
            
            'shipping_email.required' => 'Please enter your shipping email address.',
            'shipping_email.email' => 'Please enter a valid email address.',
            'shipping_email.max' => 'Email cannot exceed 255 characters.',
            
            'shipping_phone.required' => 'Please enter your shipping phone number.',
            'shipping_phone.max' => 'Phone number cannot exceed 20 characters.',
            'shipping_phone.regex' => 'Please enter a valid phone number format.',
            
            'shipping_street_address.required' => 'Please enter your shipping street address.',
            'shipping_street_address.min' => 'Street address must be at least 5 characters.',
            'shipping_street_address.max' => 'Street address cannot exceed 255 characters.',
            
            'shipping_city.required' => 'Please enter your shipping city.',
            'shipping_city.min' => 'City must be at least 2 characters.',
            'shipping_city.max' => 'City cannot exceed 255 characters.',
            
            'shipping_region_id.required' => 'Please select a shipping region.',
            'shipping_region_id.exists' => 'The selected shipping region is invalid.',
            
            'shipping_zip_code.required' => 'Please enter your shipping postcode.',
            'shipping_zip_code.regex' => 'Please enter a valid 4-digit New Zealand postcode.',
            'shipping_zip_code.max' => 'Postcode must be 4 digits.',
            
            'shipping_country.required' => 'Country is required.',
            
            // Payment Messages
            'payment_intent_id.required' => 'Payment intent is required. Please try again.',
            
            // Optional Messages
            'notes.max' => 'Order notes cannot exceed 1000 characters.',
        ];
    }
}
