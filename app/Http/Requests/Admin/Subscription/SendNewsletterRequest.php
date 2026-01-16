<?php

namespace App\Http\Requests\Admin\Subscription;

use Illuminate\Foundation\Http\FormRequest;

class SendNewsletterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'email_template_id' => 'nullable|exists:email_templates,id',
            'send_to' => 'required|in:all,active',
            'test_email' => 'nullable|email',
        ];
    }

    public function messages(): array
    {
        return [
            'subject.required' => 'Subject is required.',
            'subject.max' => 'Subject must not exceed 255 characters.',
            'body.required' => 'Email body is required.',
            'email_template_id.exists' => 'Selected email template does not exist.',
            'send_to.required' => 'Please select recipients.',
            'send_to.in' => 'Invalid recipient selection.',
            'test_email.email' => 'Test email must be a valid email address.',
        ];
    }
}
