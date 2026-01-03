# Google Drive URL Scheduler Setup

## Overview
The system automatically fetches Google Drive URLs from configured folders using Laravel's task scheduler.

## Command Details
- **Command**: `php artisan gd:fetch`
- **Schedule**: Daily at 2:00 AM
- **Description**: Fetches and syncs Google Drive URLs from all configured folders

## Manual Execution
You can manually run the fetch command at any time:
```bash
php artisan gd:fetch
```

## Scheduler Configuration

### Schedule Options
You can modify the schedule in `app/Console/Kernel.php`:

```php
// Daily at 2:00 AM (default)
$schedule->command('gd:fetch')->dailyAt('02:00');

// Every hour
$schedule->command('gd:fetch')->hourly();

// Every 6 hours
$schedule->command('gd:fetch')->everySixHours();

// Every day at midnight
$schedule->command('gd:fetch')->daily();

// Twice daily at 1:00 AM and 1:00 PM
$schedule->command('gd:fetch')->twiceDaily(1, 13);

// Weekly on Monday at 8:00 AM
$schedule->command('gd:fetch')->weeklyOn(1, '8:00');
```

## Server Setup

### Option 1: URL-Based Cron (Easiest for Shared Hosting)

**Step 1:** Set your CRON_TOKEN in `.env` file:
```env
CRON_TOKEN=your_random_secure_token_here
```

**Step 2:** Use a cron service to call this URL every minute:
```
https://onstream.cloud/cron/run?token=your_random_secure_token_here
```

**Recommended Services:**
- [cron-job.org](https://cron-job.org) - Free, reliable
- [EasyCron](https://www.easycron.com) - Free tier available
- [cPanel Cron with wget/curl](#cpanel-wget-method)

**cPanel Setup with wget:**
```bash
* * * * * wget -q -O- "https://onstream.cloud/cron/run?token=your_random_secure_token_here" > /dev/null 2>&1
```

**cPanel Setup with curl:**
```bash
* * * * * curl -s "https://onstream.cloud/cron/run?token=your_random_secure_token_here" > /dev/null 2>&1
```

### Option 2: Traditional Server Cron

### For Linux/cPanel
Add this cron job to your server (runs every minute to check scheduled tasks):
```
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

### For Windows (XAMPP/Local Development)
1. Open Task Scheduler
2. Create a new task
3. Set trigger: Daily, repeat every 1 minute
4. Set action: Run program
   - Program: `C:\xampp\php\php.exe`
   - Arguments: `artisan schedule:run`
   - Start in: `C:\xampp\htdocs\onstream_19Dec25`

### For Shared Hosting
1. Go to cPanel > Cron Jobs
2. Add new cron job:
   - Minute: `*`
   - Hour: `*`
   - Day: `*`
   - Month: `*`
   - Weekday: `*`
   - Command (use one of these formats):

**Option 1 (Recommended):**
```bash
* * * * * cd /home/u559276167/domains/onstream.cloud/public_html && /usr/bin/php artisan schedule:run >> /dev/null 2>&1
```

**Option 2:**
```bash
* * * * * /usr/bin/php /home/u559276167/domains/onstream.cloud/public_html/artisan schedule:run >> /dev/null 2>&1
```

**For cPanel File Manager path:**
If your Laravel project is in `public_html`, use:
```bash
* * * * * cd /home/u559276167/public_html && /usr/bin/php artisan schedule:run >> /dev/null 2>&1
```

**Important Notes:**
- Replace `/home/u559276167/domains/onstream.cloud/public_html` with your actual full project path
- Replace `/usr/bin/php` with the correct PHP path (check with `which php` in SSH)
- Common PHP paths: `/usr/bin/php`, `/usr/local/bin/php`, `/opt/cpanel/ea-php83/root/usr/bin/php`

## What It Does
1. Reads API key and folder IDs from settings table
2. Connects to Google Drive API
3. Fetches all files from each configured folder (with pagination)
4. Adds new files to the database
5. Updates existing files
6. Marks files as "used" if they're already assigned to movies
7. Logs all activities

## Logs
Check logs at: `storage/logs/laravel.log`

Look for entries starting with:
- `GD Fetch Cron: ...`

## Troubleshooting

### Command Not Running
1. Check if Laravel scheduler cron is set up on server
2. Run `php artisan schedule:list` to see scheduled commands
3. Check `storage/logs/laravel.log` for errors

### No Files Being Fetched
1. Verify API key and folder IDs in Admin > API URLs > Google Drive Settings
2. Check folder permissions in Google Drive (must be publicly accessible)
3. Run manually: `php artisan gd:fetch` to see error messages

### Testing
Test the scheduler is working:
```bash
# See all scheduled tasks
php artisan schedule:list

# Run scheduler manually
php artisan schedule:run

# Run GD fetch command directly
php artisan gd:fetch
```
