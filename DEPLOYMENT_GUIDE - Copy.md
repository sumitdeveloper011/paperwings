# PaperWings E-Commerce - Deployment Guide

## 📋 **PROJECT SUMMARY**

### **Application Type**
Laravel 12 E-Commerce Platform (PHP 8.2+)

### **Hosting Environment**
- **Deployment Type**: Shared Hosting (cPanel/Plesk compatible)
- **No Redis/Memcached**: File-based cache only
- **No Supervisor**: Queue processing via cron jobs

### **Requirements**
- PHP 8.2+, MySQL 5.7+, Composer, SSL Certificate
- Stripe account (payment processing)
- SMTP server (email sending)

---

## 🔐 **REQUIRED .ENV KEYS**

### **1. Core Application Settings**
```bash
APP_NAME="PaperWings"
APP_ENV=production
APP_KEY=base64:GENERATE_WITH_php_artisan_key:generate
APP_DEBUG=false
APP_TIMEZONE=UTC
APP_URL=https://yourdomain.com
ASSET_VERSION=1.0.0
```

### **2. Database Configuration**
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=paperwings_production
DB_USERNAME=your_db_user
DB_PASSWORD=your_secure_password
DB_CHARSET=utf8
DB_COLLATION=utf8_unicode_ci

# Optional - Database Cache
DB_CACHE_CONNECTION=mysql
DB_CACHE_TABLE=cache
```

### **3. Queue Configuration**
```bash
QUEUE_CONNECTION=database
DB_QUEUE_TABLE=jobs
DB_QUEUE=default
DB_QUEUE_RETRY_AFTER=90
```

### **4. Session Configuration**
```bash
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_EXPIRE_ON_CLOSE=false
```

### **5. Cache Configuration**
```bash
# Shared hosting - use file-based cache
CACHE_STORE=file

# NOTE: Redis is not available on shared hosting
# File cache is perfectly suitable for small-medium traffic sites
```

### **6. Mail Configuration**
```bash
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_smtp_user
MAIL_PASSWORD=your_smtp_pass
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@paperwings.com
MAIL_FROM_NAME="${APP_NAME}"

# Optional - Advanced SMTP
MAIL_SCHEME=null
MAIL_URL=null
MAIL_EHLO_DOMAIN=null
```

### **7. Stripe Payment Gateway** ⚠️ **REQUIRED FOR CHECKOUT**
```bash
STRIPE_KEY=pk_live_xxxxxxxxxxxxxxxxxxxxx
STRIPE_SECRET=sk_live_xxxxxxxxxxxxxxxxxxxxx
STRIPE_WEBHOOK_SECRET=whsec_xxxxxxxxxxxxxxxxxxxxx
```

### **8. EposNow API** (Inventory Management)
```bash
EPOSNOW_API_KEY=your_eposnow_api_key
EPOSNOW_API_SECRET=your_eposnow_api_secret
EPOSNOW_API_BASE=https://api.eposnow.com
EPOSNOW_DEFAULT_CATEGORY_ID=null
```

### **9. NZ Post API** (Address Validation)
```bash
NZPOST_API_KEY=your_nzpost_api_key
NZPOST_API_URL=https://api.nzpost.co.nz/addresschecker/v1
```

### **10. Google OAuth** (Social Login)
```bash
GOOGLE_CLIENT_ID=your_google_client_id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
```

### **11. Facebook OAuth** (Social Login)
```bash
FACEBOOK_CLIENT_ID=your_facebook_app_id
FACEBOOK_CLIENT_SECRET=your_facebook_app_secret
FACEBOOK_REDIRECT_URI="${APP_URL}/auth/facebook/callback"
```

### **12. AWS S3** (Optional - For Cloud Storage)
```bash
AWS_ACCESS_KEY_ID=your_aws_key
AWS_SECRET_ACCESS_KEY=your_aws_secret
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your_bucket_name
AWS_URL=https://your_bucket.s3.amazonaws.com
FILESYSTEM_DISK=public
# Change to 's3' if using AWS
```

### **13. Logging Configuration**
```bash
LOG_CHANNEL=stack
LOG_LEVEL=error
LOG_DEPRECATIONS_CHANNEL=null
LOG_DEPRECATIONS_TRACE=false
```

---

## 🚀 **PRODUCTION DEPLOYMENT CHECKLIST**

### **PHASE 1: Pre-Deployment Preparation**

#### **A. Server Requirements (Shared Hosting)**
- [ ] **PHP**: Version 8.2 or higher
- [ ] **PHP Extensions**: 
  - BCMath, Ctype, cURL, DOM, Fileinfo, Filter, Hash, Mbstring
  - OpenSSL, PCRE, PDO, Session, Tokenizer, XML, GD/Imagick
  - ✅ Most shared hosting providers have these enabled by default
- [ ] **Composer Access**: Via SSH or hosting control panel
- [ ] **Node.js/NPM**: For local asset compilation (compile locally, upload built files)
- [ ] **Database**: MySQL 5.7+ or MariaDB 10.3+ (usually provided by hosting)
- [ ] **Web Server**: Apache with mod_rewrite (standard on shared hosting)
- [ ] **SSL Certificate**: Free Let's Encrypt or paid SSL (via cPanel/Plesk)
- [ ] **SSH Access**: Recommended but not required (can use FTP/SFTP)
- [ ] **Cron Jobs**: Available via cPanel/Plesk (for scheduled tasks)

#### **B. Security Configuration**
- [ ] **Generate APP_KEY**: Run `php artisan key:generate`
- [ ] **Set APP_DEBUG=false**: Never enable debug mode in production
- [ ] **Set APP_ENV=production**: Correct environment setting
- [ ] **Secure .env file**: Set permissions to 600 (read/write owner only)
- [ ] **Hide .env from web**: Ensure `.env` is outside `public/` or blocked by `.htaccess`
- [ ] **Disable directory listing**: Configure web server to prevent directory browsing
- [ ] **Configure CORS**: Set proper CORS headers if using API
- [ ] **Rate Limiting**: Verify rate limiting is active on routes
- [ ] **CSRF Protection**: Ensure CSRF middleware is enabled

#### **C. Database Setup**
- [ ] **Create production database**: Create database with proper charset (utf8/utf8mb4)
- [ ] **Database user**: Create dedicated user with limited privileges (no DROP/ALTER in production)
- [ ] **Update .env**: Set `DB_*` credentials
- [ ] **Run migrations**: `php artisan migrate --force`
- [ ] **Run seeders**: `php artisan db:seed --force` (if needed)
- [ ] **Backup database**: Create initial backup before seeding
- [ ] **Test connection**: Verify database connectivity
- [ ] **Create queue tables**: Ensure `jobs`, `failed_jobs` tables exist
- [ ] **Create cache tables**: Ensure `cache`, `cache_locks` tables exist (if using database cache)
- [ ] **Create session table**: Ensure `sessions` table exists

---

### **PHASE 2: Code Deployment**

#### **A. Repository Setup (Choose One Method)**

**Method 1: SSH Access (Recommended)**
- [ ] **Clone repository**: `git clone https://github.com/sumitdeveloper011/paperwings.git`
- [ ] **Checkout production branch**: `git checkout main`
- [ ] **Set correct permissions**:
  ```bash
  chmod -R 755 storage bootstrap/cache
  ```

**Method 2: FTP/SFTP Upload (No SSH)**
- [ ] **Clone locally**: Clone repository on your local machine
- [ ] **Build assets locally**: Run `npm install && npm run build`
- [ ] **Upload via FTP**: Upload all files to hosting (e.g., `public_html/` or `www/`)
- [ ] **Exclude from upload**: `node_modules/`, `.git/`, `.env` (upload .env separately)
- [ ] **Set permissions**: Use cPanel File Manager to set `storage/` and `bootstrap/cache/` to 755

#### **B. Dependencies Installation**

**If you have SSH access:**
- [ ] **Install Composer packages**: 
  ```bash
  composer install --optimize-autoloader --no-dev
  ```
- [ ] **Install NPM packages**: `npm ci --production`
- [ ] **Build frontend assets**: `npm run build`

**If you DON'T have SSH (FTP upload only):**
- [ ] **Install dependencies locally**: Run `composer install` and `npm install` on local machine
- [ ] **Build assets locally**: Run `npm run build` on local machine
- [ ] **Upload vendor folder**: Upload `vendor/` directory via FTP (may take time due to many files)
- [ ] **Upload built assets**: Upload `public/build/` and all compiled CSS/JS files
- [ ] **Alternative**: Use hosting control panel's "Composer" feature if available

#### **C. Configuration & Optimization**

**Via SSH (if available):**
- [ ] **Copy .env file**: Copy `.env.example` to `.env` and configure all keys
- [ ] **Link storage**: `php artisan storage:link`
- [ ] **Cache configuration**: `php artisan config:cache`
- [ ] **Cache routes**: `php artisan route:cache`
- [ ] **Cache views**: `php artisan view:cache`
- [ ] **Optimize autoloader**: `composer dump-autoload --optimize`

**Via cPanel/Plesk (No SSH):**
- [ ] **Upload .env file**: Create `.env` via File Manager and paste configured values
- [ ] **Run artisan commands**: Use hosting terminal/SSH or create a temporary PHP file:
  ```php
  <?php
  // artisan-runner.php (create in public/, delete after use)
  require __DIR__.'/../vendor/autoload.php';
  $app = require_once __DIR__.'/../bootstrap/app.php';
  
  Artisan::call('storage:link');
  Artisan::call('config:cache');
  Artisan::call('route:cache');
  Artisan::call('view:cache');
  
  echo "Optimization complete!";
  // DELETE THIS FILE AFTER RUNNING
  ```
- [ ] **Access via browser**: Visit `https://yourdomain.com/artisan-runner.php`
- [ ] **Delete file immediately**: Remove `artisan-runner.php` after running for security

---

### **PHASE 3: Third-Party Integrations**

#### **A. Payment Gateway (Stripe)**
- [ ] **Set production keys**: Use `pk_live_*` and `sk_live_*` keys
- [ ] **Configure webhook**: Set webhook URL in Stripe dashboard
  - URL: `https://yourdomain.com/stripe/webhook`
  - Events: `payment_intent.succeeded`, `charge.refunded`, etc.
- [ ] **Copy webhook secret**: Add `STRIPE_WEBHOOK_SECRET` to .env
- [ ] **Test payment flow**: Perform test checkout with real card (small amount)
- [ ] **Verify payment receipts**: Ensure order confirmation emails are sent

#### **B. Email Configuration**
- [ ] **Set SMTP credentials**: Configure production mail server
- [ ] **Set FROM address**: Use real domain email address
- [ ] **Test email sending**: 
  ```bash
  php artisan tinker
  Mail::raw('Test email', function($msg) {
      $msg->to('admin@example.com')->subject('Production Test');
  });
  ```
- [ ] **Verify email templates**: Test all email templates (order, verification, reset password)
- [ ] **SPF/DKIM records**: Configure DNS records for email deliverability

#### **C. External APIs**
- [ ] **EposNow**: Configure API keys (if using inventory sync)
- [ ] **NZ Post**: Configure address validation API keys
- [ ] **Google OAuth**: Update authorized redirect URIs in Google Console
- [ ] **Facebook OAuth**: Update redirect URIs in Facebook App settings
- [ ] **Test API connections**: Verify all external APIs are reachable

---

### **PHASE 4: Web Server Configuration (Shared Hosting)**

#### **A. Document Root Setup**
- [ ] **Set document root to `public/`**: 
  - In cPanel: Go to "Domains" → Select domain → Change "Document Root" to `/public_html/public` or `/paperwings/public`
  - Alternative: Move all `public/*` files to root and update `index.php` paths
- [ ] **Verify .htaccess exists**: Ensure `public/.htaccess` file is present
- [ ] **Enable mod_rewrite**: Usually enabled by default on shared hosting

#### **B. .htaccess Configuration (public/.htaccess)**
Laravel's default `.htaccess` should work, but you can add security headers:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

# Security Headers
<IfModule mod_headers.c>
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
</IfModule>

# Disable directory browsing
Options -Indexes

# Protect .env file
<Files .env>
    Order allow,deny
    Deny from all
</Files>
```

#### **C. SSL Certificate (via cPanel/Plesk)**
- [ ] **Install SSL**: Use cPanel's "SSL/TLS" section
  - **Free SSL**: Let's Encrypt (AutoSSL) - recommended
  - **Paid SSL**: Purchase and install via cPanel
- [ ] **Force HTTPS**: Enable "Force HTTPS Redirect" in cPanel or add to .htaccess:
  ```apache
  # Force HTTPS (add to top of .htaccess)
  RewriteEngine On
  RewriteCond %{HTTPS} off
  RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
  ```

#### **D. PHP Configuration (via cPanel)**
- [ ] **Select PHP version**: Choose PHP 8.2 or higher in "Select PHP Version"
- [ ] **PHP Extensions**: Enable required extensions via cPanel's PHP extension manager
- [ ] **PHP Settings**: Update via cPanel's "MultiPHP INI Editor" or create `.user.ini` in root:
  ```ini
  upload_max_filesize = 20M
  post_max_size = 25M
  max_execution_time = 300
  memory_limit = 256M
  max_input_vars = 3000
  ```

---

### **PHASE 5: Background Processes (Shared Hosting)**

#### **A. Queue Processing (Without Supervisor)**
Since shared hosting doesn't support supervisor, use cron jobs to process queues:

**Option 1: Process queues via cron (Recommended for low traffic)**
- [ ] **Add to cPanel Cron Jobs**: Every 1 minute
  ```bash
  * * * * * cd /home/username/public_html && php artisan queue:work --stop-when-empty --max-time=50 >> /dev/null 2>&1
  ```
  - This runs queue worker every minute and processes all pending jobs
  - `--stop-when-empty`: Stops when no jobs remain
  - `--max-time=50`: Ensures it finishes within 1 minute cron cycle

**Option 2: Run queue synchronously (For very low traffic)**
- [ ] **Change .env**: `QUEUE_CONNECTION=sync`
  - Jobs execute immediately (not queued)
  - Simpler but slower for user (they wait for job completion)

**Important Notes:**
- Shared hosting typically doesn't allow long-running processes
- Queue workers will be killed by hosting provider if they run too long
- For high-traffic sites, consider upgrading to VPS with supervisor access

#### **B. Scheduled Tasks (Cron Jobs via cPanel)**
- [ ] **Access cPanel Cron Jobs**: Go to "Cron Jobs" in cPanel
- [ ] **Add Laravel scheduler cron**: Run every 1 minute
  ```bash
  * * * * * cd /home/username/public_html && php artisan schedule:run >> /dev/null 2>&1
  ```
  - Replace `/home/username/public_html` with your actual path
  - Use cPanel's "Get path" feature if unsure

- [ ] **Scheduled commands in your app**:
  - Cleanup unverified users: Weekly (Sundays at 2 AM UTC)
  - Any future scheduled tasks will run automatically via this cron

- [ ] **Test cron setup**: Wait 1 minute and check `storage/logs/laravel.log` for cron execution
- [ ] **Alternative test**: Run manually via SSH: `php artisan schedule:run`

---

### **PHASE 6: File Storage & Uploads**

#### **A. Storage Configuration (Shared Hosting)**

**Via SSH:**
- [ ] **Create storage directories**:
  ```bash
  mkdir -p storage/app/public/{products,galleries,sliders,brands,testimonials}
  mkdir -p storage/logs
  mkdir -p storage/framework/{cache,sessions,views}
  ```
- [ ] **Set permissions**:
  ```bash
  chmod -R 755 storage
  chmod -R 755 bootstrap/cache
  ```
- [ ] **Symlink public storage**: `php artisan storage:link`

**Via cPanel File Manager (No SSH):**
- [ ] **Navigate to storage/app/public/**: Create subdirectories using "New Folder"
  - Create: `products`, `galleries`, `sliders`, `brands`, `testimonials`
- [ ] **Set permissions**: Right-click folders → "Change Permissions" → Set to `755`
- [ ] **Create symlink manually**:
  ```bash
  # If PHP shell access available in cPanel
  symlink('/home/username/public_html/storage/app/public', '/home/username/public_html/public/storage');
  ```
  - Or use the artisan-runner.php method from Phase 2
  - Or ask hosting support to create symlink

- [ ] **Upload limits**: Create `.user.ini` in root directory (cPanel method):
  ```ini
  upload_max_filesize = 20M
  post_max_size = 25M
  max_execution_time = 300
  memory_limit = 256M
  ```

---

### **PHASE 7: Performance & Security**

#### **A. Performance Optimization**
- [ ] **Enable OPcache**: Check if enabled in cPanel's PHP settings (usually enabled by default)
- [ ] **Database indexing**: Verify indexes on high-traffic tables
- [ ] **Query optimization**: Monitor slow queries via cPanel's phpMyAdmin
- [ ] **Cache warming**: Run optimization commands:
  ```bash
  php artisan config:cache
  php artisan route:cache
  php artisan view:cache
  ```
- [ ] **File cache**: Since no Redis, ensure `CACHE_STORE=file` in .env
- [ ] **Monitor disk usage**: Shared hosting has limited disk space, regularly clean `storage/logs/`

#### **B. Security Configuration**
- [ ] **Set log level**: `LOG_LEVEL=error` in .env
- [ ] **Enable brute-force protection**: cPanel's "cPHulk" (if available)
- [ ] **Password protect admin** (optional): cPanel's "Password Protect Directories"
- [ ] **Verify security headers**: https://securityheaders.com/
- [ ] **Verify SSL certificate**: https://www.ssllabs.com/ssltest/

---

### **PHASE 8: Testing & Validation**

#### **A. Functional Testing**
- [ ] **Homepage loads**: Verify homepage without errors
- [ ] **User registration**: Create test account and verify email
- [ ] **User login**: Test login with credentials
- [ ] **Product browsing**: Browse categories and products
- [ ] **Add to cart**: Add products to cart
- [ ] **Checkout flow**: Complete full checkout process
- [ ] **Payment processing**: Test Stripe payment (small amount)
- [ ] **Order confirmation**: Verify order email received
- [ ] **Admin login**: Access admin panel
- [ ] **Admin CRUD**: Test create/edit/delete operations
- [ ] **File uploads**: Test image uploads (products, galleries)
- [ ] **Search functionality**: Test search bar
- [ ] **Wishlist**: Add/remove products from wishlist
- [ ] **Reviews**: Submit product review
- [ ] **Contact form**: Test contact form submission

#### **B. Quick Tests**
- [ ] **Page load speed**: Google PageSpeed Insights
- [ ] **Mobile responsiveness**: Test on mobile devices
- [ ] **HTTPS enforcement**: Verify all pages use HTTPS
- [ ] **Security**: Check forms have CSRF tokens

---

### **PHASE 9: Backup & Disaster Recovery (Shared Hosting)**

#### **A. Database Backup (via cPanel)**
- [ ] **Automatic backups**: Enable in cPanel's "Backup Wizard"
  - Most shared hosts offer daily/weekly automatic backups
  - Check your hosting plan's backup policy
- [ ] **Manual backup**: Use cPanel's "Backup" → "Download a MySQL Database Backup"
  - Download `.sql.gz` file regularly
  - Store off-site (Google Drive, Dropbox, local computer)
- [ ] **Schedule via cron** (if SSH available):
  ```bash
  # Daily backup at 3 AM
  0 3 * * * mysqldump -u DB_USER -p'DB_PASS' DB_NAME | gzip > ~/backups/db_$(date +\%Y\%m\%d).sql.gz
  ```

**Backup Script for cPanel (backup-db.php):**
```php
<?php
// backup-db.php - Place in a protected directory, run via cron
$dbHost = 'localhost';
$dbUser = 'your_db_user';
$dbPass = 'your_db_pass';
$dbName = 'your_db_name';
$backupDir = __DIR__ . '/backups/';

if (!is_dir($backupDir)) mkdir($backupDir, 0755, true);

$filename = $backupDir . 'db_' . date('Ymd_His') . '.sql';
$command = "mysqldump -h{$dbHost} -u{$dbUser} -p{$dbPass} {$dbName} > {$filename}";

exec($command, $output, $return);

if ($return === 0) {
    exec("gzip {$filename}");
    echo "Backup successful: " . basename($filename) . ".gz\n";
    
    // Delete backups older than 30 days
    $files = glob($backupDir . "db_*.sql.gz");
    foreach ($files as $file) {
        if (filemtime($file) < strtotime('-30 days')) {
            unlink($file);
        }
    }
} else {
    echo "Backup failed!\n";
}
```

- [ ] **Schedule via cron**: 
  ```bash
  0 3 * * * php /home/username/public_html/backup-db.php >> /home/username/backup.log 2>&1
  ```

#### **B. File Backup**
- [ ] **Full account backup**: Use cPanel's "Backup" → "Download a Full Account Backup"
  - Includes all files, databases, email, configurations
  - Run monthly and download to local storage
- [ ] **Partial backup**: Backup only important directories
  - Download `storage/app/public/` (product images, uploads)
  - Download `.env` file (securely store credentials)
  - Download `database/` directory (migrations, seeders)
- [ ] **Automated backup solutions**:
  - Use hosting provider's automatic backup service
  - Consider third-party backup services (CodeGuard, JetBackup)
  - Use FTP backup tools (WinSCP, FileZilla with scheduled sync)
- [ ] **Off-site storage**: Store backups in multiple locations
  - Cloud storage (Google Drive, Dropbox, OneDrive)
  - External hard drive (local backup)
  - Different hosting provider (redundancy)
- [ ] **Test restore**: Periodically test restoring from backup on staging environment

---

### **PHASE 10: Go-Live**
- [ ] **DNS update**: Point domain to production server
- [ ] **DNS propagation**: Wait 24-48 hours for full propagation
- [ ] **SSL certificate**: Verify HTTPS works on live domain
- [ ] **Smoke test**: Re-test critical functionality on live domain
- [ ] **Notify stakeholders**: Inform team of successful deployment

---

## 🔧 **ESSENTIAL COMMANDS**
```bash
# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer dump-autoload --optimize

# Database operations
php artisan migrate --force
php artisan db:seed --force

# Queue management
php artisan queue:restart
php artisan queue:failed  # View failed jobs
php artisan queue:retry all  # Retry failed jobs

# Maintenance mode
php artisan down --refresh=15  # Enable with 15s refresh
php artisan up  # Disable maintenance mode
```

## 📊 **PRODUCTION .ENV SETTINGS**
```bash
APP_ENV=production
APP_DEBUG=false
LOG_LEVEL=error
CACHE_STORE=file  # Shared hosting - no Redis
QUEUE_CONNECTION=database  # Process via cron job
MAIL_MAILER=smtp  # Use production mail server
SESSION_DRIVER=database  # Or 'file' if database sessions cause issues
```

---

## ⚠️ **CRITICAL WARNINGS**

1. **NEVER commit .env file** - Contains sensitive credentials
2. **NEVER enable APP_DEBUG in production** - Exposes sensitive data
3. **ALWAYS use HTTPS** - Required for Stripe payments
4. **ALWAYS backup before deployments** - Database and files
5. **ALWAYS use strong passwords** - Database, admin, API keys

---

## 🆘 **TROUBLESHOOTING**

### **Common Issues**

#### **Issue: 500 Internal Server Error**
```bash
# Check Laravel logs in cPanel File Manager
# storage/logs/laravel.log

# Common fixes
chmod -R 755 storage bootstrap/cache
php artisan config:clear
php artisan cache:clear
```

#### **Issue: Queue jobs not processing (Shared Hosting)**
```bash
# Check if cron job is running
# View cron execution logs in cPanel

# Check failed jobs
php artisan queue:failed

# Manually process queue
php artisan queue:work --stop-when-empty

# If issues persist, use sync driver (no queue)
# In .env: QUEUE_CONNECTION=sync

# Check cPanel cron job logs
cat ~/logs/cron.log  # Path varies by host
```

#### **Issue: Emails not sending**
```bash
# Test mail configuration
php artisan tinker
Mail::raw('Test', fn($msg) => $msg->to('test@example.com')->subject('Test'));

# Check mail logs
tail -f storage/logs/laravel.log | grep -i mail
```

#### **Issue: Images not displaying (Shared Hosting)**
```bash
# Verify storage link exists
ls -la public/storage  # Should be a symlink

# Create symlink via SSH
php artisan storage:link

# Or create manually
ln -s ../storage/app/public public/storage

# Via cPanel: Use File Manager → Create symlink
# Source: /home/username/public_html/storage/app/public
# Destination: /home/username/public_html/public/storage

# Check permissions via cPanel File Manager
# Set storage/ to 755
# Set storage/app/public/ to 755

# If symlinks not allowed by host, move files:
# Move storage/app/public/* to public/storage/
# Update .env: FILESYSTEM_DISK=public
```

#### **Issue: 500 Error after deployment**
```bash
# Check error logs in cPanel
# File Manager → storage/logs/laravel.log

# Common fixes:
# 1. Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# 2. Check .env file exists and APP_KEY is set
php artisan key:generate

# 3. Check file permissions
chmod -R 755 storage bootstrap/cache

# 4. Check PHP version (must be 8.2+)
# cPanel → Select PHP Version

# 5. Verify .htaccess exists in public/
# Reupload from Laravel fresh install if missing
```

#### **Issue: Composer/SSH not available**
```bash
# Use cPanel's built-in composer (if available)
# Terminal → composer install

# Or install dependencies locally and upload via FTP
# Local: composer install --no-dev
# Upload entire vendor/ folder

# Use hosting provider's Softaculous/Installatron
# Some provide Laravel auto-installer

# Contact hosting support for composer access
# Many shared hosts provide SSH upon request
```

---

## 🎯 **SHARED HOSTING TIPS**

### **1. Document Root Configuration**

**Option A: Point domain to `/public` (Recommended)**
```
Domain: yourdomain.com → /home/username/public_html/public
```

**Option B: Move public contents to root (Alternative)**
```bash
# Move all files from public/ to root
mv public/* ./
mv public/.htaccess ./

# Update index.php paths
# Change: require __DIR__.'/../vendor/autoload.php';
# To: require __DIR__.'/vendor/autoload.php';

# Change: $app = require_once __DIR__.'/../bootstrap/app.php';
# To: $app = require_once __DIR__.'/bootstrap/app.php';
```

### **2. Performance Optimization**

```bash
# 1. Use file cache (already configured)
CACHE_STORE=file

# 2. Cache everything
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 3. Optimize composer autoloader
composer dump-autoload --optimize --classmap-authoritative

# 4. Set log level to error only
LOG_LEVEL=error

# 5. Clean old logs via cron
find storage/logs/ -name "*.log" -type f -mtime +7 -delete
```

### **3. When to Upgrade to VPS**

Consider VPS when:
- Site traffic exceeds 10,000+ visitors/month
- Queue jobs fail frequently
- Need real-time features (WebSockets)
- Shared hosting resources maxed out

---

## 📞 **SUPPORT**

- **Laravel Docs**: https://laravel.com/docs/12.x
- **Stripe Docs**: https://stripe.com/docs
- **Repository**: https://github.com/sumitdeveloper011/paperwings

---

**Last Updated**: 2026-01-18  
**Version**: 2.0.0 (Shared Hosting Edition)

