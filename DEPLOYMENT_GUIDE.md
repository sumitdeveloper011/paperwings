# PaperWings - Deployment Guide (ZIP Upload Method)

## 🔐 **REQUIRED .ENV KEYS**

### **Core Settings**
```bash
APP_NAME="PaperWings"
APP_ENV=production
APP_KEY=base64:GENERATE_WITH_php_artisan_key:generate
APP_DEBUG=false
APP_URL=https://yourdomain.com
ASSET_VERSION=1.0.0
```

### **Database**
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_db_name
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
```

### **Cache & Session**
```bash
CACHE_STORE=file
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true
FILESYSTEM_DISK=public
```

### **Queue & Logs**
```bash
QUEUE_CONNECTION=database
LOG_CHANNEL=stack
LOG_LEVEL=error
```

### **Mail (SMTP)**
```bash
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_smtp_user
MAIL_PASSWORD=your_smtp_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

### **Stripe Payment** ⚠️ **REQUIRED**
```bash
STRIPE_KEY=pk_live_xxxxxxxxxxxxxxxxxxxxx
STRIPE_SECRET=sk_live_xxxxxxxxxxxxxxxxxxxxx
STRIPE_WEBHOOK_SECRET=whsec_xxxxxxxxxxxxxxxxxxxxx
```

### **Optional APIs**
```bash
# EposNow (Inventory)
EPOSNOW_API_KEY=your_key
EPOSNOW_API_SECRET=your_secret
EPOSNOW_API_BASE=https://api.eposnow.com

# NZ Post (Address Validation)
NZPOST_API_KEY=your_key
NZPOST_API_URL=https://api.nzpost.co.nz/addresschecker/v1

# Google OAuth
GOOGLE_CLIENT_ID=your_client_id
GOOGLE_CLIENT_SECRET=your_client_secret
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"

# Facebook OAuth
FACEBOOK_CLIENT_ID=your_app_id
FACEBOOK_CLIENT_SECRET=your_app_secret
FACEBOOK_REDIRECT_URI="${APP_URL}/auth/facebook/callback"
```

---

## 🚀 **DEPLOYMENT STEPS**

### **STEP 1: Prepare Locally**
1. Install dependencies:
   ```bash
   composer install --no-dev --optimize-autoloader
   npm install
   npm run build
   ```

2. Create `.env` file with production settings (use values above)

3. Create ZIP file:
   - Include: All project files + `vendor/` + built assets
   - Exclude: `node_modules/`, `.git/`, `.env` (upload separately)

---

### **STEP 2: Upload to Server**

1. **Extract ZIP** in cPanel File Manager to your directory (e.g., `public_html/`)

2. **Upload .env separately** via File Manager → Create file → Paste your production .env content

3. **Set Permissions** via File Manager:
   ```
   storage/ → 755
   bootstrap/cache/ → 755
   ```

---

### **STEP 3: Configure Server**

#### **A. Set Document Root**
cPanel → Domains → Change document root to: `/public_html/public`

#### **B. Set PHP Version**
cPanel → Select PHP Version → Choose PHP 8.2+

#### **C. Update PHP Settings**
cPanel → MultiPHP INI Editor → Add:
```ini
upload_max_filesize = 20M
post_max_size = 25M
max_execution_time = 300
memory_limit = 256M
```

---

### **STEP 4: Database Setup**

1. **Create database** in cPanel → MySQL Databases
2. **Create user** with all privileges
3. **Update .env** with database credentials
4. **Run migrations** via Terminal or create `migrate.php` in public/:
   ```php
   <?php
   require __DIR__.'/../vendor/autoload.php';
   $app = require_once __DIR__.'/../bootstrap/app.php';
   Artisan::call('migrate', ['--force' => true]);
   echo "Migrations complete!";
   // DELETE THIS FILE AFTER RUNNING
   ```
   Visit: `yourdomain.com/migrate.php` then delete file

---

### **STEP 5: Run Optimization**

Create `optimize.php` in public/:
```php
<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

Artisan::call('storage:link');
Artisan::call('config:cache');
Artisan::call('route:cache');
Artisan::call('view:cache');

echo "Optimization complete!";
// DELETE THIS FILE AFTER RUNNING
```

Visit: `yourdomain.com/optimize.php` then **delete file immediately**

---

### **STEP 6: Setup Cron Jobs**

cPanel → Cron Jobs → Add two cron jobs:

**1. Laravel Scheduler (every minute):**
```bash
* * * * * cd /home/username/public_html && php artisan schedule:run >> /dev/null 2>&1
```

**2. Queue Worker (every minute):**
```bash
* * * * * cd /home/username/public_html && php artisan queue:work --stop-when-empty --max-time=50 >> /dev/null 2>&1
```

Replace `/home/username/public_html` with your actual path

---

### **STEP 7: SSL Setup**

1. **Install SSL**: cPanel → SSL/TLS → Let's Encrypt (AutoSSL) - Free
2. **Enable Force HTTPS**: cPanel → Domains → Force HTTPS Redirect

Or manually add to `public/.htaccess` (at the top):
```apache
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

3. **Update .env**:
   ```bash
   APP_URL=https://yourdomain.com
   SESSION_SECURE_COOKIE=true
   ```

---

### **STEP 8: Test Everything**

- [ ] Homepage loads
- [ ] User registration + email verification
- [ ] User login
- [ ] Product browsing
- [ ] Add to cart
- [ ] Checkout + Stripe payment (test with small amount)
- [ ] Order confirmation email
- [ ] Admin panel login
- [ ] File uploads work
- [ ] Search works
- [ ] Contact form works

---

## 🔧 **ESSENTIAL COMMANDS**

If you have SSH access, run these after deployment:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
```

---

## ⚠️ **IMPORTANT**

1. ❌ NEVER set `APP_DEBUG=true` in production
2. ❌ NEVER commit `.env` file
3. ✅ ALWAYS use HTTPS (required for Stripe)
4. ✅ ALWAYS backup database before updates
5. ✅ DELETE `migrate.php` and `optimize.php` after use

---

## 🆘 **TROUBLESHOOTING**

### **500 Error**
Check `storage/logs/laravel.log` via File Manager

Fixes:
```bash
chmod -R 755 storage bootstrap/cache
php artisan config:clear
php artisan cache:clear
```

### **Images Not Showing**
Create symlink via File Manager or run:
```bash
php artisan storage:link
```

### **Emails Not Sending**
- Verify SMTP credentials in .env
- Check `storage/logs/laravel.log`
- Test with: `php artisan tinker` then `Mail::raw('Test', fn($m) => $m->to('you@email.com')->subject('Test'));`

### **Blank Page After Upload**
- Check PHP version is 8.2+
- Verify `.htaccess` exists in public/
- Check file permissions: storage/ and bootstrap/cache/ must be 755

---

## 📞 **SUPPORT**

- Laravel Docs: https://laravel.com/docs/12.x
- Stripe Docs: https://stripe.com/docs

---

**Version**: 2.1.0 (ZIP Upload Edition)
