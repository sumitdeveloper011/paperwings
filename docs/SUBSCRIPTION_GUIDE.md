# Subscription Guide

## How Users Can Subscribe

Users can subscribe to the newsletter in the following ways:

### 1. Home Page Subscription Form

**Location**: Home page bottom section (Subscription Banner)

**How it works**:
- User enters their email address in the subscription form
- Clicks the "Subscribe" button
- System validates the email format
- If email is already subscribed (status = 1), shows error message
- If email was previously unsubscribed (status = 0), reactivates the subscription
- If email is new, creates a new subscription record
- Shows success toast notification: "Thank you for subscribing! You will receive our latest offers and updates."

**Technical Details**:
- **Route**: `POST /subscription`
- **Controller**: `App\Http\Controllers\Frontend\SubscriptionController@store`
- **Validation**: Email is required, must be valid format, max 255 characters
- **Database**: Stores in `subscriptions` table with:
  - `email` (lowercased and trimmed)
  - `status` = 1 (active)
  - `subscribed_at` = current timestamp
  - `uuid` (auto-generated for unsubscribe links)

### 2. Subscription Status

**Active Subscription** (`status = 1`):
- User receives newsletter emails
- Email appears in admin subscription list as "Active"
- Can be used for sending newsletters

**Inactive Subscription** (`status = 0`):
- User has unsubscribed
- Email appears in admin subscription list as "Inactive"
- Will not receive newsletter emails
- If same email subscribes again, it will be reactivated

### 3. Unsubscribe Process

**How users unsubscribe**:
- Users receive newsletter emails with unsubscribe link
- Unsubscribe link format: `/subscription/unsubscribe/{uuid}`
- Clicking the link sets `status = 0` and `unsubscribed_at = current timestamp`
- Shows success message: "You have been successfully unsubscribed from our newsletter."

**Technical Details**:
- **Route**: `POST /subscription/unsubscribe/{uuid}`
- **Controller**: `App\Http\Controllers\Frontend\SubscriptionController@unsubscribe`
- Uses UUID to identify subscription (more secure than ID)

### 4. Admin Management

**View Subscriptions**:
- Admin can view all subscriptions at `/admin/subscriptions`
- Can search by email address
- Can view individual subscription details
- Can delete subscriptions

**Send Newsletter**:
- Admin can send newsletters to all active subscribers
- Access via "Send Newsletter" button in subscription list
- Uses `SendNewsletterJob` queue job for bulk email sending

### 5. Frontend Features

**Toast Notifications**:
- Success: Green toast with success message
- Error: Red toast with error message
- Auto-dismisses after 5 seconds

**Form Validation**:
- Real-time email format validation
- Shows error if email is invalid
- Prevents duplicate subscriptions (shows error if already subscribed)

**Loading States**:
- Button shows spinner during submission
- Button text changes to "Subscribing..."
- Button is disabled during submission

### 6. Database Schema

**subscriptions table**:
- `id` - Primary key
- `uuid` - Unique identifier for unsubscribe links
- `email` - User email address (unique, indexed)
- `status` - 1 = Active, 0 = Inactive
- `subscribed_at` - Timestamp when subscribed
- `unsubscribed_at` - Timestamp when unsubscribed (nullable)
- `created_at` - Record creation timestamp
- `updated_at` - Record update timestamp

### 7. Analytics Integration

**Google Analytics Tracking**:
- Tracks newsletter subscription events
- Event name: `newsletter_subscribe`
- Parameters:
  - `method`: "homepage"
  - `user_id`: Authenticated user ID (if logged in)

### 8. Error Handling

**Common Errors**:
- **Email already subscribed**: "This email is already subscribed to our newsletter."
- **Invalid email format**: "Please enter a valid email address."
- **Server error**: "An error occurred. Please try again later."

**Error Response Codes**:
- `422` - Validation error (invalid email format)
- `409` - Conflict (email already subscribed)
- `500` - Server error

### 9. Best Practices

**For Users**:
- Use a valid email address
- Check spam folder for confirmation emails
- Use unsubscribe link if no longer want emails

**For Admins**:
- Regularly clean inactive subscriptions
- Monitor subscription growth
- Send relevant and valuable content
- Respect unsubscribe requests immediately
