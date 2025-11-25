# TripEase

[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-blue.svg)](https://www.php.net)
[![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-orange.svg)](https://www.mysql.com)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple.svg)](https://getbootstrap.com)
[![License: MIT](https://img.shields.io/badge/License-MIT-success.svg)](LICENSE)

TripEase is a small-but-ambitious travel booking platform. Travelers can reserve boats or rooms, service providers can manage their listings, and admins keep everything tidy. The project is written in plain PHP with a MySQL database, so it is easy to host on any XAMPP/LAMP stack.

---

## Overview

TripEase focuses on being understandable first and fancy later. The codebase is organized by role (traveler, provider, admin) and by shared helpers (config, includes, assets). Each layer uses the same query builder and authentication helper so you only configure things once.

| Role     | Highlights                                                           |
| -------- | -------------------------------------------------------------------- |
| Traveler | Browse/search listings, book trips, manage reviews, update profile   |
| Provider | Build listings, upload photos, handle bookings, watch simple stats   |
| Admin    | Approve providers, review listings, monitor activity, tweak settings |

### Feature Highlights

- Multi-role authentication with separate dashboards
- Listing search with filters for category, price, location, and dates
- Booking workflow with confirmation, cancellation window, and receipts
- Review + rating system to keep listings honest
- Secure upload flow (MIME check, max size, clean file names)
- PDO-based query builder and helper functions for repetitive tasks
- Responsive UI using Bootstrap 5, custom CSS, and vanilla JS widgets

### Tech Stack & Tooling

- **Backend:** PHP 7.4+, PDO, custom query builder, Auth helper
- **Database:** MySQL 5.7+, schema + demo data under `database/`
- **Frontend:** HTML5, Bootstrap 5.3, Font Awesome 6, modular CSS/JS
- **Dev Tools:** Composer, PHPUnit 9, VS Code (recommended), Git
- **Server:** Works on XAMPP/WAMP/LAMP, Apache 2.4+ with mod_rewrite enabled

---

## Project Layout

```
TripEase/
├── config/          # Base config + database connection helper
├── includes/        # Auth class, shared header/footer, helpers
├── assets/          # CSS / JS / images for the UI
├── user/            # Traveler dashboard pages
├── provider/        # Provider dashboard pages
├── admin/           # Admin dashboards + reports
├── database/        # schema.sql and optional demo-data.sql
├── tests/           # PHPUnit config, fixtures, and suites
├── uploads/         # User, provider, and listing images (keep writable)
└── *.php            # Public pages (index, search, login, etc.)
```

Detailed docs live inside `all mds/` if you need deep dives on specific modules.

---

## Before You Start

- PHP 7.4 or newer
- MySQL 5.7 or newer
- Apache / Nginx with PHP enabled (XAMPP is easiest)
- Composer (for installing PHPUnit + autoloading)
- Node is **not** required

Hardware-wise, 2 GB RAM and ~500 MB disk space are fine for local testing.

---

## Installation (Localhost)

1. **Clone or download** the project into your web root (e.g., `C:\xampp\htdocs\TripEase`).
   ```powershell
   git clone https://github.com/sisayeedcse/TripEase---Online-Booking-System.git TripEase
   ```
2. **Install PHP dependencies** (mainly PHPUnit + autoloaders).
   ```powershell
   cd C:\xampp\htdocs\TripEase
   composer install
   ```
3. **Create the database.**
   - Open `http://localhost/phpmyadmin`
   - Create a database named `tripease` (utf8mb4_unicode_ci)
   - Import `database/schema.sql`
   - (Optional) import `database/demo-data.sql` for sample listings
4. **Update config.**
   Edit `config/config.php` and double-check:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', ''); // XAMPP default
   define('DB_NAME', 'tripease');
   define('APP_URL', 'http://localhost/TripEase');
   ```
   Set your timezone/currency while you are here.
5. **Give uploads write access.**
   - Windows: make sure the folder is not read-only
   - Linux/macOS: `chmod -R 775 uploads`
6. **Start Apache + MySQL** from XAMPP (or your stack of choice).
7. Visit `http://localhost/TripEase` and log in with the default accounts.

### Default Accounts

| Role     | Email                   | Password   |
| -------- | ----------------------- | ---------- |
| Admin    | `admin@tripease.com`    | `password` |
| Provider | `boatking@tripease.com` | `password` |
| Traveler | `traveler@tripease.com` | `password` |

Please change these right away in a real deployment.

---

## Configuration Checklist

- [ ] Update `APP_URL`, timezone, and currency inside `config/config.php`
- [ ] Set a **strong** admin password (`admins` table)
- [ ] Adjust upload size and allowed MIME types if needed
- [ ] Enable HTTPS + secure cookies when deploying live
- [ ] Turn off `display_errors` on production servers

Extra SMTP constants are already in the config for when you hook up email later.

---

## Running Tests

PHPUnit comes via Composer. Helpful scripts from `composer.json`:

```powershell
# Run everything
composer test

# Only unit tests
composer test-unit

# Only integration tests
composer test-integration

# HTML coverage report in tests/coverage
composer test-coverage
```

Before running the integration suite, execute `tests/clean-test-data.php` so you start with a fresh DB snapshot.

---

## Troubleshooting

- **White screen / blank page:** enable `display_errors` in `php.ini` while developing and check the Apache error log.
- **Cannot upload images:** confirm the `uploads/` subfolders exist and have write permission.
- **Login keeps redirecting:** clear browser cookies or delete old `sessions` if you are mixing HTTP and HTTPS.
- **Database connection failed:** double-check the credentials in `config/config.php` and verify the MySQL service is running.
- **Pretty URLs not working:** ensure `mod_rewrite` is enabled and `.htaccess` wasn’t removed.

---

## Roadmap (Short Version)

1. Finish user/provider/admin dashboard flows (bookings, analytics, notifications)
2. Add password reset + email notification support
3. Integrate payment gateway and SMS updates
4. Ship wishlists, calendar sync, and richer analytics
5. Explore mobile companion app + public API

See `all mds/ROADMAP*.md` for the long-form plans.

---

## Contributing

Friendly contributions are welcome:

1. Fork and clone the repo
2. Create a feature branch (`feature/my-update`)
3. Make your changes + add simple tests when possible
4. Run `composer test`
5. Open a Pull Request describing what changed and why

Please follow PSR-12 for PHP and keep comments short but helpful. Security fixes should be sent privately to `support@tripease.com` first.

---

## License

TripEase is released under the [MIT License](LICENSE). You can use it commercially, remix it, and ship your own flavor. Just keep the copyright notice.

---

## Need More Info?

- Quick install: `all mds/QUICKSTART.md`
- Testing guide: `all mds/TESTING_GUIDE.md`
- UI notes: `all mds/MODERN_UI_COMPLETE.md`
- Full docs: browse everything inside `all mds/`

If you get stuck, open an issue on GitHub or email `support@tripease.com`.

---

Thanks for checking out TripEase! Even if you are a beginner, feel free to poke around the code—each section is small and commented so you can learn as you go.
