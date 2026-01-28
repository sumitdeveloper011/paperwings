<?php

namespace App\Http\Controllers\Admin\EmailTemplate;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EmailTemplate\StoreEmailTemplateRequest;
use App\Http\Requests\Admin\EmailTemplate\UpdateEmailTemplateRequest;
use App\Models\EmailTemplate;
use App\Repositories\Interfaces\EmailTemplateRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EmailTemplateController extends Controller
{
    protected EmailTemplateRepositoryInterface $emailTemplateRepository;

    public function __construct(EmailTemplateRepositoryInterface $emailTemplateRepository)
    {
        $this->emailTemplateRepository = $emailTemplateRepository;
    }

    public function index(Request $request): ViewContract|JsonResponse
    {
        $search = $request->get('search', '');
        $category = $request->get('category', '');

        $query = EmailTemplate::query();

        if ($search !== '') {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('slug', 'LIKE', "%{$search}%")
                  ->orWhere('subject', 'LIKE', "%{$search}%");
            });
        }

        if ($category !== '') {
            $query->where('category', $category);
        }

        $templates = $query->with(['creator', 'updater'])
            ->orderBy('category')
            ->orderBy('name')
            ->paginate(15)
            ->withPath($request->url())
            ->appends($request->except('page'));

        if ($request->ajax() || $request->expectsJson() || $request->has('ajax')) {
            $paginationHtml = '';
            if ($templates instanceof LengthAwarePaginator) {
                $paginationHtml = '<div class="pagination-wrapper">' .
                    view('components.pagination', [
                        'paginator' => $templates
                    ])->render() .
                    '</div>';
            }

            return response()->json([
                'success' => true,
                'html' => view('admin.email-template.partials.table', compact('templates'))->render(),
                'pagination' => $paginationHtml,
                'total' => $templates->total(),
            ]);
        }

        $categories = ['order' => 'Order', 'user' => 'User', 'newsletter' => 'Newsletter', 'system' => 'System'];

        return view('admin.email-template.index', compact('templates', 'search', 'category', 'categories'));
    }

    public function create(): ViewContract
    {
        $categories = ['order' => 'Order', 'user' => 'User', 'newsletter' => 'Newsletter', 'system' => 'System'];
        return view('admin.email-template.create', compact('categories'));
    }

    public function store(StoreEmailTemplateRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        if (!isset($validated['is_active'])) {
            $validated['is_active'] = true;
        }

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $variables = $validated['variables'] ?? [];
        unset($validated['variables']);

        $template = $this->emailTemplateRepository->create($validated);

        if (!empty($variables)) {
            foreach ($variables as $variable) {
                if (!empty($variable['variable_name'])) {
                    $template->variables()->create([
                        'variable_name' => $variable['variable_name'],
                        'variable_description' => $variable['variable_description'] ?? null,
                        'example_value' => $variable['example_value'] ?? null,
                        'is_required' => $variable['is_required'] ?? false,
                    ]);
                }
            }
        }

        return redirect()->route('admin.email-templates.index')
            ->with('success', 'Email template created successfully!');
    }

    public function show(EmailTemplate $emailTemplate): ViewContract
    {
        $emailTemplate->load(['variables', 'creator', 'updater']);
        return view('admin.email-template.show', compact('emailTemplate'));
    }

    public function edit(EmailTemplate $emailTemplate): ViewContract
    {
        $emailTemplate->load('variables');
        $categories = ['order' => 'Order', 'user' => 'User', 'newsletter' => 'Newsletter', 'system' => 'System'];
        return view('admin.email-template.edit', compact('emailTemplate', 'categories'));
    }

    public function update(UpdateEmailTemplateRequest $request, EmailTemplate $emailTemplate): RedirectResponse
    {
        $validated = $request->validated();

        $validated['updated_by'] = Auth::id();
        $validated['version'] = $emailTemplate->version + 1;

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $variables = $validated['variables'] ?? [];
        unset($validated['variables']);

        $this->emailTemplateRepository->update($emailTemplate, $validated);

        if ($request->has('variables') && is_array($variables)) {
            $emailTemplate->variables()->delete();
            foreach ($variables as $variable) {
                if (!empty($variable['variable_name'])) {
                    $emailTemplate->variables()->create([
                        'variable_name' => $variable['variable_name'],
                        'variable_description' => $variable['variable_description'] ?? null,
                        'example_value' => $variable['example_value'] ?? null,
                        'is_required' => $variable['is_required'] ?? false,
                    ]);
                }
            }
        }

        return redirect()->route('admin.email-templates.index')
            ->with('success', 'Email template updated successfully!');
    }

    public function destroy(EmailTemplate $emailTemplate): RedirectResponse
    {
        if ($emailTemplate->category === 'system') {
            return redirect()->route('admin.email-templates.index')
                ->with('error', 'System templates cannot be deleted.');
        }

        $this->emailTemplateRepository->delete($emailTemplate);

        return redirect()->route('admin.email-templates.index')
            ->with('success', 'Email template deleted successfully!');
    }

    public function duplicate(EmailTemplate $emailTemplate): RedirectResponse
    {
        $newTemplate = $emailTemplate->replicate();
        $newTemplate->name = $emailTemplate->name . ' (Copy)';
        $newTemplate->slug = null;
        $newTemplate->version = 1;
        $newTemplate->created_by = Auth::id();
        $newTemplate->updated_by = Auth::id();
        $newTemplate->save();

        foreach ($emailTemplate->variables as $variable) {
            $newTemplate->variables()->create($variable->toArray());
        }

        return redirect()->route('admin.email-templates.edit', $newTemplate)
            ->with('success', 'Email template duplicated successfully!');
    }

    public function preview(Request $request, EmailTemplate $emailTemplate): JsonResponse
    {
        $variables = $request->get('variables', []);
        
        $subject = $emailTemplate->subject;
        $body = $emailTemplate->body;

        foreach ($variables as $key => $value) {
            $subject = str_replace('{' . $key . '}', $value, $subject);
            $body = str_replace('{' . $key . '}', $value, $body);
        }

        return response()->json([
            'success' => true,
            'subject' => $subject,
            'body' => $body,
        ]);
    }

    public function previewInBrowser(Request $request, EmailTemplate $emailTemplate): ViewContract
    {
        $variables = $request->get('variables', []);
        
        $subject = $emailTemplate->subject;
        $body = $emailTemplate->body;

        foreach ($variables as $key => $value) {
            $subject = str_replace('{' . $key . '}', $value, $subject);
            $body = str_replace('{' . $key . '}', $value, $body);
        }

        $settings = \App\Helpers\SettingHelper::all();
        $logoUrl = url('assets/frontend/images/logo.png');
        if (!filter_var($logoUrl, FILTER_VALIDATE_URL)) {
            $logoUrl = config('app.url') . '/assets/frontend/images/logo.png';
        }
        $contactPhone = \App\Helpers\SettingHelper::getFirstFromArraySetting($settings, 'phones') ?? '+64 4-568 7770';
        $contactEmail = \App\Helpers\SettingHelper::getFirstFromArraySetting($settings, 'emails') ?? 'info@paperwings.co.nz';
        $socialLinks = \App\Helpers\SettingHelper::extractSocialLinks($settings);

        return view('emails.template-body', [
            'body' => $body,
            'logoUrl' => $logoUrl,
            'contactPhone' => $contactPhone,
            'contactEmail' => $contactEmail,
            'socialLinks' => $socialLinks,
        ]);
    }

    public function updateStatus(Request $request, EmailTemplate $emailTemplate): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'is_active' => 'required|boolean',
        ]);

        $emailTemplate->update(['is_active' => $validated['is_active']]);

        $statusLabel = $validated['is_active'] ? 'Active' : 'Inactive';

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Email template set to {$statusLabel}",
                'status' => $validated['is_active']
            ]);
        }

        return redirect()->back()
                        ->with('success', "Email template set to {$statusLabel}");
    }

    public function sendTest(Request $request, EmailTemplate $emailTemplate): RedirectResponse
    {
        $request->validate([
            'test_email' => 'required|email:dns',
        ]);

        $variables = $request->get('variables', []);

        try {
            \Mail::send([], [], function ($message) use ($emailTemplate, $variables, $request) {
                $subject = $emailTemplate->subject;
                $body = $emailTemplate->body;

                foreach ($variables as $key => $value) {
                    $subject = str_replace('{' . $key . '}', $value, $subject);
                    $body = str_replace('{' . $key . '}', $value, $body);
                }

                $message->to($request->test_email)
                    ->subject($subject)
                    ->html($body);
            });

            return redirect()->back()
                ->with('success', 'Test email sent successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to send test email: ' . $e->getMessage());
        }
    }

    /**
     * Preview all email templates with sample data
     */
    public function previewAll(): ViewContract
    {
        $templates = EmailTemplate::with('variables')
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        $previews = [];
        $settings = \App\Helpers\SettingHelper::all();
        $logoUrl = \App\Helpers\SettingHelper::logo();
        $contactPhone = \App\Helpers\SettingHelper::getFirstFromArraySetting($settings, 'phones') ?? '+64 9 123 4567';
        $contactEmail = \App\Helpers\SettingHelper::getFirstFromArraySetting($settings, 'emails') ?? 'info@paperwings.co.nz';
        $appName = \App\Helpers\SettingHelper::siteName();

        foreach ($templates as $template) {
            $sampleVariables = $this->generateSampleVariables($template, $logoUrl, $contactPhone, $contactEmail, $appName);
            
            $subject = $template->subject;
            $body = $template->body;

            foreach ($sampleVariables as $key => $value) {
                $subject = str_replace('{' . $key . '}', $value ?? '', $subject);
                $body = str_replace('{' . $key . '}', $value ?? '', $body);
            }

            $previews[] = [
                'template' => $template,
                'subject' => $subject,
                'body' => $body, // Keep original body, extract in view
                'variables' => $sampleVariables,
            ];
        }

        $socialLinks = \App\Helpers\SettingHelper::extractSocialLinks($settings);

        return view('admin.email-template.preview-all', [
            'previews' => $previews,
            'logoUrl' => $logoUrl,
            'contactPhone' => $contactPhone,
            'contactEmail' => $contactEmail,
            'socialLinks' => $socialLinks,
        ]);
    }

    /**
     * Generate sample variables for a template based on its slug
     */
    private function generateSampleVariables(EmailTemplate $template, string $logoUrl, string $contactPhone, string $contactEmail, string $appName): array
    {
        $slug = $template->slug;
        $baseVariables = [
            'logo_url' => $logoUrl,
            'app_name' => $appName,
            'contact_phone' => $contactPhone,
            'contact_email' => $contactEmail,
            'current_year' => date('Y'),
        ];

        switch ($slug) {
            case 'order_confirmation':
                return array_merge($baseVariables, [
                    'customer_name' => 'John Doe',
                    'order_number' => 'ORD-20260121-ABC123',
                    'order_date' => date('F d, Y'),
                    'order_status' => 'Pending',
                    'payment_status' => 'Paid',
                    'order_items' => $this->getSampleOrderItems(),
                    'subtotal' => '100.00',
                    'shipping_cost' => '10.00',
                    'tax' => '15.00',
                    'total' => '125.00',
                    'shipping_name' => 'John Doe',
                    'shipping_address_line1' => '123 Main Street',
                    'shipping_address_line2' => 'Apt 4B',
                    'shipping_city' => 'Auckland',
                    'shipping_state' => 'Auckland',
                    'shipping_zip' => '1010',
                    'shipping_country' => 'New Zealand',
                    'billing_name' => 'John Doe',
                    'billing_address_line1' => '123 Main Street',
                    'billing_address_line2' => 'Apt 4B',
                    'billing_city' => 'Auckland',
                    'billing_state' => 'Auckland',
                    'billing_zip' => '1010',
                    'billing_country' => 'New Zealand',
                    'payment_method' => 'Credit Card',
                    'payment_card' => '4242',
                    'order_view_url' => url('/account/orders/ORD-20260121-ABC123'),
                ]);

            case 'order_processing':
                return array_merge($baseVariables, [
                    'customer_name' => 'John Doe',
                    'order_number' => 'ORD-20260121-ABC123',
                    'order_total' => '$125.00',
                    'processing_time' => '2-3 business days',
                ]);

            case 'order_shipped':
                return array_merge($baseVariables, [
                    'customer_name' => 'John Doe',
                    'order_number' => 'ORD-20260121-ABC123',
                    'order_total' => '$125.00',
                    'tracking_number' => 'TRACK123456789',
                    'shipping_carrier' => 'NZ Post',
                    'tracking_url' => 'https://tracking.nzpost.co.nz/TRACK123456789',
                    'estimated_delivery_date' => date('F d, Y', strtotime('+5 days')),
                ]);

            case 'order_delivered':
                return array_merge($baseVariables, [
                    'customer_name' => 'John Doe',
                    'order_number' => 'ORD-20260121-ABC123',
                    'delivery_date' => date('F d, Y'),
                    'review_url' => url('/products/review'),
                ]);

            case 'order_cancelled':
                return array_merge($baseVariables, [
                    'customer_name' => 'John Doe',
                    'order_number' => 'ORD-20260121-ABC123',
                    'cancellation_reason' => 'Customer request',
                    'refund_info' => 'Refund will be processed within 5-7 business days',
                ]);

            case 'welcome_email':
                return array_merge($baseVariables, [
                    'customer_name' => 'John Doe',
                    'login_url' => url('/login'),
                ]);

            case 'password_reset':
                return array_merge($baseVariables, [
                    'customer_name' => 'John Doe',
                    'reset_link' => url('/reset-password?token=sample-token-123'),
                    'expiry_time' => '60 minutes',
                ]);

            case 'email_verification':
                return array_merge($baseVariables, [
                    'customer_name' => 'John Doe',
                    'verification_link' => url('/verify-email?token=sample-token-123'),
                ]);

            case 'contact_notification':
                return array_merge($baseVariables, [
                    'contact_name' => 'John Doe',
                    'message_subject' => 'Product Inquiry',
                    'message_preview' => 'I would like to know more about your products...',
                    'reference_number' => 'CONT-20260121-001',
                ]);

            case 'contact_status_update':
                return array_merge($baseVariables, [
                    'contact_name' => 'John Doe',
                    'message_subject' => 'Product Inquiry',
                    'status' => 'Solved',
                    'old_status' => 'Pending',
                    'new_status' => 'Solved',
                    'resolution_message' => 'Your inquiry has been resolved. Thank you for contacting us.',
                    'response_time' => '24 hours',
                    'feedback_url' => url('/contact/feedback'),
                ]);

            default:
                return $baseVariables;
        }
    }

    /**
     * Get sample order items HTML
     */
    private function getSampleOrderItems(): string
    {
        return '
        <tr>
            <td style="padding: 10px; border-bottom: 1px solid #eee;">
                <strong>Sample Product 1</strong><br>
                <small>Quantity: 2</small>
            </td>
            <td style="padding: 10px; border-bottom: 1px solid #eee; text-align: right;">$50.00</td>
        </tr>
        <tr>
            <td style="padding: 10px; border-bottom: 1px solid #eee;">
                <strong>Sample Product 2</strong><br>
                <small>Quantity: 1</small>
            </td>
            <td style="padding: 10px; border-bottom: 1px solid #eee; text-align: right;">$50.00</td>
        </tr>';
    }

    /**
     * Extract clean email content from full HTML document
     * Removes DOCTYPE, html, body tags and extracts only content rows (no nested structures)
     */
    private function extractEmailContent(string $body): string
    {
        if (empty($body)) {
            \Log::debug('EmailTemplate: extractEmailContent - Empty body');
            return '';
        }

        \Log::debug('EmailTemplate: extractEmailContent - Original body length: ' . strlen($body));
        \Log::debug('EmailTemplate: extractEmailContent - First 200 chars: ' . substr($body, 0, 200));

        // Check if it's a full HTML document
        $isFullHtml = stripos($body, '<!DOCTYPE') !== false || 
                     stripos($body, '<html') !== false ||
                     stripos($body, '<body') !== false;

        \Log::debug('EmailTemplate: extractEmailContent - Is full HTML: ' . ($isFullHtml ? 'YES' : 'NO'));

        if (!$isFullHtml) {
            // Not full HTML, assume it's already content rows
            \Log::debug('EmailTemplate: extractEmailContent - Returning body as-is (not full HTML)');
            return $body;
        }

        // Step 1: Extract content between <body> tags
        if (preg_match('/<body[^>]*>(.*?)<\/body>/is', $body, $bodyMatches)) {
            $bodyContent = trim($bodyMatches[1]);
            \Log::debug('EmailTemplate: extractEmailContent - Extracted from <body> tag, length: ' . strlen($bodyContent));
        } else {
            $bodyContent = $body;
            \Log::debug('EmailTemplate: extractEmailContent - No <body> tag found, using body as-is');
        }

        // Step 2: Find the main content table (width="600" or max-width: 600px)
        $mainTableContent = '';
        if (preg_match('/<table[^>]*width[^>]*600[^>]*>(.*?)<\/table>/is', $bodyContent, $tableMatches)) {
            $mainTableContent = $tableMatches[1];
            \Log::debug('EmailTemplate: extractEmailContent - Found table with width=600, inner content length: ' . strlen($mainTableContent));
        } elseif (preg_match('/<table[^>]*>(.*?)<\/table>/is', $bodyContent, $tableMatches)) {
            $mainTableContent = $tableMatches[1];
            \Log::debug('EmailTemplate: extractEmailContent - Found any table, inner content length: ' . strlen($mainTableContent));
        } else {
            $mainTableContent = $bodyContent;
            \Log::debug('EmailTemplate: extractEmailContent - No table found, using bodyContent');
        }

        // Check if mainTableContent still contains full HTML structure (NESTING ISSUE)
        $hasNestedHtml = stripos($mainTableContent, '<!DOCTYPE') !== false || 
                        stripos($mainTableContent, '<html') !== false ||
                        stripos($mainTableContent, '<body') !== false;
        
        if ($hasNestedHtml) {
            \Log::warning('EmailTemplate: extractEmailContent - NESTING DETECTED! mainTableContent still contains full HTML structure');
            \Log::debug('EmailTemplate: extractEmailContent - Nested HTML first 300 chars: ' . substr($mainTableContent, 0, 300));
        }

        // Step 3: Extract <tr> rows, but be careful with nested structures
        // Use DOMDocument for more reliable parsing
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        @$dom->loadHTML('<?xml encoding="UTF-8">' . $mainTableContent);
        libxml_clear_errors();
        
        $contentRows = [];
        $xpath = new \DOMXPath($dom);
        $rows = $xpath->query('//tr');
        
        \Log::debug('EmailTemplate: extractEmailContent - Found ' . $rows->length . ' <tr> rows');
        
        foreach ($rows as $index => $row) {
            $rowHtml = $dom->saveHTML($row);
            
            // Check if this row contains nested full HTML
            $rowHasNestedHtml = stripos($rowHtml, '<!DOCTYPE') !== false || 
                               stripos($rowHtml, '<html') !== false ||
                               stripos($rowHtml, '<body') !== false;
            
            if ($rowHasNestedHtml) {
                \Log::warning("EmailTemplate: extractEmailContent - Row #{$index} contains nested full HTML structure!");
                \Log::debug("EmailTemplate: extractEmailContent - Row #{$index} first 200 chars: " . substr($rowHtml, 0, 200));
            }
            
            // Filter out structural rows
            $lowerRow = strtolower($rowHtml);
            $isStructural = stripos($lowerRow, 'logo') !== false || 
                           stripos($lowerRow, 'top bar') !== false ||
                           stripos($lowerRow, 'footer') !== false || 
                           stripos($lowerRow, 'copyright') !== false ||
                           stripos($lowerRow, 'get in touch') !== false ||
                           stripos($lowerRow, 'social media') !== false ||
                           stripos($lowerRow, 'contact us') !== false ||
                           stripos($lowerRow, 'unsubscribe') !== false;
            
            if (!$isStructural && !$rowHasNestedHtml) {
                $contentRows[] = $rowHtml;
                \Log::debug("EmailTemplate: extractEmailContent - Row #{$index} added to content (length: " . strlen($rowHtml) . ")");
            } else {
                \Log::debug("EmailTemplate: extractEmailContent - Row #{$index} filtered out (structural: " . ($isStructural ? 'YES' : 'NO') . ", nested HTML: " . ($rowHasNestedHtml ? 'YES' : 'NO') . ")");
            }
        }

        $result = !empty($contentRows) ? implode('', $contentRows) : $mainTableContent;
        \Log::debug('EmailTemplate: extractEmailContent - Final result length: ' . strlen($result));
        \Log::debug('EmailTemplate: extractEmailContent - Final result first 200 chars: ' . substr($result, 0, 200));

        // Return only content rows
        return $result;
    }
}
