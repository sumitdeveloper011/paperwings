<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmailTemplate;
use App\Models\EmailTemplateVariable;
use App\Models\User;
use Illuminate\Support\Str;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['SuperAdmin', 'Admin']);
        })->first();

        $userId = $adminUser ? $adminUser->id : null;

        $templates = [
            [
                'name' => 'Welcome Email',
                'slug' => 'welcome_email',
                'subject' => 'Welcome to {app_name}!',
                'body' => $this->getWelcomeEmailTemplate(),
                'description' => 'Sent to new users when they register',
                'category' => 'user',
                'is_active' => true,
                'created_by' => $userId,
                'updated_by' => $userId,
            ],
            [
                'name' => 'Email Verification',
                'slug' => 'email_verification',
                'subject' => 'Verify Your Email Address - {app_name}',
                'body' => $this->getEmailVerificationTemplate(),
                'description' => 'Sent to verify user email address',
                'category' => 'system',
                'is_active' => true,
                'created_by' => $userId,
                'updated_by' => $userId,
            ],
            [
                'name' => 'Password Reset',
                'slug' => 'password_reset',
                'subject' => 'Reset Your Password - {app_name}',
                'body' => $this->getPasswordResetTemplate(),
                'description' => 'Sent when user requests password reset',
                'category' => 'system',
                'is_active' => true,
                'created_by' => $userId,
                'updated_by' => $userId,
            ],
            [
                'name' => 'Contact Notification',
                'slug' => 'contact_notification',
                'subject' => 'Thank You for Contacting Us - {app_name}',
                'body' => $this->getContactNotificationTemplate(),
                'description' => 'Sent to customers after they submit a contact form',
                'category' => 'user',
                'is_active' => true,
                'created_by' => $userId,
                'updated_by' => $userId,
            ],
            [
                'name' => 'Contact Status Update',
                'slug' => 'contact_status_update',
                'subject' => 'Your Contact Message Status Updated - {app_name}',
                'body' => $this->getContactStatusUpdateTemplate(),
                'description' => 'Sent to customers when their contact message status is updated',
                'category' => 'user',
                'is_active' => true,
                'created_by' => $userId,
                'updated_by' => $userId,
            ],
            [
                'name' => 'Welcome Admin User',
                'slug' => 'welcome_admin_user',
                'subject' => 'Welcome to {app_name} Admin Panel',
                'body' => $this->getWelcomeAdminUserTemplate(),
                'description' => 'Sent to new admin users when their account is created',
                'category' => 'system',
                'is_active' => true,
                'created_by' => $userId,
                'updated_by' => $userId,
            ],
            [
                'name' => 'Contact Admin Notification',
                'slug' => 'contact_admin_notification',
                'subject' => 'New Contact Form Submission - {app_name}',
                'body' => $this->getContactAdminNotificationTemplate(),
                'description' => 'Sent to admin when a new contact form is submitted',
                'category' => 'system',
                'is_active' => true,
                'created_by' => $userId,
                'updated_by' => $userId,
            ],
            [
                'name' => 'Order Confirmation',
                'slug' => 'order_confirmation',
                'subject' => 'Order Confirmation - Order #{order_number}',
                'body' => $this->getOrderConfirmationTemplate(),
                'description' => 'Sent to customers when an order is placed',
                'category' => 'order',
                'is_active' => true,
                'created_by' => $userId,
                'updated_by' => $userId,
            ],
            [
                'name' => 'Order Admin Notification',
                'slug' => 'order_admin_notification',
                'subject' => 'New Order Received - Order #{order_number}',
                'body' => $this->getOrderAdminNotificationTemplate(),
                'description' => 'Sent to admin when a new order is placed',
                'category' => 'order',
                'is_active' => true,
                'created_by' => $userId,
                'updated_by' => $userId,
            ],
            [
                'name' => 'Order Processing',
                'slug' => 'order_processing',
                'subject' => 'Order Processing - Order #{order_number}',
                'body' => $this->getOrderProcessingTemplate(),
                'description' => 'Sent to customers when an order is being processed',
                'category' => 'order',
                'is_active' => true,
                'created_by' => $userId,
                'updated_by' => $userId,
            ],
            [
                'name' => 'Order Shipped',
                'slug' => 'order_shipped',
                'subject' => 'Order Shipped - Order #{order_number}',
                'body' => $this->getOrderShippedTemplate(),
                'description' => 'Sent to customers when an order has been shipped',
                'category' => 'order',
                'is_active' => true,
                'created_by' => $userId,
                'updated_by' => $userId,
            ],
            [
                'name' => 'Order Delivered',
                'slug' => 'order_delivered',
                'subject' => 'Order Delivered - Order #{order_number}',
                'body' => $this->getOrderDeliveredTemplate(),
                'description' => 'Sent to customers when an order has been delivered',
                'category' => 'order',
                'is_active' => true,
                'created_by' => $userId,
                'updated_by' => $userId,
            ],
            [
                'name' => 'Order Cancelled',
                'slug' => 'order_cancelled',
                'subject' => 'Order Cancelled - Order #{order_number}',
                'body' => $this->getOrderCancelledTemplate(),
                'description' => 'Sent to customers when an order has been cancelled',
                'category' => 'order',
                'is_active' => true,
                'created_by' => $userId,
                'updated_by' => $userId,
            ],
            [
                'name' => 'Review Notification',
                'slug' => 'review_notification',
                'subject' => 'Thank You for Your Review - {app_name}',
                'body' => $this->getReviewNotificationTemplate(),
                'description' => 'Sent to customers when they submit a product review',
                'category' => 'review',
                'is_active' => true,
                'created_by' => $userId,
                'updated_by' => $userId,
            ],
            [
                'name' => 'Review Admin Notification',
                'slug' => 'review_admin_notification',
                'subject' => 'New Product Review Submitted - {app_name}',
                'body' => $this->getReviewAdminNotificationTemplate(),
                'description' => 'Sent to admin when a new product review is submitted',
                'category' => 'review',
                'is_active' => true,
                'created_by' => $userId,
                'updated_by' => $userId,
            ],
        ];

        foreach ($templates as $templateData) {
            $template = EmailTemplate::firstOrCreate(
                ['slug' => $templateData['slug']],
                array_merge($templateData, ['uuid' => Str::uuid()])
            );

            // Seed variables for each template
            switch ($templateData['slug']) {
                case 'welcome_email':
                    $this->seedWelcomeEmailVariables($template->id);
                    break;
                case 'email_verification':
                    $this->seedEmailVerificationVariables($template->id);
                    break;
                case 'password_reset':
                    $this->seedPasswordResetVariables($template->id);
                    break;
                case 'contact_notification':
                    $this->seedContactNotificationVariables($template->id);
                    break;
                case 'contact_status_update':
                    $this->seedContactStatusUpdateVariables($template->id);
                    break;
                case 'welcome_admin_user':
                    $this->seedWelcomeAdminUserVariables($template->id);
                    break;
                case 'contact_admin_notification':
                    $this->seedContactAdminNotificationVariables($template->id);
                    break;
                case 'order_confirmation':
                    $this->seedOrderConfirmationVariables($template->id);
                    break;
                case 'order_admin_notification':
                    $this->seedOrderAdminNotificationVariables($template->id);
                    break;
                case 'order_processing':
                    $this->seedOrderProcessingVariables($template->id);
                    break;
                case 'order_shipped':
                    $this->seedOrderShippedVariables($template->id);
                    break;
                case 'order_delivered':
                    $this->seedOrderDeliveredVariables($template->id);
                    break;
                case 'order_cancelled':
                    $this->seedOrderCancelledVariables($template->id);
                    break;
                case 'review_notification':
                    $this->seedReviewNotificationVariables($template->id);
                    break;
                case 'review_admin_notification':
                    $this->seedReviewAdminNotificationVariables($template->id);
                    break;
            }
        }

        $this->command->info('✓ Seeded ' . count($templates) . ' email templates');
    }

    private function seedWelcomeEmailVariables(int $templateId): void
    {
        $variables = [
            ['variable_name' => 'user_name', 'variable_description' => 'User full name', 'example_value' => 'John Doe', 'is_required' => true],
            ['variable_name' => 'app_name', 'variable_description' => 'Application name', 'example_value' => 'PaperWings', 'is_required' => true],
            ['variable_name' => 'shop_link', 'variable_description' => 'URL to shop/home page', 'example_value' => 'https://example.com', 'is_required' => true],
        ];

        foreach ($variables as $var) {
            EmailTemplateVariable::firstOrCreate(
                [
                    'template_id' => $templateId,
                    'variable_name' => $var['variable_name'],
                ],
                [
                    'variable_description' => $var['variable_description'],
                    'example_value' => $var['example_value'],
                    'is_required' => $var['is_required'],
                ]
            );
        }
    }

    private function getWelcomeEmailTemplate(): string
    {
        return '<!-- Body Content -->
                    <tr>
                        <td style="padding: 40px 40px 30px 40px; background-color: #ffffff;">
                            <!-- Greeting -->
                            <p style="margin: 0 0 20px 0; color: #000000; font-size: 16px; font-weight: 400;">
                                Hi {user_name},
                            </p>
                            <!-- Main content -->
                            <p style="margin: 0 0 20px 0; color: #000000; font-size: 16px; font-weight: 400; line-height: 1.6;">
                                We\'re thrilled to have you join the {app_name} family! Your account has been successfully created and you\'re all set to explore our wide range of premium stationery and office supplies.
                            </p>
                            <p style="margin: 0 0 30px 0; color: #000000; font-size: 16px; font-weight: 400; line-height: 1.6;">
                                Browse through our collection of notebooks, pens, office supplies, and more. We\'re here to help you find everything you need for your workspace.
                            </p>
                        </td>
                    </tr>
                    
                    <!-- CTA Button - Coral Red -->
                    <tr>
                        <td style="padding: 0 40px 30px 40px; text-align: center; background-color: #ffffff;">
                            <a href="{shop_link}" style="display: inline-block; padding: 16px 40px; background-color: #e95c67; color: #ffffff; text-decoration: none; border-radius: 4px; font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                                START SHOPPING
                            </a>
                        </td>
                    </tr>';
    }

    private function seedEmailVerificationVariables(int $templateId): void
    {
        $variables = [
            ['variable_name' => 'user_name', 'variable_description' => 'User full name', 'example_value' => 'John Doe', 'is_required' => true],
            ['variable_name' => 'verification_link', 'variable_description' => 'Email verification URL', 'example_value' => 'https://example.com/verify-email?token=...', 'is_required' => true],
            ['variable_name' => 'app_name', 'variable_description' => 'Application name', 'example_value' => 'PaperWings', 'is_required' => false],
        ];

        foreach ($variables as $var) {
            EmailTemplateVariable::firstOrCreate(
                [
                    'template_id' => $templateId,
                    'variable_name' => $var['variable_name'],
                ],
                [
                    'variable_description' => $var['variable_description'],
                    'example_value' => $var['example_value'],
                    'is_required' => $var['is_required'],
                ]
            );
        }
    }

    private function getEmailVerificationTemplate(): string
    {
        return '<!-- Body Content -->
                    <tr>
                        <td style="padding: 40px 40px 30px 40px; background-color: #ffffff;">
                            <p style="margin: 0 0 20px 0; color: #000000; font-size: 16px; font-weight: 400;">
                                Hi {user_name},
                            </p>
                            <p style="margin: 0 0 30px 0; color: #000000; font-size: 16px; font-weight: 400; line-height: 1.6;">
                                You\'re almost ready to get started. Please click on the button below to verify your email address and enjoy exclusive cleaning services with us!
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 0 40px 30px 40px; text-align: center; background-color: #ffffff;">
                            <a href="{verification_link}" style="display: inline-block; padding: 16px 40px; background-color: #e95c67; color: #ffffff; text-decoration: none; border-radius: 4px; font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                                VERIFY YOUR EMAIL
                            </a>
                        </td>
                    </tr>';
    }

    private function seedPasswordResetVariables(int $templateId): void
    {
        $variables = [
            ['variable_name' => 'user_name', 'variable_description' => 'User full name', 'example_value' => 'John Doe', 'is_required' => true],
            ['variable_name' => 'reset_link', 'variable_description' => 'Password reset URL', 'example_value' => 'https://example.com/reset-password?token=...', 'is_required' => true],
            ['variable_name' => 'expiration_time', 'variable_description' => 'Link expiration time', 'example_value' => '60 minutes', 'is_required' => true],
            ['variable_name' => 'app_name', 'variable_description' => 'Application name', 'example_value' => 'PaperWings', 'is_required' => false],
        ];

        foreach ($variables as $var) {
            EmailTemplateVariable::firstOrCreate(
                [
                    'template_id' => $templateId,
                    'variable_name' => $var['variable_name'],
                ],
                [
                    'variable_description' => $var['variable_description'],
                    'example_value' => $var['example_value'],
                    'is_required' => $var['is_required'],
                ]
            );
        }
    }

    private function getPasswordResetTemplate(): string
    {
        return '<!-- Body Content -->
                    <tr>
                        <td style="padding: 40px 40px 30px 40px; background-color: #ffffff;">
                            <p style="margin: 0 0 20px 0; color: #000000; font-size: 16px; font-weight: 400;">
                                Hi {user_name},
                            </p>
                            <p style="margin: 0 0 20px 0; color: #000000; font-size: 16px; font-weight: 400; line-height: 1.6;">
                                We received a request to reset your password for your {app_name} account. If you made this request, please click the button below to create a new password.
                            </p>
                            <p style="margin: 0 0 30px 0; color: #000000; font-size: 16px; font-weight: 400; line-height: 1.6;">
                                If you didn\'t request a password reset, you can safely ignore this email. Your password will remain unchanged.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 0 40px 30px 40px; text-align: center; background-color: #ffffff;">
                            <a href="{reset_link}" style="display: inline-block; padding: 16px 40px; background-color: #e95c67; color: #ffffff; text-decoration: none; border-radius: 4px; font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                                RESET PASSWORD
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 0 40px 30px 40px; background-color: #ffffff;">
                            <p style="margin: 0; color: #666666; font-size: 13px; font-weight: 400; line-height: 1.5; text-align: center;">
                                This link will expire in {expiration_time} for security reasons. If you need a new link, please request another password reset.
                            </p>
                        </td>
                    </tr>';
    }

    private function seedContactNotificationVariables(int $templateId): void
    {
        $variables = [
            ['variable_name' => 'contact_name', 'variable_description' => 'Contact person full name', 'example_value' => 'John Doe', 'is_required' => true],
            ['variable_name' => 'message_preview', 'variable_description' => 'Preview of the contact message', 'example_value' => 'I would like to inquire about...', 'is_required' => true],
            ['variable_name' => 'reference_number', 'variable_description' => 'Contact message reference number', 'example_value' => 'CONT-20260121-ABC123', 'is_required' => true],
            ['variable_name' => 'response_time', 'variable_description' => 'Expected response time', 'example_value' => '24-48 hours', 'is_required' => false],
            ['variable_name' => 'app_name', 'variable_description' => 'Application name', 'example_value' => 'PaperWings', 'is_required' => false],
        ];

        foreach ($variables as $var) {
            EmailTemplateVariable::firstOrCreate(
                [
                    'template_id' => $templateId,
                    'variable_name' => $var['variable_name'],
                ],
                [
                    'variable_description' => $var['variable_description'],
                    'example_value' => $var['example_value'],
                    'is_required' => $var['is_required'],
                ]
            );
        }
    }

    private function getContactNotificationTemplate(): string
    {
        return '<!-- Body Content -->
                    <tr>
                        <td style="padding: 40px 40px 30px 40px; background-color: #ffffff;">
                            <p style="margin: 0 0 20px 0; color: #000000; font-size: 16px; font-weight: 400;">
                                Hi {contact_name},
                            </p>
                            <p style="margin: 0 0 20px 0; color: #000000; font-size: 16px; font-weight: 400; line-height: 1.6;">
                                Thank you for reaching out to us! We\'ve received your message and our team will get back to you as soon as possible.
                            </p>
                            <p style="margin: 0 0 30px 0; color: #000000; font-size: 16px; font-weight: 400; line-height: 1.6;">
                                We typically respond within {response_time}. If your inquiry is urgent, please feel free to call us directly.
                            </p>
                            
                            <!-- Contact Info Box -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f8f9fa; border-radius: 8px; margin-bottom: 30px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="margin: 0 0 10px 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Your Message
                                        </p>
                                        <p style="margin: 0 0 20px 0; color: #000000; font-size: 14px; font-weight: 400; line-height: 1.6;">
                                            {message_preview}
                                        </p>
                                        <p style="margin: 0 0 10px 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Reference Number
                                        </p>
                                        <p style="margin: 0; color: #2850a3; font-size: 16px; font-weight: 700;">
                                            #{reference_number}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>';
    }

    private function seedContactStatusUpdateVariables(int $templateId): void
    {
        $variables = [
            ['variable_name' => 'contact_name', 'variable_description' => 'Contact person full name', 'example_value' => 'John Doe', 'is_required' => true],
            ['variable_name' => 'reference_number', 'variable_description' => 'Contact message reference number', 'example_value' => 'CONT-20260121-ABC123', 'is_required' => true],
            ['variable_name' => 'status', 'variable_description' => 'Current status of the contact message', 'example_value' => 'Solved', 'is_required' => true],
            ['variable_name' => 'app_name', 'variable_description' => 'Application name', 'example_value' => 'PaperWings', 'is_required' => false],
        ];

        foreach ($variables as $var) {
            EmailTemplateVariable::firstOrCreate(
                [
                    'template_id' => $templateId,
                    'variable_name' => $var['variable_name'],
                ],
                [
                    'variable_description' => $var['variable_description'],
                    'example_value' => $var['example_value'],
                    'is_required' => $var['is_required'],
                ]
            );
        }
    }

    private function getContactStatusUpdateTemplate(): string
    {
        return '<!-- Body Content -->
                    <tr>
                        <td style="padding: 40px 40px 30px 40px; background-color: #ffffff;">
                            <p style="margin: 0 0 20px 0; color: #000000; font-size: 16px; font-weight: 400;">
                                Hi {contact_name},
                            </p>
                            <p style="margin: 0 0 20px 0; color: #000000; font-size: 16px; font-weight: 400; line-height: 1.6;">
                                We\'re writing to inform you that your contact inquiry (Reference #{reference_number}) has been marked as resolved.
                            </p>
                            
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f8f9fa; border-radius: 8px; margin-bottom: 30px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="margin: 0 0 10px 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Reference Number
                                        </p>
                                        <p style="margin: 0 0 20px 0; color: #2850a3; font-size: 18px; font-weight: 700;">
                                            #{reference_number}
                                        </p>
                                        <p style="margin: 0 0 10px 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Status
                                        </p>
                                        <p style="margin: 0; color: #e95c67; font-size: 16px; font-weight: 600;">
                                            {status}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>';
    }

    private function seedWelcomeAdminUserVariables(int $templateId): void
    {
        $variables = [
            ['variable_name' => 'admin_name', 'variable_description' => 'Admin user full name', 'example_value' => 'John Doe', 'is_required' => true],
            ['variable_name' => 'admin_email', 'variable_description' => 'Admin user email address', 'example_value' => 'admin@example.com', 'is_required' => true],
            ['variable_name' => 'temporary_password', 'variable_description' => 'Temporary password for admin account', 'example_value' => 'TempPass123!', 'is_required' => true],
            ['variable_name' => 'admin_role', 'variable_description' => 'Admin user role(s)', 'example_value' => 'Admin', 'is_required' => true],
            ['variable_name' => 'admin_login_url', 'variable_description' => 'URL to admin login page', 'example_value' => 'https://example.com/admin/login', 'is_required' => true],
            ['variable_name' => 'app_name', 'variable_description' => 'Application name', 'example_value' => 'PaperWings', 'is_required' => false],
        ];

        foreach ($variables as $var) {
            EmailTemplateVariable::firstOrCreate(
                [
                    'template_id' => $templateId,
                    'variable_name' => $var['variable_name'],
                ],
                [
                    'variable_description' => $var['variable_description'],
                    'example_value' => $var['example_value'],
                    'is_required' => $var['is_required'],
                ]
            );
        }
    }

    private function getWelcomeAdminUserTemplate(): string
    {
        return '<!-- Body Content -->
                    <tr>
                        <td style="padding: 40px 40px 30px 40px; background-color: #ffffff;">
                            <p style="margin: 0 0 20px 0; color: #000000; font-size: 16px; font-weight: 400;">
                                Hi {admin_name},
                            </p>
                            <p style="margin: 0 0 20px 0; color: #000000; font-size: 16px; font-weight: 400; line-height: 1.6;">
                                Your admin account has been successfully created. You now have access to the {app_name} admin panel with the following credentials:
                            </p>
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f8f9fa; border-radius: 8px; margin-bottom: 30px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="margin: 0 0 10px 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Email Address
                                        </p>
                                        <p style="margin: 0 0 20px 0; color: #000000; font-size: 16px; font-weight: 600;">
                                            {admin_email}
                                        </p>
                                        <p style="margin: 0 0 10px 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Temporary Password
                                        </p>
                                        <p style="margin: 0 0 20px 0; color: #e95c67; font-size: 16px; font-weight: 700; word-break: break-all;">
                                            {temporary_password}
                                        </p>
                                        <p style="margin: 0 0 10px 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Role
                                        </p>
                                        <p style="margin: 0; color: #2850a3; font-size: 16px; font-weight: 600;">
                                            {admin_role}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            
                            <p style="margin: 0 0 20px 0; color: #000000; font-size: 16px; font-weight: 400; line-height: 1.6;">
                                For security reasons, please change your password immediately after your first login. You can access the admin panel using the link below.
                            </p>
                            <p style="margin: 0 0 30px 0; color: #000000; font-size: 16px; font-weight: 400; line-height: 1.6;">
                                If you have any questions or need assistance, please contact the system administrator.
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Login Button -->
                    <tr>
                        <td style="padding: 0 40px 30px 40px; text-align: center; background-color: #ffffff;">
                            <a href="{admin_login_url}" style="display: inline-block; padding: 16px 40px; background-color: #e95c67; color: #ffffff; text-decoration: none; border-radius: 4px; font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                                ACCESS ADMIN PANEL
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 0 40px 30px 40px; background-color: #ffffff;">
                            <p style="margin: 0; color: #666666; font-size: 13px; font-weight: 400; line-height: 1.5; text-align: center;">
                                <strong>Security Notice:</strong> Keep your login credentials secure and do not share them with anyone. Change your password immediately after first login.
                            </p>
                        </td>
                    </tr>';
    }

    private function seedContactAdminNotificationVariables(int $templateId): void
    {
        $variables = [
            ['variable_name' => 'subject', 'variable_description' => 'Contact message subject', 'example_value' => 'Inquiry about products', 'is_required' => true],
            ['variable_name' => 'name', 'variable_description' => 'Contact person full name', 'example_value' => 'John Doe', 'is_required' => true],
            ['variable_name' => 'email', 'variable_description' => 'Contact person email address', 'example_value' => 'john@example.com', 'is_required' => true],
            ['variable_name' => 'phone_number', 'variable_description' => 'Contact person phone number', 'example_value' => '+1234567890', 'is_required' => false],
            ['variable_name' => 'message', 'variable_description' => 'Contact message content', 'example_value' => 'I would like to inquire about...', 'is_required' => true],
            ['variable_name' => 'submission_date', 'variable_description' => 'Date and time of submission', 'example_value' => 'January 21, 2026 at 10:30 AM', 'is_required' => true],
            ['variable_name' => 'reference_number', 'variable_description' => 'Contact message reference number', 'example_value' => 'CONT-20260121-ABC123', 'is_required' => true],
        ];

        foreach ($variables as $var) {
            EmailTemplateVariable::firstOrCreate(
                [
                    'template_id' => $templateId,
                    'variable_name' => $var['variable_name'],
                ],
                [
                    'variable_description' => $var['variable_description'],
                    'example_value' => $var['example_value'],
                    'is_required' => $var['is_required'],
                ]
            );
        }
    }

    private function getContactAdminNotificationTemplate(): string
    {
        return '<!-- Body Content -->
                    <tr>
                        <td style="padding: 40px 40px 30px 40px; background-color: #ffffff;">
                            <p style="margin: 0 0 20px 0; color: #000000; font-size: 16px; font-weight: 400;">
                                Hello Admin,
                            </p>
                            <p style="margin: 0 0 30px 0; color: #000000; font-size: 16px; font-weight: 400; line-height: 1.6;">
                                A new contact form submission has been received. Please review the details below:
                            </p>
                            
                            <!-- Contact Information Box -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f8f9fa; border-radius: 8px; margin-bottom: 30px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="margin: 0 0 10px 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Subject
                                        </p>
                                        <p style="margin: 0 0 20px 0; color: #2850a3; font-size: 18px; font-weight: 700;">
                                            {subject}
                                        </p>
                                        
                                        <p style="margin: 0 0 10px 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Name
                                        </p>
                                        <p style="margin: 0 0 20px 0; color: #000000; font-size: 16px; font-weight: 600;">
                                            {name}
                                        </p>
                                        
                                        <p style="margin: 0 0 10px 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Email Address
                                        </p>
                                        <p style="margin: 0 0 20px 0; color: #000000; font-size: 16px; font-weight: 400;">
                                            <a href="mailto:{email}" style="color: #2850a3; text-decoration: none;">{email}</a>
                                        </p>
                                        
                                        <p style="margin: 0 0 10px 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Phone Number
                                        </p>
                                        <p style="margin: 0 0 20px 0; color: #000000; font-size: 16px; font-weight: 400;">
                                            <a href="tel:{phone_number}" style="color: #2850a3; text-decoration: none;">{phone_number}</a>
                                        </p>
                                        
                                        <p style="margin: 0 0 10px 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Message
                                        </p>
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #ffffff; border-left: 3px solid #2850a3; border-radius: 4px;">
                                            <tr>
                                                <td style="padding: 15px;">
                                                    <p style="margin: 0; color: #000000; font-size: 16px; font-weight: 400; line-height: 1.6; white-space: pre-wrap;">
                                                        {message}
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Submission Details -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f8f9fa; border-radius: 8px; margin-bottom: 30px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="margin: 0 0 10px 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Submission Date
                                        </p>
                                        <p style="margin: 0 0 15px 0; color: #000000; font-size: 14px; font-weight: 400;">
                                            {submission_date}
                                        </p>
                                        <p style="margin: 0 0 10px 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Reference Number
                                        </p>
                                        <p style="margin: 0; color: #2850a3; font-size: 16px; font-weight: 700;">
                                            #{reference_number}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>';
    }

    private function seedOrderConfirmationVariables(int $templateId): void
    {
        $variables = [
            ['variable_name' => 'customer_name', 'variable_description' => 'Customer full name', 'example_value' => 'John Doe', 'is_required' => true],
            ['variable_name' => 'order_number', 'variable_description' => 'Order number', 'example_value' => 'ORD-20260121-ABC123', 'is_required' => true],
            ['variable_name' => 'order_date', 'variable_description' => 'Order date', 'example_value' => 'January 21, 2026', 'is_required' => true],
            ['variable_name' => 'order_status', 'variable_description' => 'Order status', 'example_value' => 'Pending', 'is_required' => true],
            ['variable_name' => 'payment_status', 'variable_description' => 'Payment status', 'example_value' => 'Paid', 'is_required' => true],
            ['variable_name' => 'order_items', 'variable_description' => 'HTML for all order items', 'example_value' => '<table>...</table>', 'is_required' => true],
            ['variable_name' => 'subtotal', 'variable_description' => 'Order subtotal', 'example_value' => '100.00', 'is_required' => true],
            ['variable_name' => 'coupon_discount_row', 'variable_description' => 'Coupon discount row HTML (if applicable)', 'example_value' => '<tr>...</tr>', 'is_required' => false],
            ['variable_name' => 'shipping_cost_row', 'variable_description' => 'Shipping cost row HTML (if applicable)', 'example_value' => '<tr>...</tr>', 'is_required' => false],
            ['variable_name' => 'stripe_fee_row', 'variable_description' => 'Stripe fee row HTML (if applicable)', 'example_value' => '<tr>...</tr>', 'is_required' => false],
            ['variable_name' => 'platform_fee_row', 'variable_description' => 'Platform fee row HTML (if applicable)', 'example_value' => '<tr>...</tr>', 'is_required' => false],
            ['variable_name' => 'total', 'variable_description' => 'Order total', 'example_value' => '125.00', 'is_required' => true],
            ['variable_name' => 'shipping_name', 'variable_description' => 'Shipping recipient name', 'example_value' => 'John Doe', 'is_required' => true],
            ['variable_name' => 'shipping_address_line1', 'variable_description' => 'Shipping address line 1', 'example_value' => '123 Main St', 'is_required' => true],
            ['variable_name' => 'shipping_address_line2_break', 'variable_description' => 'Shipping address line 2 with break (if applicable)', 'example_value' => 'Apt 4B<br>', 'is_required' => false],
            ['variable_name' => 'shipping_city', 'variable_description' => 'Shipping city', 'example_value' => 'Auckland', 'is_required' => true],
            ['variable_name' => 'shipping_state', 'variable_description' => 'Shipping state/region', 'example_value' => 'Auckland', 'is_required' => true],
            ['variable_name' => 'shipping_zip', 'variable_description' => 'Shipping zip code', 'example_value' => '1010', 'is_required' => true],
            ['variable_name' => 'shipping_country', 'variable_description' => 'Shipping country', 'example_value' => 'New Zealand', 'is_required' => true],
            ['variable_name' => 'billing_name', 'variable_description' => 'Billing recipient name', 'example_value' => 'John Doe', 'is_required' => true],
            ['variable_name' => 'billing_address_line1', 'variable_description' => 'Billing address line 1', 'example_value' => '123 Main St', 'is_required' => true],
            ['variable_name' => 'billing_address_line2_break', 'variable_description' => 'Billing address line 2 with break (if applicable)', 'example_value' => 'Apt 4B<br>', 'is_required' => false],
            ['variable_name' => 'billing_city', 'variable_description' => 'Billing city', 'example_value' => 'Auckland', 'is_required' => true],
            ['variable_name' => 'billing_state', 'variable_description' => 'Billing state/region', 'example_value' => 'Auckland', 'is_required' => true],
            ['variable_name' => 'billing_zip', 'variable_description' => 'Billing zip code', 'example_value' => '1010', 'is_required' => true],
            ['variable_name' => 'billing_country', 'variable_description' => 'Billing country', 'example_value' => 'New Zealand', 'is_required' => true],
            ['variable_name' => 'payment_method', 'variable_description' => 'Payment method', 'example_value' => 'Credit Card', 'is_required' => true],
            ['variable_name' => 'payment_card_info', 'variable_description' => 'Payment card information (if applicable)', 'example_value' => '<br>Card ending in 4242', 'is_required' => false],
            ['variable_name' => 'order_view_url', 'variable_description' => 'URL to view order details', 'example_value' => 'https://example.com/orders/123', 'is_required' => true],
        ];

        foreach ($variables as $var) {
            EmailTemplateVariable::firstOrCreate(
                [
                    'template_id' => $templateId,
                    'variable_name' => $var['variable_name'],
                ],
                [
                    'variable_description' => $var['variable_description'],
                    'example_value' => $var['example_value'],
                    'is_required' => $var['is_required'],
                ]
            );
        }
    }

    private function getOrderConfirmationTemplate(): string
    {
        return '<!-- Body Content -->
                    <tr>
                        <td style="padding: 40px 40px 20px 40px; background-color: #ffffff;">
                            <p style="margin: 0 0 20px 0; color: #000000; font-size: 16px; font-weight: 400;">
                                Hi {customer_name},
                            </p>
                            <p style="margin: 0 0 30px 0; color: #000000; font-size: 16px; font-weight: 400; line-height: 1.6;">
                                We\'ve received your order and it\'s being processed. You\'ll receive a shipping confirmation email once your order ships.
                            </p>
                            
                            <!-- Order Info Box -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f8f9fa; border-radius: 8px; margin-bottom: 30px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td style="padding-bottom: 12px;">
                                                    <p style="margin: 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Order Number
                                                    </p>
                                                    <p style="margin: 5px 0 0 0; color: #2850a3; font-size: 18px; font-weight: 700;">
                                                        #{order_number}
                                                    </p>
                                                </td>
                                                <td align="right" style="padding-bottom: 12px;">
                                                    <p style="margin: 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Order Date
                                                    </p>
                                                    <p style="margin: 5px 0 0 0; color: #000000; font-size: 18px; font-weight: 600;">
                                                        {order_date}
                                                    </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding-top: 12px; border-top: 1px solid #e9ecef;">
                                                    <p style="margin: 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Order Status
                                                    </p>
                                                    <p style="margin: 5px 0 0 0; color: #e95c67; font-size: 16px; font-weight: 600;">
                                                        {order_status}
                                                    </p>
                                                </td>
                                                <td align="right" style="padding-top: 12px; border-top: 1px solid #e9ecef;">
                                                    <p style="margin: 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Payment Status
                                                    </p>
                                                    <p style="margin: 5px 0 0 0; color: #e95c67; font-size: 16px; font-weight: 600;">
                                                        {payment_status}
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Order Items -->
                    <tr>
                        <td style="padding: 0 40px 30px 40px; background-color: #ffffff;">
                            <h2 style="margin: 0 0 20px 0; color: #2850a3; font-size: 20px; font-weight: 700; border-bottom: 2px solid #e9ecef; padding-bottom: 10px;">
                                Order Items
                            </h2>
                            
                            {order_items}
                        </td>
                    </tr>
                    
                    <!-- Order Totals -->
                    <tr>
                        <td style="padding: 0 40px 30px 40px; background-color: #ffffff;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f8f9fa; border-radius: 8px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td style="padding-bottom: 10px;">
                                                    <p style="margin: 0; color: #666666; font-size: 14px; font-weight: 400;">
                                                        Subtotal
                                                    </p>
                                                </td>
                                                <td align="right" style="padding-bottom: 10px;">
                                                    <p style="margin: 0; color: #000000; font-size: 14px; font-weight: 400;">
                                                        ${subtotal}
                                                    </p>
                                                </td>
                                            </tr>
                                            {coupon_discount_row}
                                            {shipping_cost_row}
                                            {stripe_fee_row}
                                            {platform_fee_row}
                                            <tr>
                                                <td style="padding-top: 15px; border-top: 2px solid #e9ecef;">
                                                    <p style="margin: 0; color: #2850a3; font-size: 18px; font-weight: 700;">
                                                        Total
                                                    </p>
                                                </td>
                                                <td align="right" style="padding-top: 15px; border-top: 2px solid #e9ecef;">
                                                    <p style="margin: 0; color: #e95c67; font-size: 24px; font-weight: 700;">
                                                        ${total}
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Shipping & Billing Address -->
                    <tr>
                        <td style="padding: 0 40px 30px 40px; background-color: #ffffff;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td width="50%" style="padding-right: 15px; vertical-align: top;">
                                        <h3 style="margin: 0 0 15px 0; color: #2850a3; font-size: 16px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Shipping Address
                                        </h3>
                                        <p style="margin: 0 0 5px 0; color: #000000; font-size: 14px; font-weight: 400; line-height: 1.6;">
                                            {shipping_name}<br>
                                            {shipping_address_line1}<br>
                                            {shipping_address_line2_break}
                                            {shipping_city}, {shipping_state} {shipping_zip}<br>
                                            {shipping_country}
                                        </p>
                                    </td>
                                    <td width="50%" style="padding-left: 15px; vertical-align: top;">
                                        <h3 style="margin: 0 0 15px 0; color: #2850a3; font-size: 16px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Billing Address
                                        </h3>
                                        <p style="margin: 0 0 5px 0; color: #000000; font-size: 14px; font-weight: 400; line-height: 1.6;">
                                            {billing_name}<br>
                                            {billing_address_line1}<br>
                                            {billing_address_line2_break}
                                            {billing_city}, {billing_state} {billing_zip}<br>
                                            {billing_country}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Payment Method -->
                    <tr>
                        <td style="padding: 0 40px 30px 40px; background-color: #ffffff;">
                            <h3 style="margin: 0 0 15px 0; color: #2850a3; font-size: 16px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                                Payment Method
                            </h3>
                            <p style="margin: 0; color: #000000; font-size: 14px; font-weight: 400;">
                                {payment_method}{payment_card_info}
                            </p>
                        </td>
                    </tr>
                    
                    <!-- View Order Button -->
                    <tr>
                        <td style="padding: 0 40px 30px 40px; text-align: center; background-color: #ffffff;">
                            <a href="{order_view_url}" style="display: inline-block; padding: 16px 40px; background-color: #e95c67; color: #ffffff; text-decoration: none; border-radius: 4px; font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                                VIEW ORDER DETAILS
                            </a>
                        </td>
                    </tr>';
    }

    private function seedOrderAdminNotificationVariables(int $templateId): void
    {
        $variables = [
            ['variable_name' => 'order_number', 'variable_description' => 'Order number', 'example_value' => 'ORD-20260121-ABC123', 'is_required' => true],
            ['variable_name' => 'order_date', 'variable_description' => 'Order date', 'example_value' => 'January 21, 2026', 'is_required' => true],
            ['variable_name' => 'order_status', 'variable_description' => 'Order status', 'example_value' => 'Pending', 'is_required' => true],
            ['variable_name' => 'payment_status', 'variable_description' => 'Payment status', 'example_value' => 'Paid', 'is_required' => true],
            ['variable_name' => 'customer_name', 'variable_description' => 'Customer full name', 'example_value' => 'John Doe', 'is_required' => true],
            ['variable_name' => 'customer_email', 'variable_description' => 'Customer email address', 'example_value' => 'john@example.com', 'is_required' => true],
            ['variable_name' => 'customer_phone_row', 'variable_description' => 'Customer phone row HTML (if applicable)', 'example_value' => '<p>...</p>', 'is_required' => false],
            ['variable_name' => 'order_items', 'variable_description' => 'HTML for all order items', 'example_value' => '<table>...</table>', 'is_required' => true],
            ['variable_name' => 'subtotal', 'variable_description' => 'Order subtotal', 'example_value' => '100.00', 'is_required' => true],
            ['variable_name' => 'coupon_discount_row', 'variable_description' => 'Coupon discount row HTML (if applicable)', 'example_value' => '<tr>...</tr>', 'is_required' => false],
            ['variable_name' => 'shipping_cost_row', 'variable_description' => 'Shipping cost row HTML (if applicable)', 'example_value' => '<tr>...</tr>', 'is_required' => false],
            ['variable_name' => 'stripe_fee_row', 'variable_description' => 'Stripe fee row HTML (if applicable)', 'example_value' => '<tr>...</tr>', 'is_required' => false],
            ['variable_name' => 'platform_fee_row', 'variable_description' => 'Platform fee row HTML (if applicable)', 'example_value' => '<tr>...</tr>', 'is_required' => false],
            ['variable_name' => 'total', 'variable_description' => 'Order total', 'example_value' => '125.00', 'is_required' => true],
            ['variable_name' => 'shipping_name', 'variable_description' => 'Shipping recipient name', 'example_value' => 'John Doe', 'is_required' => true],
            ['variable_name' => 'shipping_address_line1', 'variable_description' => 'Shipping address line 1', 'example_value' => '123 Main St', 'is_required' => true],
            ['variable_name' => 'shipping_address_line2_break', 'variable_description' => 'Shipping address line 2 with break (if applicable)', 'example_value' => 'Apt 4B<br>', 'is_required' => false],
            ['variable_name' => 'shipping_city', 'variable_description' => 'Shipping city', 'example_value' => 'Auckland', 'is_required' => true],
            ['variable_name' => 'shipping_state', 'variable_description' => 'Shipping state/region', 'example_value' => 'Auckland', 'is_required' => true],
            ['variable_name' => 'shipping_zip', 'variable_description' => 'Shipping zip code', 'example_value' => '1010', 'is_required' => true],
            ['variable_name' => 'shipping_country', 'variable_description' => 'Shipping country', 'example_value' => 'New Zealand', 'is_required' => true],
            ['variable_name' => 'billing_name', 'variable_description' => 'Billing recipient name', 'example_value' => 'John Doe', 'is_required' => true],
            ['variable_name' => 'billing_address_line1', 'variable_description' => 'Billing address line 1', 'example_value' => '123 Main St', 'is_required' => true],
            ['variable_name' => 'billing_address_line2_break', 'variable_description' => 'Billing address line 2 with break (if applicable)', 'example_value' => 'Apt 4B<br>', 'is_required' => false],
            ['variable_name' => 'billing_city', 'variable_description' => 'Billing city', 'example_value' => 'Auckland', 'is_required' => true],
            ['variable_name' => 'billing_state', 'variable_description' => 'Billing state/region', 'example_value' => 'Auckland', 'is_required' => true],
            ['variable_name' => 'billing_zip', 'variable_description' => 'Billing zip code', 'example_value' => '1010', 'is_required' => true],
            ['variable_name' => 'billing_country', 'variable_description' => 'Billing country', 'example_value' => 'New Zealand', 'is_required' => true],
            ['variable_name' => 'payment_method', 'variable_description' => 'Payment method', 'example_value' => 'Credit Card', 'is_required' => true],
            ['variable_name' => 'payment_card_info', 'variable_description' => 'Payment card information (if applicable)', 'example_value' => '<br>Card ending in 4242', 'is_required' => false],
            ['variable_name' => 'admin_order_view_url', 'variable_description' => 'URL to view order in admin panel', 'example_value' => 'https://example.com/admin/orders/123', 'is_required' => true],
        ];

        foreach ($variables as $var) {
            EmailTemplateVariable::firstOrCreate(
                [
                    'template_id' => $templateId,
                    'variable_name' => $var['variable_name'],
                ],
                [
                    'variable_description' => $var['variable_description'],
                    'example_value' => $var['example_value'],
                    'is_required' => $var['is_required'],
                ]
            );
        }
    }

    private function getOrderAdminNotificationTemplate(): string
    {
        return '<!-- Body Content -->
                    <tr>
                        <td style="padding: 40px 40px 20px 40px; background-color: #ffffff;">
                            <p style="margin: 0 0 20px 0; color: #000000; font-size: 16px; font-weight: 400;">
                                Hello Admin,
                            </p>
                            <p style="margin: 0 0 30px 0; color: #000000; font-size: 16px; font-weight: 400; line-height: 1.6;">
                                A new order has been placed and requires your attention. Please review the order details below:
                            </p>
                            
                            <!-- Order Info Box -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f8f9fa; border-radius: 8px; margin-bottom: 30px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td style="padding-bottom: 12px;">
                                                    <p style="margin: 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Order Number
                                                    </p>
                                                    <p style="margin: 5px 0 0 0; color: #2850a3; font-size: 18px; font-weight: 700;">
                                                        #{order_number}
                                                    </p>
                                                </td>
                                                <td align="right" style="padding-bottom: 12px;">
                                                    <p style="margin: 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Order Date
                                                    </p>
                                                    <p style="margin: 5px 0 0 0; color: #000000; font-size: 18px; font-weight: 600;">
                                                        {order_date}
                                                    </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding-top: 12px; border-top: 1px solid #e9ecef;">
                                                    <p style="margin: 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Order Status
                                                    </p>
                                                    <p style="margin: 5px 0 0 0; color: #e95c67; font-size: 16px; font-weight: 600;">
                                                        {order_status}
                                                    </p>
                                                </td>
                                                <td align="right" style="padding-top: 12px; border-top: 1px solid #e9ecef;">
                                                    <p style="margin: 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Payment Status
                                                    </p>
                                                    <p style="margin: 5px 0 0 0; color: #e95c67; font-size: 16px; font-weight: 600;">
                                                        {payment_status}
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Customer Information -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f8f9fa; border-radius: 8px; margin-bottom: 30px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <h3 style="margin: 0 0 15px 0; color: #2850a3; font-size: 16px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Customer Information
                                        </h3>
                                        <p style="margin: 0 0 8px 0; color: #000000; font-size: 16px; font-weight: 600;">
                                            {customer_name}
                                        </p>
                                        <p style="margin: 0 0 8px 0; color: #000000; font-size: 14px; font-weight: 400;">
                                            <a href="mailto:{customer_email}" style="color: #2850a3; text-decoration: none;">{customer_email}</a>
                                        </p>
                                        {customer_phone_row}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    
                    <tr>
                        <td style="padding: 0 40px 30px 40px; background-color: #ffffff;">
                            <h2 style="margin: 0 0 20px 0; color: #2850a3; font-size: 20px; font-weight: 700; border-bottom: 2px solid #e9ecef; padding-bottom: 10px;">
                                Order Items
                            </h2>
                            
                            {order_items}
                        </td>
                    </tr>
                    
                    
                    <tr>
                        <td style="padding: 0 40px 30px 40px; background-color: #ffffff;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f8f9fa; border-radius: 8px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td style="padding-bottom: 10px;">
                                                    <p style="margin: 0; color: #666666; font-size: 14px; font-weight: 400;">
                                                        Subtotal
                                                    </p>
                                                </td>
                                                <td align="right" style="padding-bottom: 10px;">
                                                    <p style="margin: 0; color: #000000; font-size: 14px; font-weight: 400;">
                                                        ${subtotal}
                                                    </p>
                                                </td>
                                            </tr>
                                            {coupon_discount_row}
                                            {shipping_cost_row}
                                            {stripe_fee_row}
                                            {platform_fee_row}
                                            <tr>
                                                <td style="padding-top: 15px; border-top: 2px solid #e9ecef;">
                                                    <p style="margin: 0; color: #2850a3; font-size: 18px; font-weight: 700;">
                                                        Total
                                                    </p>
                                                </td>
                                                <td align="right" style="padding-top: 15px; border-top: 2px solid #e9ecef;">
                                                    <p style="margin: 0; color: #e95c67; font-size: 24px; font-weight: 700;">
                                                        ${total}
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    
                    <tr>
                        <td style="padding: 0 40px 30px 40px; background-color: #ffffff;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td width="50%" style="padding-right: 15px; vertical-align: top;">
                                        <h3 style="margin: 0 0 15px 0; color: #2850a3; font-size: 16px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Shipping Address
                                        </h3>
                                        <p style="margin: 0 0 5px 0; color: #000000; font-size: 14px; font-weight: 400; line-height: 1.6;">
                                            {shipping_name}<br>
                                            {shipping_address_line1}<br>
                                            {shipping_address_line2_break}
                                            {shipping_city}, {shipping_state} {shipping_zip}<br>
                                            {shipping_country}
                                        </p>
                                    </td>
                                    <td width="50%" style="padding-left: 15px; vertical-align: top;">
                                        <h3 style="margin: 0 0 15px 0; color: #2850a3; font-size: 16px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Billing Address
                                        </h3>
                                        <p style="margin: 0 0 5px 0; color: #000000; font-size: 14px; font-weight: 400; line-height: 1.6;">
                                            {billing_name}<br>
                                            {billing_address_line1}<br>
                                            {billing_address_line2_break}
                                            {billing_city}, {billing_state} {billing_zip}<br>
                                            {billing_country}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    
                    <tr>
                        <td style="padding: 0 40px 30px 40px; background-color: #ffffff;">
                            <h3 style="margin: 0 0 15px 0; color: #2850a3; font-size: 16px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                                Payment Method
                            </h3>
                            <p style="margin: 0; color: #000000; font-size: 14px; font-weight: 400;">
                                {payment_method}{payment_card_info}
                            </p>
                        </td>
                    </tr>
                    
                    
                    <tr>
                        <td style="padding: 0 40px 30px 40px; text-align: center; background-color: #ffffff;">
                            <a href="{admin_order_view_url}" style="display: inline-block; padding: 16px 40px; background-color: #e95c67; color: #ffffff; text-decoration: none; border-radius: 4px; font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                                VIEW ORDER IN ADMIN PANEL
                            </a>
                        </td>
                    </tr>';
    }

    private function seedOrderProcessingVariables(int $templateId): void
    {
        $variables = [
            ['variable_name' => 'customer_name', 'variable_description' => 'Customer full name', 'example_value' => 'John Doe', 'is_required' => true],
            ['variable_name' => 'order_number', 'variable_description' => 'Order number', 'example_value' => 'ORD-20260121-ABC123', 'is_required' => true],
            ['variable_name' => 'processing_time', 'variable_description' => 'Expected processing time', 'example_value' => '2-3 business days', 'is_required' => true],
            ['variable_name' => 'order_status', 'variable_description' => 'Order status', 'example_value' => 'Processing', 'is_required' => true],
            ['variable_name' => 'order_view_url', 'variable_description' => 'URL to view order details', 'example_value' => 'https://example.com/orders/123', 'is_required' => true],
        ];

        foreach ($variables as $var) {
            EmailTemplateVariable::firstOrCreate(
                [
                    'template_id' => $templateId,
                    'variable_name' => $var['variable_name'],
                ],
                [
                    'variable_description' => $var['variable_description'],
                    'example_value' => $var['example_value'],
                    'is_required' => $var['is_required'],
                ]
            );
        }
    }

    private function getOrderProcessingTemplate(): string
    {
        return '<!-- Body Content -->
                    <tr>
                        <td style="padding: 40px 40px 30px 40px; background-color: #ffffff;">
                            <p style="margin: 0 0 20px 0; color: #000000; font-size: 16px; font-weight: 400;">
                                Hi {customer_name},
                            </p>
                            <p style="margin: 0 0 20px 0; color: #000000; font-size: 16px; font-weight: 400; line-height: 1.6;">
                                Great news! Your order #{order_number} is now being processed. Our team is preparing your items for shipment.
                            </p>
                            <p style="margin: 0 0 30px 0; color: #000000; font-size: 16px; font-weight: 400; line-height: 1.6;">
                                We\'ll send you another email with tracking information as soon as your order ships. Expected processing time: {processing_time}.
                            </p>
                            
                            <!-- Order Info Box -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f8f9fa; border-radius: 8px; margin-bottom: 30px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="margin: 0 0 10px 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Order Number
                                        </p>
                                        <p style="margin: 0 0 15px 0; color: #2850a3; font-size: 18px; font-weight: 700;">
                                            #{order_number}
                                        </p>
                                        <p style="margin: 0 0 10px 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Order Status
                                        </p>
                                        <p style="margin: 0; color: #e95c67; font-size: 16px; font-weight: 600;">
                                            {order_status}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- View Order Button -->
                    <tr>
                        <td style="padding: 0 40px 30px 40px; text-align: center; background-color: #ffffff;">
                            <a href="{order_view_url}" style="display: inline-block; padding: 16px 40px; background-color: #e95c67; color: #ffffff; text-decoration: none; border-radius: 4px; font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                                VIEW ORDER STATUS
                            </a>
                        </td>
                    </tr>';
    }

    private function seedOrderShippedVariables(int $templateId): void
    {
        $variables = [
            ['variable_name' => 'customer_name', 'variable_description' => 'Customer full name', 'example_value' => 'John Doe', 'is_required' => true],
            ['variable_name' => 'order_number', 'variable_description' => 'Order number', 'example_value' => 'ORD-20260121-ABC123', 'is_required' => true],
            ['variable_name' => 'tracking_number', 'variable_description' => 'Tracking number', 'example_value' => 'TRACK123456789', 'is_required' => true],
            ['variable_name' => 'tracking_url', 'variable_description' => 'Tracking URL', 'example_value' => 'https://tracking.example.com/TRACK123456789', 'is_required' => true],
            ['variable_name' => 'estimated_delivery_date', 'variable_description' => 'Estimated delivery date', 'example_value' => 'January 25, 2026', 'is_required' => true],
        ];

        foreach ($variables as $var) {
            EmailTemplateVariable::firstOrCreate(
                [
                    'template_id' => $templateId,
                    'variable_name' => $var['variable_name'],
                ],
                [
                    'variable_description' => $var['variable_description'],
                    'example_value' => $var['example_value'],
                    'is_required' => $var['is_required'],
                ]
            );
        }
    }

    private function getOrderShippedTemplate(): string
    {
        return '<!-- Body Content -->
                    <tr>
                        <td style="padding: 40px 40px 30px 40px; background-color: #ffffff;">
                            <p style="margin: 0 0 20px 0; color: #000000; font-size: 16px; font-weight: 400;">
                                Hi {customer_name},
                            </p>
                            <p style="margin: 0 0 20px 0; color: #000000; font-size: 16px; font-weight: 400; line-height: 1.6;">
                                Great news! Your order #{order_number} has been shipped and is on its way to you.
                            </p>
                            <p style="margin: 0 0 30px 0; color: #000000; font-size: 16px; font-weight: 400; line-height: 1.6;">
                                You can track your package using the tracking information below. Expected delivery: {estimated_delivery_date}.
                            </p>
                            
                            <!-- Tracking Info Box -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f8f9fa; border-radius: 8px; margin-bottom: 30px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="margin: 0 0 10px 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Tracking Number
                                        </p>
                                        <p style="margin: 0 0 20px 0; color: #2850a3; font-size: 18px; font-weight: 700; word-break: break-all;">
                                            {tracking_number}
                                        </p>
                                        <p style="margin: 0 0 10px 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Tracking Url
                                        </p>
                                        <p style="margin: 0 0 20px 0; color: #000000; font-size: 16px; font-weight: 600;">
                                            {tracking_url}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>';
    }

    private function seedOrderDeliveredVariables(int $templateId): void
    {
        $variables = [
            ['variable_name' => 'customer_name', 'variable_description' => 'Customer full name', 'example_value' => 'John Doe', 'is_required' => true],
            ['variable_name' => 'order_number', 'variable_description' => 'Order number', 'example_value' => 'ORD-20260121-ABC123', 'is_required' => true],
            ['variable_name' => 'delivery_date', 'variable_description' => 'Delivery date', 'example_value' => 'January 25, 2026', 'is_required' => true],
            ['variable_name' => 'review_url', 'variable_description' => 'URL to leave a review', 'example_value' => 'https://example.com/orders/123/review', 'is_required' => true],
        ];

        foreach ($variables as $var) {
            EmailTemplateVariable::firstOrCreate(
                [
                    'template_id' => $templateId,
                    'variable_name' => $var['variable_name'],
                ],
                [
                    'variable_description' => $var['variable_description'],
                    'example_value' => $var['example_value'],
                    'is_required' => $var['is_required'],
                ]
            );
        }
    }

    private function getOrderDeliveredTemplate(): string
    {
        return '<!-- Body Content -->
                    <tr>
                        <td style="padding: 40px 40px 30px 40px; background-color: #ffffff;">
                            <p style="margin: 0 0 20px 0; color: #000000; font-size: 16px; font-weight: 400;">
                                Hi {customer_name},
                            </p>
                            <p style="margin: 0 0 20px 0; color: #000000; font-size: 16px; font-weight: 400; line-height: 1.6;">
                                Great news! Your order #{order_number} has been successfully delivered on {delivery_date}.
                            </p>
                            <p style="margin: 0 0 30px 0; color: #000000; font-size: 16px; font-weight: 400; line-height: 1.6;">
                                We hope you\'re happy with your purchase! If you have any questions or concerns, please don\'t hesitate to contact us.
                            </p>
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f8f9fa; border-radius: 8px; margin-bottom: 30px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="margin: 0 0 10px 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Order Number
                                        </p>
                                        <p style="margin: 0 0 20px 0; color: #2850a3; font-size: 18px; font-weight: 700;">
                                            #{order_number}
                                        </p>
                                        <p style="margin: 0 0 10px 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Delivered On
                                        </p>
                                        <p style="margin: 0; color: #e95c67; font-size: 16px; font-weight: 600;">
                                            {delivery_date}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 0 40px 30px 40px; text-align: center; background-color: #ffffff;">
                            <a href="{review_url}" style="display: inline-block; padding: 16px 40px; background-color: #e95c67; color: #ffffff; text-decoration: none; border-radius: 4px; font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                                LEAVE A REVIEW
                            </a>
                        </td>
                    </tr>';
    }

    private function seedOrderCancelledVariables(int $templateId): void
    {
        $variables = [
            ['variable_name' => 'customer_name', 'variable_description' => 'Customer full name', 'example_value' => 'John Doe', 'is_required' => true],
            ['variable_name' => 'order_number', 'variable_description' => 'Order number', 'example_value' => 'ORD-20260121-ABC123', 'is_required' => true],
            ['variable_name' => 'order_status', 'variable_description' => 'Order status', 'example_value' => 'Cancelled', 'is_required' => true],
            ['variable_name' => 'refund_info_row', 'variable_description' => 'Refund information row HTML (if applicable)', 'example_value' => '<tr>...</tr>', 'is_required' => false],
            ['variable_name' => 'shop_url', 'variable_description' => 'URL to shop page', 'example_value' => 'https://example.com/shop', 'is_required' => true],
        ];

        foreach ($variables as $var) {
            EmailTemplateVariable::firstOrCreate(
                [
                    'template_id' => $templateId,
                    'variable_name' => $var['variable_name'],
                ],
                [
                    'variable_description' => $var['variable_description'],
                    'example_value' => $var['example_value'],
                    'is_required' => $var['is_required'],
                ]
            );
        }
    }

    private function getOrderCancelledTemplate(): string
    {
        return '<!-- Body Content -->
                    <tr>
                        <td style="padding: 40px 40px 30px 40px; background-color: #ffffff;">
                            <p style="margin: 0 0 20px 0; color: #000000; font-size: 16px; font-weight: 400;">
                                Hi {customer_name},
                            </p>
                            <p style="margin: 0 0 20px 0; color: #000000; font-size: 16px; font-weight: 400; line-height: 1.6;">
                                We\'re writing to inform you that your order #{order_number} has been cancelled.
                            </p>
                            <!-- Order Info Box -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f8f9fa; border-radius: 8px; margin-bottom: 30px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="margin: 0 0 10px 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Order Number
                                        </p>
                                        <p style="margin: 0 0 20px 0; color: #2850a3; font-size: 18px; font-weight: 700;">
                                            #{order_number}
                                        </p>
                                        <p style="margin: 0 0 10px 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Order Status
                                        </p>
                                        <p style="margin: 0; color: #e95c67; font-size: 16px; font-weight: 600;">
                                            {order_status}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    {refund_info_row}
                    <tr>
                        <td style="padding: 0 40px 30px 40px; text-align: center; background-color: #ffffff;">
                            <a href="{shop_url}" style="display: inline-block; padding: 16px 40px; background-color: #e95c67; color: #ffffff; text-decoration: none; border-radius: 4px; font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                                SHOP AGAIN
                            </a>
                        </td>
                    </tr>';
    }

    private function seedReviewNotificationVariables(int $templateId): void
    {
        $variables = [
            ['variable_name' => 'reviewer_name', 'variable_description' => 'Reviewer name', 'example_value' => 'John Doe', 'is_required' => true],
            ['variable_name' => 'product_name', 'variable_description' => 'Product name', 'example_value' => 'Product Name', 'is_required' => true],
            ['variable_name' => 'rating', 'variable_description' => 'Rating value (1-5)', 'example_value' => '5', 'is_required' => true],
            ['variable_name' => 'rating_stars', 'variable_description' => 'Rating stars display', 'example_value' => '★★★★★', 'is_required' => true],
            ['variable_name' => 'review_text', 'variable_description' => 'Review text content', 'example_value' => 'Great product!', 'is_required' => true],
            ['variable_name' => 'product_url', 'variable_description' => 'URL to product page', 'example_value' => 'https://example.com/products/product-slug', 'is_required' => true],
        ];

        foreach ($variables as $var) {
            EmailTemplateVariable::firstOrCreate(
                [
                    'template_id' => $templateId,
                    'variable_name' => $var['variable_name'],
                ],
                [
                    'variable_description' => $var['variable_description'],
                    'example_value' => $var['example_value'],
                    'is_required' => $var['is_required'],
                ]
            );
        }
    }

    private function getReviewNotificationTemplate(): string
    {
        return '<!-- Body Content -->
                    <tr>
                        <td style="padding: 40px 40px 20px 40px; background-color: #ffffff;">
                            <p style="margin: 0 0 20px 0; color: #000000; font-size: 16px; font-weight: 400;">
                                Hi {reviewer_name},
                            </p>
                            <p style="margin: 0 0 30px 0; color: #000000; font-size: 16px; font-weight: 400; line-height: 1.6;">
                                Thank you for taking the time to review <strong>{product_name}</strong>. Your review has been submitted and is pending admin approval. Once approved, it will be published on our website.
                            </p>
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f8f9fa; border-radius: 8px; margin-bottom: 30px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="margin: 0 0 12px 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Product
                                        </p>
                                        <p style="margin: 0 0 20px 0; color: #2850a3; font-size: 18px; font-weight: 700;">
                                            {product_name}
                                        </p>
                                        <p style="margin: 0 0 12px 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Your Rating
                                        </p>
                                        <p style="margin: 0 0 20px 0; color: #ff9800; font-size: 18px; font-weight: 700;">
                                            {rating_stars} ({rating}/5)
                                        </p>
                                        <p style="margin: 0 0 12px 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Your Review
                                        </p>
                                        <p style="margin: 0; color: #000000; font-size: 14px; font-weight: 400; line-height: 1.6; white-space: pre-wrap;">
                                            {review_text}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td align="center" style="padding: 0 0 30px 0;">
                                        <a href="{product_url}" style="display: inline-block; padding: 16px 40px; background-color: #e95c67; color: #ffffff; text-decoration: none; border-radius: 4px; font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                                            VIEW PRODUCT
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>';
    }

    private function seedReviewAdminNotificationVariables(int $templateId): void
    {
        $variables = [
            ['variable_name' => 'product_name', 'variable_description' => 'Product name', 'example_value' => 'Product Name', 'is_required' => true],
            ['variable_name' => 'reviewer_name', 'variable_description' => 'Reviewer name', 'example_value' => 'John Doe', 'is_required' => true],
            ['variable_name' => 'reviewer_email', 'variable_description' => 'Reviewer email address', 'example_value' => 'john@example.com', 'is_required' => true],
            ['variable_name' => 'rating', 'variable_description' => 'Rating value (1-5)', 'example_value' => '5', 'is_required' => true],
            ['variable_name' => 'rating_stars', 'variable_description' => 'Rating stars display', 'example_value' => '★★★★★', 'is_required' => true],
            ['variable_name' => 'review_text', 'variable_description' => 'Review text content', 'example_value' => 'Great product!', 'is_required' => true],
            ['variable_name' => 'submission_date', 'variable_description' => 'Review submission date', 'example_value' => 'January 25, 2026', 'is_required' => true],
            ['variable_name' => 'admin_review_view_url', 'variable_description' => 'URL to view reviews in admin panel', 'example_value' => 'https://example.com/admin/reviews', 'is_required' => true],
        ];

        foreach ($variables as $var) {
            EmailTemplateVariable::firstOrCreate(
                [
                    'template_id' => $templateId,
                    'variable_name' => $var['variable_name'],
                ],
                [
                    'variable_description' => $var['variable_description'],
                    'example_value' => $var['example_value'],
                    'is_required' => $var['is_required'],
                ]
            );
        }
    }

    private function getReviewAdminNotificationTemplate(): string
    {
        return '<!-- Body Content -->
                    <tr>
                        <td style="padding: 40px 40px 20px 40px; background-color: #ffffff;">
                            <p style="margin: 0 0 20px 0; color: #000000; font-size: 16px; font-weight: 400;">
                                Hello Admin,
                            </p>
                            <p style="margin: 0 0 30px 0; color: #000000; font-size: 16px; font-weight: 400; line-height: 1.6;">
                                A new product review has been submitted and is pending your approval:
                            </p>
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f8f9fa; border-radius: 8px; margin-bottom: 30px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td style="padding-bottom: 15px;">
                                                    <p style="margin: 0 0 5px 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Product
                                                    </p>
                                                    <p style="margin: 0; color: #2850a3; font-size: 18px; font-weight: 700;">
                                                        {product_name}
                                                    </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding-bottom: 15px; border-top: 1px solid #e9ecef; padding-top: 15px;">
                                                    <p style="margin: 0 0 5px 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Reviewer
                                                    </p>
                                                    <p style="margin: 0; color: #000000; font-size: 16px; font-weight: 600;">
                                                        {reviewer_name}
                                                    </p>
                                                    <p style="margin: 5px 0 0 0; color: #2850a3; font-size: 14px; font-weight: 400;">
                                                        <a href="mailto:{reviewer_email}" style="color: #2850a3; text-decoration: none;">{reviewer_email}</a>
                                                    </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding-bottom: 15px; border-top: 1px solid #e9ecef; padding-top: 15px;">
                                                    <p style="margin: 0 0 5px 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Rating
                                                    </p>
                                                    <p style="margin: 0; color: #ff9800; font-size: 18px; font-weight: 700;">
                                                        {rating_stars} ({rating}/5)
                                                    </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding-bottom: 15px; border-top: 1px solid #e9ecef; padding-top: 15px;">
                                                    <p style="margin: 0 0 5px 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Review
                                                    </p>
                                                    <p style="margin: 0; color: #000000; font-size: 14px; font-weight: 400; line-height: 1.6; white-space: pre-wrap;">
                                                        {review_text}
                                                    </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding-top: 15px; border-top: 1px solid #e9ecef;">
                                                    <p style="margin: 0 0 5px 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Submitted On
                                                    </p>
                                                    <p style="margin: 0; color: #000000; font-size: 14px; font-weight: 400;">
                                                        {submission_date}
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 0 40px 30px 40px; text-align: center; background-color: #ffffff;">
                            <a href="{admin_review_view_url}" style="display: inline-block; padding: 16px 40px; background-color: #e95c67; color: #ffffff; text-decoration: none; border-radius: 4px; font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                                VIEW REVIEWS IN ADMIN PANEL
                            </a>
                        </td>
                    </tr>';
    }
}
