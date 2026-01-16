<!-- Tips Section -->
<div class="modern-card mb-4">
    <div class="modern-card__header">
        <h3 class="modern-card__title">
            <i class="fas fa-lightbulb"></i>
            Tips & Guidelines
        </h3>
    </div>
    <div class="modern-card__body">
        <ul class="tips-list">
            <li class="tips-list__item">
                <i class="fas fa-check-circle"></i>
                <div>
                    <strong>Using Variables</strong>
                    <p>Use curly braces <code class="code-inline">{variable_name}</code> to insert dynamic content. Variables are automatically replaced when emails are sent. Examples: <code class="code-inline">{order_number}</code>, <code class="code-inline">{customer_name}</code>, <code class="code-inline">{order_total}</code>.</p>
                </div>
            </li>
            <li class="tips-list__item">
                <i class="fas fa-check-circle"></i>
                <div>
                    <strong>Common Variables</strong>
                    <p>For order templates: <code class="code-inline">{order_number}</code>, <code class="code-inline">{order_date}</code>, <code class="code-inline">{order_total}</code>, <code class="code-inline">{customer_name}</code>, <code class="code-inline">{shipping_address}</code>. For user templates: <code class="code-inline">{user_name}</code>, <code class="code-inline">{user_email}</code>, <code class="code-inline">{reset_link}</code>.</p>
                </div>
            </li>
            <li class="tips-list__item">
                <i class="fas fa-check-circle"></i>
                <div>
                    <strong>HTML Formatting</strong>
                    <p>Use the rich text editor to format your emails with HTML. You can add headings, paragraphs, lists, links, and images. The editor provides a WYSIWYG interface for easy formatting.</p>
                </div>
            </li>
            <li class="tips-list__item">
                <i class="fas fa-check-circle"></i>
                <div>
                    <strong>Email Subject Line</strong>
                    <p>Keep subject lines concise (50-60 characters) and include important variables like order numbers. Examples: <code class="code-inline">"Order Confirmation - {order_number}"</code> or <code class="code-inline">"Welcome {customer_name}!"</code>.</p>
                </div>
            </li>
            <li class="tips-list__item">
                <i class="fas fa-check-circle"></i>
                <div>
                    <strong>Template Categories</strong>
                    <p>Choose the appropriate category: <strong>Order</strong> for order-related emails, <strong>User</strong> for account emails, <strong>Newsletter</strong> for marketing, <strong>System</strong> for system notifications.</p>
                </div>
            </li>
            <li class="tips-list__item">
                <i class="fas fa-check-circle"></i>
                <div>
                    <strong>Auto-Generated Slugs</strong>
                    <p>Slugs are automatically generated from template names if left empty. They must be unique and URL-friendly. You can customize them, but avoid special characters and spaces.</p>
                </div>
            </li>
            <li class="tips-list__item">
                <i class="fas fa-check-circle"></i>
                <div>
                    <strong>Testing Templates</strong>
                    <p>Always use the "Send Test Email" feature before activating a template. This allows you to preview how the email will look and verify that all variables are working correctly.</p>
                </div>
            </li>
            <li class="tips-list__item">
                <i class="fas fa-check-circle"></i>
                <div>
                    <strong>Active Status</strong>
                    <p>Only active templates are used by the system. Set a template to inactive if you want to temporarily disable it without deleting it. This is useful for testing or seasonal templates.</p>
                </div>
            </li>
            <li class="tips-list__item">
                <i class="fas fa-check-circle"></i>
                <div>
                    <strong>Version Control</strong>
                    <p>Each template has a version number that increments automatically when updated. This helps track changes and allows you to revert to previous versions if needed.</p>
                </div>
            </li>
            <li class="tips-list__item">
                <i class="fas fa-check-circle"></i>
                <div>
                    <strong>Best Practices</strong>
                    <p>Keep emails professional, clear, and concise. Use a friendly but professional tone. Include all necessary information and make sure call-to-action buttons or links are prominent and easy to find.</p>
                </div>
            </li>
        </ul>
    </div>
</div>
