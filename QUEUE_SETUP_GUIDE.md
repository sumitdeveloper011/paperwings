# Queue Setup Guide - PaperWings

## ✅ **Implementation Complete**

All queue processing has been configured and optimized for your shared hosting environment.

---

## **📋 What Was Implemented**

### **1. Scheduler Configuration** (`routes/console.php`)
- ✅ Main queue processing (emails, notifications) - Every 1 minute
- ✅ Newsletter queue processing - Every 1 minute
- ✅ Import jobs queue processing - Every 5 minutes

### **2. Queue Optimization**
- ✅ `SendNewsletterJob` → Uses `newsletters` queue
- ✅ `ImportEposNowProductsJob` → Uses `imports` queue
- ✅ `ImportEposNowCategoriesJob` → Uses `imports` queue

---

## **🚀 Setup Steps**

### **Step 1: Environment Configuration**

Ensure your `.env` file has:
```bash
QUEUE_CONNECTION=database
DB_QUEUE_TABLE=jobs
DB_QUEUE=default
DB_QUEUE_RETRY_AFTER=90
```

### **Step 2: Database Setup**

Run migrations (if not already done):
```bash
php artisan migrate
```

This creates:
- `jobs` table (stores queued jobs)
- `failed_jobs` table (stores failed jobs)

### **Step 3: Set Up Cron Job**

**Location:** cPanel → Cron Jobs

**Add this cron job:**
```bash
* * * * * cd /home/n8mzgc0ev1uw/public_html/demo/demo.paperwings.co.nz && /usr/bin/php artisan schedule:run >> /dev/null 2>&1
```

**⚠️ Important:** Replace `/home/n8mzgc0ev1uw/public_html/demo/demo.paperwings.co.nz` with your actual project path.

**How to find your path:**
1. SSH into your server
2. Run: `pwd` in your project directory
3. Or check in cPanel File Manager

---

## **📊 Queue Processing Overview**

### **Queue Types:**

| Queue Name | What It Handles | Processing Frequency | Max Time |
|------------|----------------|---------------------|----------|
| **default** | Order emails, Contact emails, User notifications | Every 1 minute | 60 seconds |
| **newsletters** | Newsletter emails | Every 1 minute | 60 seconds |
| **imports** | Product/Category imports | Every 5 minutes | 3600 seconds (1 hour) |

### **Job Distribution:**

| Job Type | Queue | Created In |
|----------|-------|------------|
| Newsletter emails | `newsletters` | Admin → Subscriptions |
| Order emails | `default` | Checkout, Order Status |
| Import jobs | `imports` | Admin → Products/Categories |
| Contact emails | `default` | Contact Form |
| User notifications | `default` | Registration, Password Reset |

---

## **🧪 Testing**

### **Test 1: Order Email**
1. Place a test order
2. Check queue: `php artisan tinker` → `DB::table('jobs')->count();`
3. Wait 1-2 minutes
4. Check again — should be 0 (processed)
5. Verify email received

### **Test 2: Newsletter**
1. Go to Admin → Subscriptions → Create Newsletter
2. Send test email to yourself
3. Check queue: `DB::table('jobs')->count();`
4. Wait 1-2 minutes
5. Verify email received

### **Test 3: Import Job**
1. Go to Admin → Products → Import from EPOSNOW
2. Start import
3. Check queue: `DB::table('jobs')->where('queue', 'imports')->count();`
4. Monitor progress in admin panel
5. Verify import completes

---

## **📈 Monitoring**

### **Check Queue Status:**

```bash
php artisan tinker
```

Then:
```php
// Check pending jobs in default queue
DB::table('jobs')->where('queue', 'default')->count();

// Check pending newsletter jobs
DB::table('jobs')->where('queue', 'newsletters')->count();

// Check pending import jobs
DB::table('jobs')->where('queue', 'imports')->count();

// Check all pending jobs
DB::table('jobs')->count();

// Check failed jobs
DB::table('failed_jobs')->count();
```

### **View Failed Jobs:**
```bash
php artisan queue:failed
```

### **Retry Failed Jobs:**
```bash
# Retry specific job
php artisan queue:retry <job-id>

# Retry all failed jobs
php artisan queue:retry all
```

### **Clear Old Failed Jobs:**
```bash
php artisan queue:flush
```

---

## **🔧 Troubleshooting**

### **Jobs Not Processing:**

1. **Check cron is running:**
   - Verify cron job exists in cPanel
   - Check cron logs in cPanel

2. **Check queue connection:**
   ```bash
   php artisan tinker
   config('queue.default'); // Should return 'database'
   ```

3. **Check database tables:**
   ```bash
   php artisan tinker
   DB::table('jobs')->exists(); // Should return true
   ```

4. **Check logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

### **Failed Jobs:**

1. **View failed jobs:**
   ```bash
   php artisan queue:failed
   ```

2. **Check error messages:**
   ```bash
   php artisan tinker
   DB::table('failed_jobs')->latest()->first();
   ```

3. **Retry failed jobs:**
   ```bash
   php artisan queue:retry all
   ```

### **Import Jobs Stuck:**

1. **Check if job is processing:**
   ```bash
   php artisan tinker
   DB::table('jobs')->where('queue', 'imports')->get();
   ```

2. **Check job timeout:**
   - Import jobs have 1 hour timeout
   - If stuck longer, manually delete from `jobs` table

3. **Restart import:**
   - Go to Admin panel
   - Retry the import

---

## **📝 Maintenance**

### **Weekly Tasks:**

1. **Check failed jobs:**
   ```bash
   php artisan queue:failed
   ```

2. **Review failed job errors:**
   ```bash
   php artisan tinker
   DB::table('failed_jobs')->whereDate('failed_at', '>=', now()->subDays(7))->get();
   ```

3. **Clear old failed jobs (optional):**
   ```bash
   php artisan queue:flush
   ```

### **Monthly Tasks:**

1. **Check queue performance:**
   - Review `jobs` table size
   - Check processing times

2. **Optimize database:**
   ```sql
   OPTIMIZE TABLE jobs;
   OPTIMIZE TABLE failed_jobs;
   ```

---

## **✅ Verification Checklist**

- [ ] `.env` has `QUEUE_CONNECTION=database`
- [ ] Migrations run: `php artisan migrate`
- [ ] Cron job set up in cPanel
- [ ] Test order email sent successfully
- [ ] Test newsletter email sent successfully
- [ ] Import job processes correctly
- [ ] No failed jobs in queue

---

## **📞 Support**

If you encounter issues:

1. Check `storage/logs/laravel.log` for errors
2. Verify cron job is running
3. Check database connection
4. Verify queue tables exist
5. Test with a simple job first

---

## **🎯 Summary**

Your queue system is now:
- ✅ Configured for shared hosting
- ✅ Optimized with separate queues
- ✅ Processing automatically via cron
- ✅ Ready for production use

**Next Steps:**
1. Set up cron job in cPanel
2. Test with a test order
3. Monitor for 24 hours
4. Set up weekly failed job review

---

**Last Updated:** {{ date('Y-m-d') }}
**Version:** 1.0
