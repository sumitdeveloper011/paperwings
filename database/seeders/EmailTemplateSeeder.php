<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmailTemplate;
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
                'name' => 'Order Delivered',
                'slug' => 'order_delivered',
                'subject' => 'Your Order #{order_number} Has Been Delivered',
                'body' => $this->getOrderDeliveredTemplate(),
                'description' => 'Sent to customers when an order is delivered',
                'category' => 'order',
                'is_active' => true,
                'created_by' => $userId,
                'updated_by' => $userId,
            ],
            [
                'name' => 'Order Cancelled',
                'slug' => 'order_cancelled',
                'subject' => 'Order #{order_number} Has Been Cancelled',
                'body' => $this->getOrderCancelledTemplate(),
                'description' => 'Sent to customers when an order is cancelled',
                'category' => 'order',
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
                'name' => 'Contact Status Update',
                'slug' => 'contact_status_update',
                'subject' => 'Your Contact Message Status Updated - {app_name}',
                'body' => $this->getContactStatusUpdateTemplate(),
                'description' => 'Sent to customers when their contact message status is updated to solved',
                'category' => 'user',
                'is_active' => true,
                'created_by' => $userId,
                'updated_by' => $userId,
            ],
            [
                'name' => 'Order Processing',
                'slug' => 'order_processing',
                'subject' => 'Your Order #{order_number} is Being Processed',
                'body' => $this->getOrderProcessingTemplate(),
                'description' => 'Sent to customers when an order status changes to processing',
                'category' => 'order',
                'is_active' => true,
                'created_by' => $userId,
                'updated_by' => $userId,
            ],
            [
                'name' => 'Order Shipped',
                'slug' => 'order_shipped',
                'subject' => 'Your Order #{order_number} Has Been Shipped',
                'body' => $this->getOrderShippedTemplate(),
                'description' => 'Sent to customers when an order is shipped with tracking information',
                'category' => 'order',
                'is_active' => true,
                'created_by' => $userId,
                'updated_by' => $userId,
            ],
        ];

        foreach ($templates as $templateData) {
            EmailTemplate::firstOrCreate(
                ['slug' => $templateData['slug']],
                array_merge($templateData, ['uuid' => Str::uuid()])
            );
        }

        $this->command->info('✓ Seeded ' . count($templates) . ' email templates');
    }

    private function getOrderConfirmationTemplate(): string
    {
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Order Confirmation</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden;">
                    <tr>
                        <td style="padding: 40px; text-align: center; background-color: #374E94;">
                            <h1 style="color: #ffffff; margin: 0;">Order Confirmation</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 40px;">
                            <p>Hi {customer_name},</p>
                            <p>Thank you for your order! Your order #{order_number} has been confirmed.</p>
                            <p><strong>Order Total:</strong> {order_total}</p>
                            <p>We will send you another email when your order ships.</p>
                            <p>Best regards,<br>{app_name}</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';
    }

    private function getOrderDeliveredTemplate(): string
    {
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Order Delivered</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden;">
                    <tr>
                        <td style="padding: 40px; text-align: center; background-color: #28a745;">
                            <h1 style="color: #ffffff; margin: 0;">Order Delivered</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 40px;">
                            <p>Hi {customer_name},</p>
                            <p>Great news! Your order #{order_number} has been delivered.</p>
                            <p>We hope you enjoy your purchase. If you have any questions, please contact us.</p>
                            <p>Best regards,<br>{app_name}</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';
    }

    private function getOrderCancelledTemplate(): string
    {
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Order Cancelled</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden;">
                    <tr>
                        <td style="padding: 40px; text-align: center; background-color: #dc3545;">
                            <h1 style="color: #ffffff; margin: 0;">Order Cancelled</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 40px;">
                            <p>Hi {customer_name},</p>
                            <p>Your order #{order_number} has been cancelled.</p>
                            <p>If you have any questions, please contact us.</p>
                            <p>Best regards,<br>{app_name}</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';
    }

    private function getContactNotificationTemplate(): string
    {
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Contact Confirmation</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden;">
                    <tr>
                        <td style="padding: 40px; text-align: center; background-color: #374E94;">
                            <h1 style="color: #ffffff; margin: 0;">Thank You for Contacting Us</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 40px;">
                            <p>Hi {customer_name},</p>
                            <p>We have received your message and will get back to you as soon as possible.</p>
                            <p><strong>Subject:</strong> {message_subject}</p>
                            <p><strong>Your Message:</strong></p>
                            <p>{message_content}</p>
                            <p>Best regards,<br>{app_name}</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';
    }

    private function getWelcomeEmailTemplate(): string
    {
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden;">
                    <tr>
                        <td style="padding: 40px; text-align: center; background-color: #374E94;">
                            <h1 style="color: #ffffff; margin: 0;">Welcome to {app_name}!</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 40px;">
                            <p>Hi {customer_name},</p>
                            <p>Welcome to {app_name}! We are excited to have you on board.</p>
                            <p>Start shopping and discover our amazing products.</p>
                            <p>Best regards,<br>{app_name} Team</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';
    }

    private function getPasswordResetTemplate(): string
    {
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden;">
                    <tr>
                        <td style="padding: 40px; text-align: center; background-color: #374E94;">
                            <h1 style="color: #ffffff; margin: 0;">Reset Your Password</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 40px;">
                            <p>Hi {customer_name},</p>
                            <p>You requested to reset your password. Click the link below to reset it:</p>
                            <p><a href="{reset_link}" style="background-color: #374E94; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 4px; display: inline-block;">Reset Password</a></p>
                            <p>This link will expire in {expiry_time}.</p>
                            <p>If you did not request this, please ignore this email.</p>
                            <p>Best regards,<br>{app_name}</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';
    }

    private function getEmailVerificationTemplate(): string
    {
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Verify Email</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden;">
                    <tr>
                        <td style="padding: 40px; text-align: center; background-color: #374E94;">
                            <h1 style="color: #ffffff; margin: 0;">Verify Your Email</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 40px;">
                            <p>Hi {customer_name},</p>
                            <p>Please verify your email address by clicking the link below:</p>
                            <p><a href="{verification_link}" style="background-color: #374E94; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 4px; display: inline-block;">Verify Email</a></p>
                            <p>If you did not create an account, please ignore this email.</p>
                            <p>Best regards,<br>{app_name}</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
            </table>
</body>
</html>';
    }

    private function getContactStatusUpdateTemplate(): string
    {
        return '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Contact Message Status Update</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, \'Helvetica Neue\', Arial, sans-serif; background-color: #ffffff; line-height: 1.6;">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #ffffff;">
        <tr>
            <td align="center">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="max-width: 600px; width: 100%; background-color: #ffffff;">
                    <!-- Top Bar - Logo -->
                    <tr>
                        <td style="padding: 20px 40px; text-align: center; background-color: #ffffff;">
                            <img src="{logo_url}" alt="Company Logo" style="max-width: 180px; height: auto; display: block; margin: 0 auto;" />
                        </td>
                    </tr>

                    <!-- Header Banner - Dark Blue -->
                    <tr>
                        <td style="padding: 50px 40px; text-align: center; background-color: #374E94;">
                            <p style="margin: 0 0 15px 0; color: #ffffff; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">
                                STATUS UPDATE
                            </p>
                            <h1 style="margin: 0; color: #ffffff; font-size: 32px; font-weight: 700; line-height: 1.2;">
                                Your Message Status Has Been Updated
                            </h1>
                        </td>
                    </tr>

                    <!-- Message Content -->
                    <tr>
                        <td style="padding: 40px 40px 20px 40px; background-color: #ffffff;">
                            <p style="margin: 0 0 20px 0; color: #000000; font-size: 16px; font-weight: 400;">
                                Hi {customer_name},
                            </p>
                            <p style="margin: 0 0 30px 0; color: #000000; font-size: 16px; font-weight: 400; line-height: 1.6;">
                                We wanted to inform you that the status of your contact message has been updated.
                            </p>

                            <!-- Message Details Box -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f8f9fa; border-radius: 8px; margin-bottom: 30px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="margin: 0 0 12px 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Subject
                                        </p>
                                        <p style="margin: 0 0 20px 0; color: #374E94; font-size: 18px; font-weight: 700;">
                                            {message_subject}
                                        </p>
                                        
                                        <!-- Status Update -->
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 20px;">
                                            <tr>
                                                <td style="padding: 15px; background-color: #ffffff; border-radius: 6px; border-left: 4px solid #374E94;">
                                                    <p style="margin: 0 0 8px 0; color: #666666; font-size: 12px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Previous Status
                                                    </p>
                                                    <p style="margin: 0 0 15px 0; color: #666666; font-size: 16px; font-weight: 600;">
                                                        {old_status}
                                                    </p>
                                                    <p style="margin: 0 0 8px 0; color: #666666; font-size: 12px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        New Status
                                                    </p>
                                                    <p style="margin: 0; color: #28a745; font-size: 18px; font-weight: 700;">
                                                        {new_status}
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>

                                        <p style="margin: 0 0 15px 0; color: #28a745; font-size: 14px; font-weight: 600; text-align: center; padding: 12px; background-color: #d4edda; border-radius: 6px;">
                                            ✓ Your inquiry has been resolved!
                                        </p>

                                        {admin_notes}
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0 0 20px 0; color: #000000; font-size: 16px; font-weight: 400; line-height: 1.6;">
                                If you have any questions or need further assistance, please feel free to contact us:
                            </p>
                            <p style="margin: 0 0 5px 0; color: #374E94; font-size: 16px; font-weight: 600;">
                                Phone: {contact_phone}
                            </p>
                            <p style="margin: 0 0 30px 0; color: #374E94; font-size: 16px; font-weight: 600;">
                                Email: {contact_email}
                            </p>
                        </td>
                    </tr>

                    <!-- Footer - Contact Information -->
                    <tr>
                        <td style="padding: 40px 40px 30px 40px; background-color: #f5f5f5;">
                            <p style="margin: 0 0 15px 0; color: #374E94; font-size: 16px; font-weight: 600; text-align: center;">
                                Get in touch
                            </p>
                            <p style="margin: 0 0 8px 0; color: #000000; font-size: 14px; font-weight: 400; text-align: center;">
                                {contact_phone}
                            </p>
                            <p style="margin: 0 0 25px 0; color: #000000; font-size: 14px; font-weight: 400; text-align: center;">
                                {contact_email}
                            </p>
                        </td>
                    </tr>

                    <!-- Copyright Bar - Dark Blue -->
                    <tr>
                        <td style="padding: 20px 40px; text-align: center; background-color: #374E94;">
                            <p style="margin: 0; color: #ffffff; font-size: 12px; font-weight: 400;">
                                Copyrights © {current_year} {app_name} All Rights Reserved
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';
    }

    private function getOrderProcessingTemplate(): string
    {
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Order Processing</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden;">
                    <tr>
                        <td style="padding: 40px; text-align: center; background-color: #ffc107;">
                            <h1 style="color: #ffffff; margin: 0;">Order Processing</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 40px;">
                            <p>Hi {customer_name},</p>
                            <p>Great news! Your order #{order_number} is now being processed.</p>
                            <p><strong>Order Total:</strong> {order_total}</p>
                            <p>We are preparing your items for shipment. You will receive another email with tracking information once your order ships.</p>
                            <p>If you have any questions, please contact us.</p>
                            <p>Best regards,<br>{app_name}</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';
    }

    private function getOrderShippedTemplate(): string
    {
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Order Shipped</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden;">
                    <tr>
                        <td style="padding: 40px; text-align: center; background-color: #17a2b8;">
                            <h1 style="color: #ffffff; margin: 0;">Order Shipped</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 40px;">
                            <p>Hi {customer_name},</p>
                            <p>Your order #{order_number} has been shipped!</p>
                            <p><strong>Tracking Number:</strong> {tracking_id}</p>
                            <p><a href="{tracking_url}" style="background-color: #17a2b8; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 4px; display: inline-block;">Track Your Order</a></p>
                            <p>You can track your package using the information above. Your order should arrive soon!</p>
                            <p>If you have any questions, please contact us.</p>
                            <p>Best regards,<br>{app_name}</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';
    }
}
