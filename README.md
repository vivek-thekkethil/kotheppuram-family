# Kotheppuram Family Portal

A Laravel 12 web application for managing family members, celebrations, gallery, events/news, family history, and contact communication with an admin dashboard.

## Tech Stack

- PHP 8.2+
- Laravel 12
- MySQL or SQLite
- Vite + Tailwind CSS
- Twilio WhatsApp API (optional)

## Core Features

### Public Website
- Home, Gallery, Events, Members, Family History, Contact Us pages
- Contact form with DB storage + styled email notification

### Admin Panel
- Admin login at `/admin`
- Dashboard with:
	- overall counts
	- member analytics (gender split, married, under 18, age groups, data coverage)
	- upcoming alerts
	- recent contact messages
- Member management (CRUD, relationships, photo/crop support)
- Gallery management (multi-upload + delete)
- Events & News management
- Landing slide management
- Family history management
- Contact message inbox
- Custom messaging to selected/all members:
	- channel: Email / WhatsApp / Both
	- subject + description
	- optional attachment

### Celebration Notifications
- Daily birthday/anniversary notification command:
	- email to members
	- optional WhatsApp send
	- dedupe per day using cache
- Scheduled automatically via Laravel scheduler.

## Local Setup

```bash
git clone <your-repo-url>
cd family
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run dev
php artisan serve
```

Open: `http://127.0.0.1:8000`

## Admin Access

Seed default admin:

```bash
php artisan db:seed
```

Default seeded admin (from `DatabaseSeeder`):
- Email: `viveknair97k@gmail.com`
- Password: `12345678`

Login URL: `/admin`

## Useful Commands

Run tests:

```bash
php artisan test
```

Run celebration notifications manually:

```bash
php artisan family:send-daily-celebrations
```

Preview without sending:

```bash
php artisan family:send-daily-celebrations --dry-run
```

Force resend for same day:

```bash
php artisan family:send-daily-celebrations --force
```

## Scheduler (Production)

Configured in `routes/console.php`:
- `family:send-daily-celebrations` runs daily at `FAMILY_NOTIFICATION_RUN_AT`.

Make sure server cron runs Laravel scheduler every minute:

```bash
* * * * * php /path-to-project/artisan schedule:run >> /dev/null 2>&1
```

## Environment Variables

Set these in `.env` as needed:

```env
FAMILY_NOTIFICATION_EMAILS=
FAMILY_NOTIFICATION_UPCOMING_DAYS=7
FAMILY_NOTIFICATION_RUN_AT=08:00

FAMILY_WHATSAPP_ENABLED=false
FAMILY_WHATSAPP_DEFAULT_COUNTRY_CODE=

TWILIO_ACCOUNT_SID=
TWILIO_AUTH_TOKEN=
TWILIO_WHATSAPP_FROM=
```

Mail configuration must also be set (`MAIL_MAILER`, `MAIL_HOST`, etc.) for email features.

## Notes

- Admin routes are protected by auth + admin middleware alias (`admin`).
- Uploaded files are stored under `public/uploads/*`.
- Email templates are in `resources/views/emails`.

---

© 2026 Kotheppuram Family. All rights reserved.