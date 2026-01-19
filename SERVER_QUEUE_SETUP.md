# Server Queue Setup Guide - PaperWings

## ðŸš€ **Complete Server Setup Instructions**

This guide covers setting up Laravel queues on your **shared hosting server** (production environment).

---

## **ðŸ“‹ Prerequisites**

- âœ… Access to cPanel
- âœ… SSH access (optional but recommended)
- âœ… Project files uploaded to server
- âœ… Database configured and working

---

## **Step 1: Environment Configuration**

### **1.1 Access Your `.env` File**

**Via cPanel File Manager:**
1. Log in to cPanel
2. Go to **File Manager**
3. Navigate to your project root directory
4. Find `.env` file
5. Click **Edit**

**Via FTP:**
- Download `.env` file
- Edit locally
- Upload back to server

### **1.2 Update Queue Configuration**

Ensure your `.env` file contains:

```bash
QUEUE_CONNECTION=database
DB_QUEUE_TABLE=jobs
DB_QUEUE=default
DB_QUEUE_RETRY_AFTER=90
```

**âš ï¸ Important:** 
- If `QUEUE_CONNECTION=sync`, change it to `database`
- Save the file after editing

---

## **Step 2: Database Setup**

### **2.1 Run Migrations**

**Option A: Via SSH (Recommended)**

```bash
cd /home/n8mzgc0ev1uw/public_html/demo/demo.paperwings.co.nz
php artisan migrate --force
```

**Option B: Via cPanel Terminal**

1. Go to cPanel â†’ **Terminal**
2. Navigate to your project:
   ```bash
   cd public_html/demo/demo.paperwings.co.nz
   ```
3. Run migration:
   ```bash
   php artisan migrate --force
   ```

**Option C: Via phpMyAdmin (If migrations fail)**

1. Go to cPanel â†’ **phpMyAdmin**
2. Select your database
3. Check if these tables exist:
   - `jobs`
   - `failed_jobs`
4. If missing, run migrations via SSH or create manually

### **2.2 Verify Tables Created**

**Via SSH/Terminal:**
```bash
php artisan tinker
```

Then:
```php
DB::table('jobs')->exists(); // Should return true
DB::table('failed_jobs')->exists(); // Should return true
exit
```

---

## **Step 3: Clear Application Caches**

**Via SSH/Terminal:**
```bash
cd /home/n8mzgc0ev1uw/public_html/demo/demo.paperwings.co.nz
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

**Why:** Ensures new queue configuration is loaded.

---

## **Step 4: Find Your Project Path**

### **4.1 Method 1: Via cPanel File Manager**

1. Open **File Manager**
2. Navigate to your project root
3. Look at the path shown in the address bar
4. Common format: `/home/username/public_html/project-name`

### **4.2 Method 2: Via SSH**

```bash
pwd
```

This shows your current directory path.

### **4.3 Method 3: Check Existing Files**

Check your `index.php` or `.env` file for absolute paths.

**Common Paths:**
- `/home/n8mzgc0ev1uw/public_html/demo/demo.paperwings.co.nz`
- `/home/username/public_html/paperwings`
- `/home/username/public_html`

**âš ï¸ Write down your exact path - you'll need it for cron job!**

---

## **Step 5: Find Your PHP Path**

### **5.1 Method 1: Via cPanel**

1. Go to cPanel â†’ **Select PHP Version**
2. Note the PHP version you're using
3. Common paths:
   - PHP 8.1: `/opt/cpanel/ea-php81/root/usr/bin/php`
   - PHP 8.2: `/opt/cpanel/ea-php82/root/usr/bin/php`
   - Default: `/usr/bin/php`

### **5.2 Method 2: Via SSH**

```bash
which php
```

This shows the PHP executable path.

### **5.3 Method 3: Test Different Paths**

Try these common paths:
```bash
/usr/bin/php --version
/opt/cpanel/ea-php81/root/usr/bin/php --version
/opt/cpanel/ea-php82/root/usr/bin/php --version
```

**âš ï¸ Write down your PHP path - you'll need it for cron job!**

---

## **Step 6: Set Up Cron Job in cPanel**

### **6.1 Access Cron Jobs**

1. Log in to **cPanel**
2. Scroll down to **Advanced** section
3. Click **Cron Jobs**

### **6.2 Add New Cron Job**

1. Scroll to **Add New Cron Job** section
2. **Common Settings:** Select **Every Minute** (`* * * * *`)
3. **Command:** Enter the following (replace with your paths):

```bash
cd /home/n8mzgc0ev1uw/public_html/demo/demo.paperwings.co.nz && /usr/bin/php artisan schedule:run >> /dev/null 2>&1
```

**Replace:**
- `/home/n8mzgc0ev1uw/public_html/demo/demo.paperwings.co.nz` â†’ Your project path
- `/usr/bin/php` â†’ Your PHP path

### **6.3 Example Commands**

**If your PHP is 8.1:**
```bash
cd /home/n8mzgc0ev1uw/public_html/demo/demo.paperwings.co.nz && /opt/cpanel/ea-php81/root/usr/bin/php artisan schedule:run >> /dev/null 2>&1
```

**If your PHP is 8.2:**
```bash
cd /home/n8mzgc0ev1uw/public_html/demo/demo.paperwings.co.nz && /opt/cpanel/ea-php82/root/usr/bin/php artisan schedule:run >> /dev/null 2>&1
```

### **6.4 Save Cron Job**

1. Click **Add New Cron Job** button
2. You should see a success message
3. Your cron job will appear in the **Current Cron Jobs** list

---

## **Step 7: Verify Cron Job**

### **7.1 Check Cron Job Status**

1. In cPanel â†’ **Cron Jobs**
2. Look at **Current Cron Jobs** section
3. Your cron job should be listed with schedule: `* * * * *`

### **7.2 Test Cron Job Manually**

1. In **Current Cron Jobs** section
2. Find your cron job
3. Click **Run Now** button
4. Check for any error messages

### **7.3 Check Cron Logs**

1. In cPanel â†’ **Cron Jobs**
2. Scroll to **Recent Cron Job Activity**
3. Look for your cron job running
4. Check for any errors in the output

### **7.4 Verify Queue Processing**

**Via SSH/Terminal:**
```bash
php artisan tinker
```

Then:
```php
// Check pending jobs
DB::table('jobs')->count();

// Check failed jobs
DB::table('failed_jobs')->count();

exit
```

---

## **Step 8: Test Queue Processing**

### **Test 1: Order Email**

1. **Place a test order** on your website
2. **Check queue status:**
   ```bash
   php artisan tinker
   DB::table('jobs')->count(); // Should show 1 or more
   exit
   ```
3. **Wait 1-2 minutes** (cron runs every minute)
4. **Check again:**
   ```bash
   php artisan tinker
   DB::table('jobs')->count(); // Should be 0 (processed)
   exit
   ```
5. **Verify email received** in your inbox

### **Test 2: Newsletter**

1. **Go to Admin Panel** â†’ Subscriptions â†’ Create Newsletter
2. **Send test email** to yourself
3. **Check queue status** (same as Test 1)
4. **Wait 1-2 minutes**
5. **Verify email received**

### **Test 3: Check Logs**

**Via SSH:**
```bash
tail -f storage/logs/laravel.log
```

Look for queue processing messages like:
- `Processed: App\Mail\OrderConfirmationMail`
- `Processed: App\Jobs\SendNewsletterJob`

---

## **Step 9: Monitor Queue System**

### **9.1 Daily Checks**

**Check queue status:**
```bash
php artisan tinker
DB::table('jobs')->count(); // Pending jobs
DB::table('failed_jobs')->count(); // Failed jobs
exit
```

### **9.2 Weekly Maintenance**

**View failed jobs:**
```bash
php artisan queue:failed
```

**Retry failed jobs:**
```bash
php artisan queue:retry all
```

**Clear old failed jobs (optional):**
```bash
php artisan queue:flush
```

### **9.3 Check Cron Activity**

1. Go to cPanel â†’ **Cron Jobs**
2. Check **Recent Cron Job Activity**
3. Ensure cron is running every minute
4. Look for any errors

---

## **ðŸ”§ Troubleshooting**

### **Problem: Cron Job Not Running**

**Solutions:**
1. âœ… Verify cron job exists in **Current Cron Jobs**
2. âœ… Check cron job syntax (no typos)
3. âœ… Verify paths are correct (project path + PHP path)
4. âœ… Test manually: Click **Run Now** in cPanel
5. âœ… Check **Recent Cron Job Activity** for errors
6. âœ… Ensure cron is enabled in your hosting account

### **Problem: Jobs Not Processing**

**Solutions:**
1. âœ… Check `.env` has `QUEUE_CONNECTION=database`
2. âœ… Verify `jobs` table exists: `DB::table('jobs')->exists();`
3. âœ… Clear config cache: `php artisan config:clear`
4. âœ… Check PHP path is correct in cron job
5. âœ… Verify project path is correct in cron job
6. âœ… Check file permissions: `chmod -R 775 storage bootstrap/cache`

### **Problem: Jobs Stuck/Failed**

**Solutions:**
1. âœ… Check failed jobs: `php artisan queue:failed`
2. âœ… View error details: `DB::table('failed_jobs')->latest()->first();`
3. âœ… Retry failed jobs: `php artisan queue:retry all`
4. âœ… Check `storage/logs/laravel.log` for errors
5. âœ… Verify database connection is working

### **Problem: Path Issues**

**Solutions:**
1. âœ… Use **absolute paths** (not relative)
2. âœ… No `~` or `$HOME` in paths
3. âœ… Verify project path exists: `ls -la /your/project/path`
4. âœ… Verify PHP path works: `/your/php/path --version`
5. âœ… Test command manually in SSH before adding to cron

### **Problem: Permission Errors**

**Solutions:**
```bash
chmod -R 775 storage bootstrap/cache
chown -R username:username storage bootstrap/cache
```

Replace `username` with your server username.

---

## **ðŸ“Š Queue Status Commands**

### **Quick Status Check**

```bash
php artisan tinker
```

Then:
```php
// Pending jobs in default queue
DB::table('jobs')->where('queue', 'default')->count();

// Pending newsletter jobs
DB::table('jobs')->where('queue', 'newsletters')->count();

// Pending import jobs
DB::table('jobs')->where('queue', 'imports')->count();

// All pending jobs
DB::table('jobs')->count();

// Failed jobs
DB::table('failed_jobs')->count();

exit
```

### **View Failed Jobs**

```bash
php artisan queue:failed
```

### **Retry Failed Jobs**

```bash
# Retry specific job
php artisan queue:retry <job-id>

# Retry all failed jobs
php artisan queue:retry all
```

### **Test Scheduler Manually**

```bash
php artisan schedule:run
```

---

## **âœ… Server Setup Checklist**

- [ ] `.env` file updated with `QUEUE_CONNECTION=database`
- [ ] Migrations run on server: `php artisan migrate --force`
- [ ] Config cache cleared: `php artisan config:clear`
- [ ] Project path identified and written down
- [ ] PHP path identified and written down
- [ ] Cron job added in cPanel
- [ ] Cron job tested (Run Now button)
- [ ] Cron job verified in Recent Activity
- [ ] Test order placed â†’ email queued
- [ ] Jobs processed (check `jobs` table)
- [ ] Email received successfully
- [ ] Monitor for 24 hours to ensure stability

---

## **ðŸ“ Quick Reference**

### **Cron Job Command Template**

```bash
cd YOUR_PROJECT_PATH && YOUR_PHP_PATH artisan schedule:run >> /dev/null 2>&1
```

### **Common PHP Paths**

- Default: `/usr/bin/php`
- PHP 8.1: `/opt/cpanel/ea-php81/root/usr/bin/php`
- PHP 8.2: `/opt/cpanel/ea-php82/root/usr/bin/php`
- PHP 8.0: `/opt/cpanel/ea-php80/root/usr/bin/php`

### **Common Project Paths**

- `/home/username/public_html/project-name`
- `/home/username/public_html/demo/demo.paperwings.co.nz`
- `/home/username/public_html`

---

## **ðŸŽ¯ What Happens After Setup**

1. **Every minute**, cron runs `schedule:run`
2. **Scheduler processes** 3 queue types:
   - Default queue (emails, notifications) - every 1 minute
   - Newsletter queue - every 1 minute
   - Import queue - every 5 minutes
3. **Jobs are processed** automatically in background
4. **Emails are sent** without blocking user requests
5. **Failed jobs** are logged in `failed_jobs` table

---

## **ðŸ“ž Support**

If you encounter issues:

1. **Check logs:** `storage/logs/laravel.log`
2. **Verify cron:** Check Recent Cron Job Activity
3. **Test manually:** Run `php artisan schedule:run` in SSH
4. **Check paths:** Verify project and PHP paths are correct
5. **Review errors:** Check `failed_jobs` table for error details

---

## **ðŸ“… Maintenance Schedule**

### **Daily (Optional)**
- Check `jobs` table count
- Monitor for stuck jobs

### **Weekly (Recommended)**
- Review failed jobs: `php artisan queue:failed`
- Retry failed jobs if needed
- Check cron activity logs

### **Monthly (Optional)**
- Clear old failed jobs: `php artisan queue:flush`
- Optimize database tables
- Review queue performance

---

**Last Updated:** 2025-01-18  
**Version:** 1.0  
**For:** PaperWings Production Server
