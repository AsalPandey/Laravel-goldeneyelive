# Hostinger Shared Hosting Deployment Guide

## Scope

This guide is for deploying this Laravel project to a Hostinger shared hosting account with an existing domain.

This release includes a ready app ZIP, the main MySQL `.sql` import, and one small patch SQL for the current course ordering migration. Do not run seeders after importing the provided SQL unless you intentionally want to refresh checkpoint CMS content.

Official Hostinger references:

- Composer on Hostinger: https://www.hostinger.com/support/5792078-how-to-use-composer-at-hostinger
- SSH access: https://www.hostinger.com/support/1583645-how-to-enable-ssh-access-in-hostinger
- PHP version in hPanel: https://support.hostinger.com/en/articles/1575755-how-to-change-the-php-version-of-your-hostinger-hosting-plan
- Laravel support and cron example: https://www.hostinger.com/support/1583301-which-laravel-versions-are-supported-at-hostinger
- Manual Laravel deployment pattern: https://www.hostinger.com/support/?p=956

## Current Project Requirements

- PHP: `^8.4` from `composer.json`.
- Laravel: `^13.0`.
- Build output: `public/build`.
- Public document root should point to Laravel `public`.
- Database: MySQL/MariaDB through Hostinger hPanel.

Hostinger docs show PHP version is changed from hPanel PHP Configuration. Before deployment, confirm your hosting plan offers PHP 8.4 or newer. If PHP 8.4+ is not available, do not deploy this project to that plan without changing hosting/runtime.

## Recommended Folder Layout

Best structure:

```text
domains/yourdomain.com/
  goldeneye-app/        # Laravel application files
  public_html/          # contents of Laravel public/ OR symlink/document-root target
```

Preferred if Hostinger lets you set domain root:

```text
domains/yourdomain.com/goldeneye-app/public
```

If Hostinger shared hosting forces `public_html`, copy the contents of Laravel `public/` into `public_html/` and adjust `public_html/index.php` paths to point to the app folder.

## Pre-Deployment Local Checklist

Run locally before upload:

```bash
composer install
npm install
npm run build
php artisan test --compact
vendor/bin/pint --dirty --format agent
```

Expected current QA for the 2026-05-04 release:

```text
94 tests passing / 373 assertions
Vite build passing
PHP syntax checks passing
Composer validation passing
Pint passing
HTTP smoke passing
```

## Release Files

Use these generated files from the local `release/` folder:

```text
goldeneye_hostinger_app_20260504-final.zip
goldeneye_hostinger_release_20260504-080254.sql
goldeneye_hostinger_patch_display_order_20260504.sql
hostinger.env.template
```

The ZIP already includes `vendor/` and `public/build/`, so it can be uploaded through Hostinger File Manager without running Composer or npm on the server. It excludes local `.env`, `.git`, `node_modules`, tests, scratch files, and the SQLite database.

## Upload Steps

1. In Hostinger hPanel, add or confirm the domain.
2. Set PHP version to 8.4 or newer.
3. Enable SSH if your plan supports it.
4. Create a MySQL database and database user in hPanel.
5. Import `goldeneye_hostinger_release_20260504-080254.sql` with phpMyAdmin.
6. Import `goldeneye_hostinger_patch_display_order_20260504.sql` with phpMyAdmin.
7. Upload and extract `goldeneye_hostinger_app_20260504-final.zip`.
8. Create `.env` from `hostinger.env.template` or `.env.example`.
9. Ensure `storage/`, `bootstrap/cache/`, and uploaded asset directories are writable.

## Install Dependencies on Server

The provided release ZIP already includes `vendor/`. You do not need Composer for the File Manager deployment path.

If deploying from Git instead of the ZIP, Hostinger provides Composer 1 and Composer 2 on many Web/Cloud plans. Their docs recommend `composer2` for PHP 8+.

From the Laravel app directory:

```bash
composer2 install --no-dev --optimize-autoloader
```

If `composer2` is unavailable:

```bash
composer install --no-dev --optimize-autoloader
```

## Configure `.env`

Create `.env` from `release/hostinger.env.template` or `.env.example` and set:

```env
APP_NAME="GoldenEye Academy"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_hostinger_db
DB_USERNAME=your_hostinger_user
DB_PASSWORD=your_secure_password

MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email_user
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=info@yourdomain.com
MAIL_FROM_NAME="GoldenEye Academy"
```

Then run from the Laravel app directory if SSH/Terminal is available:

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

Run `php artisan key:generate --force` only if `APP_KEY` is still empty. If you imported both the main SQL and patch SQL, do not run `php artisan migrate --force`; the imported database already contains the migrated schema and seeded CMS content.

## Seeder Decision

The provided SQL already contains checkpoint CMS content and admin/staff users.

Only when you intentionally want to reload checkpoint CMS content:

```bash
php artisan db:seed --force
```

The seeders are designed to be non-destructive and idempotent. They use stable keys/slugs and do not truncate tables.

If you want only public content without users/roles, use:

```bash
php artisan db:seed --class=LiveSiteSeeder --force
```

For a full admin-ready checkpoint, use the default `DatabaseSeeder`.

Default seeded admin:

```text
Email: admin@goldeneye.edu.np
Password: password
```

Change this password immediately after first login.

## Point Domain to Laravel Public

If you can set document root:

```text
your app path/public
```

If using `public_html`, copy Laravel `public` contents into `public_html` and edit `public_html/index.php`:

```php
require __DIR__.'/../goldeneye-app/vendor/autoload.php';
$app = require_once __DIR__.'/../goldeneye-app/bootstrap/app.php';
```

Adjust `../goldeneye-app/` to the actual app folder name.

## Storage Link

If the app uses Laravel public storage:

```bash
php artisan storage:link
```

If symlinks are blocked on shared hosting, manually copy or configure uploads to `public/site/img` because this project already uses public asset paths for CMS images.

## Cache for Production

After `.env`, database, and domain are correct:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

If any route-cache error appears because of closures, skip `route:cache` and use:

```bash
php artisan config:cache
php artisan view:cache
```

## Cron / Scheduler

Hostinger documents Laravel scheduler cron in this form:

```bash
/usr/bin/php /home/u12345678/domains/domain.tld/public_html/artisan schedule:run
```

For this project, use the real path to `artisan`. If your Laravel app is outside `public_html`, point cron to that location.

## Queue

This app queues contact emails in some flows. On shared hosting, long-running queue workers may not be reliable.

Options:

- Set `QUEUE_CONNECTION=sync` for simple shared hosting deployment.
- If Hostinger plan supports persistent processes, configure a queue worker.
- If using cron, run a limited queue command periodically:

```bash
php artisan queue:work --stop-when-empty --tries=3
```

## Permissions

Ensure these are writable:

```text
storage/
bootstrap/cache/
public/site/img/
public/site/img/blog/
public/site/img/courses/
public/site/img/categories/
public/site/img/teachers/
public/site/img/testimonials/
public/site/img/notices/
```

## Post-Deployment QA

Check these URLs:

```text
/
/courses-all
/blog
/about
/contact
/join-now?course=undecided
/admin/dashboard
/sitemap.xml
/robots.txt
```

Submit one test contact inquiry and one test course-help request. Confirm:

- Database row is created.
- Admin submissions page shows the lead.
- Email is sent or logged according to mail config.
- WhatsApp CTA opens with prefilled message.
- Public pages use seeded CMS content.

## Go-Live Safety

- Set `APP_DEBUG=false`.
- Use HTTPS domain in `APP_URL`.
- Change seeded admin password.
- Clear old caches after final `.env` changes.
- Do not keep `phpinfo.php` or test files on the server.
- Back up database before future seeding or CMS imports.
