<?php

namespace App\Http\Requests\Admin\Coupon;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCouponRequest extends FormRequest
{
    // Determine if the user is authorized to make this request
    public function authorize(): bool
    {
        return true;
    }

    // Get the validation rules that apply to the request
    public function rules(): array
    {
        $coupon = $this->route('coupon');
        $couponUuid = $coupon ? $coupon->uuid : null;

        return [
            'code' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Z0-9]+$/',
                Rule::unique('coupons', 'code')->ignore($couponUuid, 'uuid'),
            ],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed',
            'value' => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    if ($this->input('type') === 'percentage' && $value > 100) {
                        $fail('Percentage discount cannot exceed 100%.');
                    }
                    if ($this->input('type') === 'fixed' && $value > 100000) {
                        $fail('Fixed discount amount cannot exceed $100,000.');
                    }
                },
            ],
            'minimum_amount' => 'nullable|numeric|min:0|max:1000000',
            'maximum_discount' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100000',
                function ($attribute, $value, $fail) {
                    if ($value !== null && $this->input('type') !== 'percentage') {
                        $fail('Maximum discount can only be set for percentage type coupons.');
                    }
                },
            ],
            'usage_limit' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value !== null && $value !== '') {
                        if (!is_numeric($value) || !ctype_digit((string)$value)) {
                            $fail('Usage limit must be a whole number.');
                        } elseif ((int)$value < 1) {
                            $fail('Usage limit must be at least 1.');
                        } elseif ((int)$value > 999999) {
                            $fail('Usage limit cannot exceed 999,999.');
                        }
                    }
                },
            ],
            'usage_limit_per_user' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value !== null && $value !== '') {
                        if (!is_numeric($value) || !ctype_digit((string)$value)) {
                            $fail('Usage limit per user must be a whole number.');
                        } elseif ((int)$value < 1) {
                            $fail('Usage limit per user must be at least 1.');
                        } elseif ((int)$value > 999999) {
                            $fail('Usage limit per user cannot exceed 999,999.');
                        }
                    }
                },
            ],
            'start_date' => [
                'required',
                'date',
                'date_format:Y-m-d',
            ],
            'end_date' => [
                'required',
                'date',
                'date_format:Y-m-d',
                'after:start_date',
            ],
            'status' => 'required|integer|in:0,1',
        ];
    }

    // Prepare the data for validation
    protected function prepareForValidation(): void
    {
        // Convert dd-mm-yyyy to yyyy-mm-dd if needed (JavaScript should handle this, but as backup)
        if ($this->has('start_date') && preg_match('/^\d{2}-\d{2}-\d{4}$/', $this->start_date)) {
            $parts = explode('-', $this->start_date);
            $this->merge([
                'start_date' => $parts[2] . '-' . $parts[1] . '-' . $parts[0],
            ]);
        }

        if ($this->has('end_date') && preg_match('/^\d{2}-\d{2}-\d{4}$/', $this->end_date)) {
            $parts = explode('-', $this->end_date);
            $this->merge([
                'end_date' => $parts[2] . '-' . $parts[1] . '-' . $parts[0],
            ]);
        }

        // Ensure code is uppercase
        if ($this->has('code')) {
            $this->merge([
                'code' => strtoupper(trim($this->code)),
            ]);
        }
    }

    // Get custom messages for validator errors
    public function messages(): array
    {
        return [
            'code.required' => 'Coupon code is required.',
            'code.unique' => 'This coupon code already exists.',
            'code.regex' => 'Coupon code must contain only uppercase letters and numbers (no spaces or special characters).',
            'code.max' => 'Coupon code cannot exceed 50 characters.',
            'name.required' => 'Coupon name is required.',
            'name.max' => 'Coupon name cannot exceed 255 characters.',
            'type.required' => 'Discount type is required.',
            'type.in' => 'Discount type must be either percentage or fixed.',
            'value.required' => 'Discount value is required.',
            'value.numeric' => 'Discount value must be a number.',
            'value.min' => 'Discount value must be at least 0.',
            'minimum_amount.numeric' => 'Minimum amount must be a number.',
            'minimum_amount.min' => 'Minimum amount must be at least 0.',
            'minimum_amount.max' => 'Minimum amount cannot exceed $1,000,000.',
            'maximum_discount.numeric' => 'Maximum discount must be a number.',
            'maximum_discount.min' => 'Maximum discount must be at least 0.',
            'maximum_discount.max' => 'Maximum discount cannot exceed $100,000.',
            'start_date.required' => 'Start date is required.',
            'start_date.date' => 'Start date must be a valid date.',
            'start_date.date_format' => 'Start date format is invalid. Please use dd-mm-yyyy format.',
            'end_date.required' => 'End date is required.',
            'end_date.date' => 'End date must be a valid date.',
            'end_date.date_format' => 'End date format is invalid. Please use dd-mm-yyyy format.',
            'end_date.after' => 'End date must be after start date.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be either Active (1) or Inactive (0).',
        ];
    }
}
