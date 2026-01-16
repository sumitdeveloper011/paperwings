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
            'test_email' => 'required|email',
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
}
