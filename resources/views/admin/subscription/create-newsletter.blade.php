@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-paper-plane"></i>
                    Send Newsletter
                </h1>
                <p class="page-header__subtitle">Compose and send newsletter to subscribers</p>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-outline-secondary btn-icon">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Subscriptions</span>
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="modern-card">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-envelope"></i>
                        Newsletter Content
                    </h3>
                </div>
                <div class="modern-card__body">
                    <form method="POST" action="{{ route('admin.subscriptions.send-newsletter') }}" id="newsletterForm">
                        @csrf

                        <div class="form-group-modern">
                            <label for="email_template_id" class="form-label-modern">
                                <i class="fas fa-file-alt"></i>
                                Use Email Template (Optional)
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-file-alt input-icon"></i>
                                <select class="form-input-modern" id="email_template_id" name="email_template_id">
                                    <option value="">Select a template...</option>
                                    @foreach($templates as $template)
                                        <option value="{{ $template->id }}" data-subject="{{ $template->subject }}" data-body="{{ $template->body }}">
                                            {{ $template->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i>
                                Select a newsletter template to pre-fill the subject and body
                            </div>
                        </div>

                        <div class="form-group-modern">
                            <label for="subject" class="form-label-modern">
                                <i class="fas fa-heading"></i>
                                Subject <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-heading input-icon"></i>
                                <input type="text"
                                       class="form-input-modern @error('subject') is-invalid @enderror"
                                       id="subject"
                                       name="subject"
                                       value="{{ old('subject') }}"
                                       placeholder="Enter newsletter subject"
                                       required>
                            </div>
                            @error('subject')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label for="body" class="form-label-modern">
                                <i class="fas fa-align-left"></i>
                                Email Body <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <textarea class="form-input-modern @error('body') is-invalid @enderror"
                                          id="body"
                                          name="body"
                                          rows="15"
                                          placeholder="Enter newsletter content (HTML supported)"
                                          required>{{ old('body') }}</textarea>
                            </div>
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i>
                                You can use HTML formatting. Available variables: {email}, {unsubscribe_url}
                            </div>
                            @error('body')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="fas fa-users"></i>
                                Send To <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <div class="radio-group">
                                    <label class="radio-label">
                                        <input type="radio" name="send_to" value="active" checked>
                                        <span class="radio-custom"></span>
                                        <span class="radio-text">Active Subscribers Only ({{ $activeCount }} emails)</span>
                                    </label>
                                    <label class="radio-label">
                                        <input type="radio" name="send_to" value="all">
                                        <span class="radio-custom"></span>
                                        <span class="radio-text">All Subscribers ({{ $totalCount }} emails)</span>
                                    </label>
                                </div>
                            </div>
                            @error('send_to')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label for="test_email" class="form-label-modern">
                                <i class="fas fa-vial"></i>
                                Test Email (Optional)
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-envelope input-icon"></i>
                                <input type="email"
                                       class="form-input-modern @error('test_email') is-invalid @enderror"
                                       id="test_email"
                                       name="test_email"
                                       value="{{ old('test_email') }}"
                                       placeholder="Enter email to send test newsletter">
                            </div>
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i>
                                Send a test email before sending to all subscribers
                            </div>
                            @error('test_email')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <div class="button-group">
                                <button type="submit" class="btn btn-primary btn-icon" id="sendBtn">
                                    <i class="fas fa-paper-plane"></i>
                                    <span id="sendBtnText">Send Newsletter</span>
                                </button>
                                @if(old('test_email') || request()->has('test'))
                                    <button type="submit" class="btn btn-outline-primary btn-icon" name="test_only" value="1" id="testBtn">
                                        <i class="fas fa-vial"></i>
                                        <span>Send Test Only</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="modern-card">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-info-circle"></i>
                        Newsletter Tips
                    </h3>
                </div>
                <div class="modern-card__body">
                    <div class="info-box">
                        <h4 class="info-box__title">
                            <i class="fas fa-lightbulb"></i>
                            Best Practices
                        </h4>
                        <ul class="info-box__list">
                            <li>Keep subject lines clear and engaging</li>
                            <li>Use HTML formatting for better presentation</li>
                            <li>Always send a test email first</li>
                            <li>Include an unsubscribe link (automatically added)</li>
                            <li>Personalize content with {email} variable</li>
                        </ul>
                    </div>

                    <div class="info-box" style="margin-top: 1.5rem;">
                        <h4 class="info-box__title">
                            <i class="fas fa-code"></i>
                            Available Variables
                        </h4>
                        <ul class="info-box__list">
                            <li><code>{email}</code> - Subscriber email address</li>
                            <li><code>{unsubscribe_url}</code> - Unsubscribe link (auto-added)</li>
                        </ul>
                    </div>

                    <div class="info-box" style="margin-top: 1.5rem; background: #fff3cd; border-left: 4px solid #ffc107;">
                        <h4 class="info-box__title" style="color: #856404;">
                            <i class="fas fa-exclamation-triangle"></i>
                            Important Notes
                        </h4>
                        <ul class="info-box__list" style="color: #856404;">
                            <li>Newsletters are sent via queue for better performance</li>
                            <li>Large lists may take time to process</li>
                            <li>Only active subscribers receive emails</li>
                            <li>Test email is sent immediately</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const templateSelect = document.getElementById('email_template_id');
    const subjectInput = document.getElementById('subject');
    const bodyTextarea = document.getElementById('body');
    const form = document.getElementById('newsletterForm');
    const sendBtn = document.getElementById('sendBtn');
    const sendBtnText = document.getElementById('sendBtnText');

    if (templateSelect) {
        templateSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value && selectedOption.dataset.subject) {
                if (confirm('This will replace your current subject and body. Continue?')) {
                    subjectInput.value = selectedOption.dataset.subject || '';
                    bodyTextarea.value = selectedOption.dataset.body || '';
                }
            }
        });
    }

    if (form) {
        form.addEventListener('submit', function(e) {
            const testEmail = document.getElementById('test_email').value;
            const sendTo = document.querySelector('input[name="send_to"]:checked').value;
            const subject = subjectInput.value.trim();
            const body = bodyTextarea.value.trim();

            if (!testEmail && !confirm(`Are you sure you want to send this newsletter to ${sendTo === 'active' ? '{{ $activeCount }}' : '{{ $totalCount }}'} subscribers?`)) {
                e.preventDefault();
                return false;
            }

            sendBtn.disabled = true;
            sendBtnText.textContent = testEmail ? 'Sending Test...' : 'Sending Newsletter...';
        });
    }
});
</script>
@endsection
