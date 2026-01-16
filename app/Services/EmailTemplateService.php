<?php

namespace App\Services;

use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Log;

class EmailTemplateService
{
    /**
     * Get template by slug
     *
     * @param string $slug
     * @return EmailTemplate|null
     */
    public function getTemplate(string $slug): ?EmailTemplate
    {
        return EmailTemplate::where('slug', $slug)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Render template subject with variables
     *
     * @param string $slug
     * @param array $variables
     * @return string|null
     */
    public function getSubject(string $slug, array $variables = []): ?string
    {
        $template = $this->getTemplate($slug);
        
        if (!$template) {
            Log::warning("Email template not found: {$slug}");
            return null;
        }

        return $this->replaceVariables($template->subject, $variables);
    }

    /**
     * Render template body with variables
     *
     * @param string $slug
     * @param array $variables
     * @return string|null
     */
    public function getBody(string $slug, array $variables = []): ?string
    {
        $template = $this->getTemplate($slug);
        
        if (!$template) {
            Log::warning("Email template not found: {$slug}");
            return null;
        }

        return $this->replaceVariables($template->body, $variables);
    }

    /**
     * Render both subject and body
     *
     * @param string $slug
     * @param array $variables
     * @return array|null
     */
    public function render(string $slug, array $variables = []): ?array
    {
        $template = $this->getTemplate($slug);
        
        if (!$template) {
            Log::warning("Email template not found: {$slug}");
            return null;
        }

        return [
            'subject' => $this->replaceVariables($template->subject, $variables),
            'body' => $this->replaceVariables($template->body, $variables),
        ];
    }

    /**
     * Replace variables in text
     *
     * @param string $text
     * @param array $variables
     * @return string
     */
    protected function replaceVariables(string $text, array $variables): string
    {
        foreach ($variables as $key => $value) {
            $text = str_replace('{' . $key . '}', $value ?? '', $text);
        }

        return $text;
    }

    /**
     * Check if template exists and is active
     *
     * @param string $slug
     * @return bool
     */
    public function templateExists(string $slug): bool
    {
        return EmailTemplate::where('slug', $slug)
            ->where('is_active', true)
            ->exists();
    }
}
