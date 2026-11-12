# Lucky Udaan Foundation — Website & Admin Dashboard

A single Laravel 12 application containing the public NGO website (27 pages) and a custom admin dashboard (17 modules) for **Lucky Udaan Foundation**, Gonda, Uttar Pradesh.

- **Stack:** PHP 8.2+, Laravel 12, Blade, Bootstrap 5 (custom SCSS), Vite, MySQL / MariaDB
- **No admin packages** (no Filament/Nova/Voyager). Auth uses Laravel's own session guard (`admin`), with roles and per-module permissions.
- **Shared-hosting ready:** built CSS/JS are committed in `public/build`; uploads go to `public/uploads` (no symlink needed); queue runs `sync`.

---

## 1. Local setup

```bash
composer install
cp .env.example .env          # then set DB_* and APP_URL
php artisan key:generate
php artisan migrate --seed
php artisan storage:link      # optional — uploads use public/uploads, so this is not required
php artisan serve
```

Open http://127.0.0.1:8000 for the website and http://127.0.0.1:8000/admin for the dashboard.

Front-end assets (only needed if you change SCSS/JS):

```bash
npm install
npm run build      # or: npm run dev
```

### Default admin logins

| Role        | Email                                | Password          | Access |
|-------------|--------------------------------------|-------------------|--------|
| Super Admin | admin@luckyudaanfoundation.com       | `LuckyUdaan@2026` | Everything |
| Editor      | editor@luckyudaanfoundation.com      | `LuckyUdaan@2026` | Content, banners, programs, campaigns, blog, events, gallery, team, volunteers, members, enquiries |
| Accountant  | accounts@luckyudaanfoundation.com    | `LuckyUdaan@2026` | Donations, donors, campaigns, reports & analytics |

**Change all three passwords immediately after the first login** (top-right menu → My profile). Roles and their module access can be edited under *Administration → Roles & Permissions*.

### What the seeders create

| Seeder | Content | Runs in production? |
|---|---|---|
| `AdminSeeder` | 3 roles + 3 admin users | Yes |
| `SettingSeeder` | All site settings (name, contact, address, colours, SEO…) | Yes — never overwrites edited values |
| `PageSeeder` | Text + SEO for all 27 pages, incl. Privacy / Terms / Refund policies | Yes — never overwrites edited pages |
| `ProgramSeeder` | 14 programs from the foundation's objectives | Yes |
| `ContentSeeder` | Sample banners, campaigns, posts, events, gallery, FAQs, testimonials, stories, certifications, openings | Yes (sample content — review before launch) |
| `DemoActivitySeeder` | Fictional donors, donations, volunteers, members, enquiries | **No** — skipped when `APP_ENV=production` |

---

## 2. Project structure

```
app/
  Http/Controllers/Site/        Public pages (Home, Page, Content, Form, Sitemap)
  Http/Controllers/Admin/       Dashboard: ResourceController (shared CRUD) + one controller per module
  Http/Requests/                Form requests for every public form + admin login
  Http/Middleware/              AdminCan (per-module permission), EnsureAdminIsActive, Honeypot
  Models/                       Eloquent models (+ Concerns/HasSlug, HasStatus)
  Support/                      Seo, Uploader (WebP + resize), Html (sanitiser), SettingsSchema, Options
  helpers.php                   setting(), upload_url(), inr(), seo() …
config/admin.php                Sidebar menu, permission modules, program icon list
routes/web.php                  Public website
routes/admin.php                Dashboard (prefixed /admin, guarded by auth:admin)
resources/views/layouts/        site.blade.php, admin.blade.php
resources/views/site/           Public pages, partials
resources/views/admin/          Dashboard; resource/index + resource/form render every CRUD module
resources/views/components/     Reusable Blade components (cards, page hero, form field, image …)
resources/scss/                 _config.scss (brand + breakpoints), site.scss, admin.scss
```

### Database tables

`admins`, `roles`, `admin_password_reset_tokens`, `settings`, `pages`, `banners`, `programs`, `campaigns`, `posts` (type = blog / news / announcement / press / media), `events`, `gallery_items`, `team_members`, `testimonials`, `success_stories`, `faqs`, `certifications`, `reports`, `partners`, `openings` (jobs + internships), `donors`, `donations`, `volunteers`, `members`, `enquiries` (contact / partnership / career / internship), plus Laravel's `sessions`, `cache`, `jobs`.

### Dashboard modules → where to find them

| # | Module | Sidebar |
|---|---|---|
| 1 | Dashboard overview (stats, charts, recent donations/enquiries) | Dashboard |
| 2 | Website content (pages, policies, testimonials, stories, FAQs, certifications, reports, partners, jobs) | Website → Content |
| 3 | Homepage banners | Website → Homepage Banners |
| 4–6 | Programs, Campaigns, Blog & News (incl. announcements, press, media) | Website / Fundraising |
| 7–8 | Donations (with printable receipts), Donors | Fundraising |
| 9–13 | Events, Volunteers, Members, Gallery, Team | Website / People |
| 14 | Reports & analytics (date filters, CSV + PDF export) | Administration |
| 15 | Login, logout, forgot/reset password, lockout after 5 failed attempts | `/admin/login` |
| 16 | Roles & per-module permissions | Administration |
| 17 | Website settings (site info, logo, contact, social, bank/UPI, legal numbers, homepage counters, SEO, brand colours) | Administration |

Every list has search, filters, sorting, pagination, an active/inactive toggle, edit/delete with a confirmation dialog and flash messages. Lists of people and donations also export to CSV.

---

## 3. Deploying to cPanel / shared hosting

Requirements: PHP 8.2+ with `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `gd` (WebP support), `zip`; MySQL 5.7+ or MariaDB 10.3+.

1. **Create the database** in cPanel → MySQL Databases (database + user, grant all privileges).
2. **Upload the project** (zip everything except `node_modules`, `.env`, and `vendor` if you can run Composer on the server) to a folder **outside** `public_html`, e.g. `/home/USER/luckyudaan`.
3. **Point the domain at `public/`.** Easiest: in cPanel → Domains, set the document root to `/home/USER/luckyudaan/public`.
   *If the document root cannot be changed:* copy the contents of `public/` into `public_html/`, then edit `public_html/index.php` and change the two paths to `__DIR__.'/../luckyudaan/vendor/autoload.php'` and `__DIR__.'/../luckyudaan/bootstrap/app.php'`. Then add `PUBLIC_PATH=/home/USER/public_html` to `.env` so uploads and the compiled assets are read from and written to `public_html`.
4. **Install dependencies.** Via cPanel Terminal / SSH: `composer install --no-dev --optimize-autoloader`. Without SSH, run that locally and upload the `vendor` folder.
5. **Configure `.env`:** copy `.env.example` → `.env`, then set `APP_URL=https://luckyudaanfoundation.com`, `APP_ENV=production`, `APP_DEBUG=false`, the `DB_*` values, and SMTP mail settings (`MAIL_MAILER=smtp`, host, port, username, password). Run `php artisan key:generate`.
6. **Database:** `php artisan migrate --seed --force`
7. **Optimise:** `php artisan config:cache && php artisan route:cache && php artisan view:cache`
8. **Permissions:** `storage/`, `bootstrap/cache/` and `public/uploads/` must be writable (755 / 775).
9. **HTTPS:** enable AutoSSL in cPanel, then uncomment the HTTPS redirect in `public/.htaccess` if you want to force it.
10. Sign in at `/admin`, change the default passwords, and fill in **Website Settings → Donation & legal**.

Node.js is **not** needed on the server — compiled assets are in `public/build`. After changing SCSS/JS locally, run `npm run build` and upload `public/build` again.

> `robots.txt` and `sitemap.xml` are generated dynamically. On non-production environments `robots.txt` blocks all crawlers and pages carry `noindex`, so a staging copy will not be indexed.

---

## 4. Security notes

- CSRF protection on every form; all input validated with form requests / rules.
- Passwords hashed (bcrypt); admin login rate-limited (5 attempts per email+IP, 5-minute lockout); deactivated admins are signed out.
- Every dashboard route checks the admin guard **and** the module permission (`admin.can:<module>` middleware + `module` gate).
- Public forms: honeypot field, time-trap (submissions under 3 seconds are discarded) and 5-per-minute rate limit.
- Uploads: type + size validated, images re-encoded to WebP with GD (strips embedded scripts/EXIF), random file names. CVs are stored privately in `storage/app/private` and downloadable only from the dashboard.
- Rich text from the editor is sanitised (scripts, inline handlers, `javascript:` URLs and non-YouTube/Maps iframes removed).

---

## 5. Content still needed from the client

See **HANDOVER.md** for the checklist.
