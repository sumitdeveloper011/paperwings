<?php

namespace App\Http\Requests\Admin\EmailTemplate;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmailTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:email_templates,name',
            'slug' => 'nullable|string|max:255|unique:email_templates,slug',
            'subject' => 'required|string|max:500',
            'body' => 'required|string',
            'description' => 'nullable|string|max:1000',
            'category' => 'required|in:order,user,newsletter,system',
            'is_active' => 'nullable|boolean',
            'variables' => 'nullable|array',
            'variables.*.variable_name' => 'required_with:variables|string|max:255',
            'variables.*.variable_description' => 'nullable|string|max:500',
            'variables.*.example_value' => 'nullable|string|max:255',
            'variables.*.is_required' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Template name is required.',
            'name.unique' => 'A template with this name already exists.',
            'name.max' => 'Template name cannot exceed 255 characters.',
            'slug.unique' => 'A template with this slug already exists.',
            'slug.max' => 'Slug cannot exceed 255 characters.',
            'subject.required' => 'Email subject is required.',
            'subject.max' => 'Email subject cannot exceed 500 characters.',
            'body.required' => 'Email body is required.',
            'description.max' => 'Description cannot exceed 1000 characters.',
            'category.required' => 'Category is required.',
            'category.in' => 'Category must be one of: order, user, newsletter, system.',
        ];
    }
}
