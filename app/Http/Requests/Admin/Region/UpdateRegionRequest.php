<?php

namespace App\Http\Requests\Admin\Region;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRegionRequest extends FormRequest
{
    // Determine if the user is authorized to make this request
    public function authorize(): bool
    {
        return true;
    }

    // Get the validation rules that apply to the request
    public function rules(): array
    {
        $region = $this->route('region');
        $regionId = $region ? $region->id : null;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('regions', 'name')->ignore($regionId),
            ],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('regions', 'slug')->ignore($regionId),
            ],
            'status' => 'nullable|integer|in:0,1',
        ];
    }

    // Get custom messages for validator errors
    public function messages(): array
    {
        return [
            'name.required' => 'Region name is required.',
            'name.unique' => 'This region name already exists.',
            'slug.unique' => 'This slug already exists.',
            'status.in' => 'Status must be either active or inactive.',
        ];
    }
}
