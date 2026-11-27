# OnThrill

Website and admin panel for pilgrimage and cultural tours: the temples of Nepal, the Chardham of Uttarakhand and Kailash Mansarovar in Tibet, for travellers from India and Nepal. Every package is priced in Indian rupees (INR) and Nepali rupees (NPR).

Built with **Laravel 13 · MySQL · Filament 5** (admin panel and JSON API) and an **Angular 22** website (`frontend/`, Tailwind CSS 4).

How the two fit together: every public address (`/`, `/packages/...`, `/destinations/...`) is still a Laravel route. Laravel sends the page's title, description, sharing tags, structured data and a plain outline of the content (what search engines and WhatsApp/Facebook previews read), plus the page's data. The Angular app, built into `public/app`, then draws the page and handles everything after that in the browser, reading `/api/...`. Sign-in, bookings and enquiries use the same session and CSRF protection as before.

---

## Run it locally (Windows + XAMPP)

Requirements: PHP 8.3+, Composer, Node 20.19+ (22 or 24 recommended), MySQL/MariaDB (XAMPP is fine).

```bash
composer install
npm install                   # also installs the Angular app in frontend/
cp .env.example .env          # then check the DB_* values
php artisan key:generate
```

Create the database (phpMyAdmin, or):

```bash
mysql -u root -e "CREATE DATABASE onthrill CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
```

Then:

```bash
php artisan migrate --seed    # tables, roles, destinations, packages, pages, photos; prints the admin password
php artisan storage:link
npm run build                 # builds the Angular website into public/app
composer serve                # http://127.0.0.1:8001
```

- Website: http://127.0.0.1:8001
- Admin panel: http://127.0.0.1:8001/admin

Port 8001 is the default so the site can run next to Online Poojan (port 8000). For another port: `php serve.php 127.0.0.1 8080`, and set `APP_URL` in `.env` to match.

Set `ADMIN_EMAIL` / `ADMIN_PASSWORD` in `.env` before seeding to choose the first admin login; otherwise a random password is printed once. Change it after logging in (top-right menu > Profile).

**Sign-in lasts until the browser is closed.** After closing the browser or restarting the PC, travellers and staff sign in again. Signing in also ends after 2 hours without activity (`SESSION_LIFETIME=120`). Set by `SESSION_EXPIRE_ON_CLOSE=true`.

**Forgotten passwords.** Both sign-in pages have "Forgot password?", which emails a reset link (valid once, for 60 minutes) once email is set up. Without email, on the server:

```bash
php artisan user:password admin@onthrill.com                      # prints a new random password
php artisan user:password admin@onthrill.com --password=MyNewPass1  # or choose one
```

## The website

| Address | What it shows |
|---|---|
| `/` | Photo slideshow with the tour search (destination, tour type, length), facts in numbers, featured destinations, packages with tour-type tabs, why us, how booking works, testimonials, gallery, questions, call to action |
| `/destinations` | All destinations, grouped by country |
| `/destinations/country/nepal` (`india`, `tibet`) | Destinations of one country |
| `/destinations/<slug>` | Key facts (altitude, best time, opening hours, nearest airport, how to reach, map link), description, photos, questions, and the packages that visit it |
| `/packages` | All packages with a filter sidebar: tour type, destination (`?destination=`) and length (`?duration=short`, `medium` or `long`); sort by price or length |
| `/packages/type/<slug>` | Packages of one tour type (Nepal Pilgrimage Tours, Chardham Yatra…) |
| `/packages/<slug>` | Facts, photos, overview, highlights, day-by-day itinerary, included / not included, before you go, places visited, questions, price box and booking form |
| `/booking/<reference>` | The traveller's booking, with payment details in their currency |
| `/contact-us`, `/gallery`, `/search` | Contact form and details, photo gallery, search |
| `/<slug>` | Pages from the admin (About Us, Travel Information, Privacy Policy, Terms, Photo Credits) |

The **INR / NPR** switch in the header changes every price on the site and is remembered in the visitor's browser.

## Bookings and payments

1. The traveller picks a date, the number of adults and children, and their nationality, then sends a **booking request**. Nothing is charged online.
2. They get a booking reference (`OT-YYMMDD-XXXXX`), a booking page and an email. The team gets an email too.
3. **Indian travellers** are quoted in INR and shown the UPI ID, UPI number and QR code (on phones, a button opens their UPI app with the amount filled in). **Nepali travellers** are quoted in NPR and shown eSewa, Khalti, the QR code and the Nepali bank account. Other nationalities get the currency selected in the header.
4. The estimate is price per person × travellers, with an advance (25% by default) to confirm. The team calls to confirm hotels, permits and the final price, and can change the price on the booking in the admin.
5. In the admin: **Confirm booking**, **Advance received**, **Paid in full**, record the UTR / transaction reference, and **WhatsApp traveller**.

NPR prices: a package can have its own NPR price; otherwise it is worked out from the INR price at the rate in **Site Settings > Prices & Payments** (1.6, the fixed INR–NPR rate), rounded to the nearest 10. Packages with no INR price show **Price on request** and can still be booked.

Staff accounts cannot book (the form is replaced by a notice), so test bookings need a customer account or no account.

## What the admin panel manages

| Section | What you can do |
|---|---|
| **Dashboard** | Booking requests this month, new bookings to confirm, travellers departing in the next 30 days, new enquiries, latest bookings |
| **Bookings > Bookings** | Traveller, tour, dates, price, status, payment, internal notes; quick actions and WhatsApp |
| **Bookings > Enquiries** | Contact-form and package enquiries; status and internal notes |
| **Tours > Packages** | Details, itinerary (one item per day), included / not included, before you go, questions, photos, INR and NPR prices with offer prices, tour type, places visited, trip facts, SEO, homepage switch, drag to reorder, duplicate |
| **Tours > Destinations** | Country, region, description, key facts, coordinates, questions, photos with credit, SEO, homepage switch. **The website menu is built from these**, by country and in this order. |
| **Tours > Tour Types & Menu** | Tour types shown in the *Tour Packages* menu and on `/packages/type/<slug>` |
| **Website > Page Content** | **All the wording on the website**, in tabs: Homepage (sections shown, slideshow photos, why us, booking steps, questions, call to action, search bar and button labels), Header & footer, Destinations (page titles, country intros, headings), Tour packages (filters, price box, booking form), Enquiries & contact (contact page, enquiry form, emails to travellers), Other pages (gallery, information page sidebar, booking confirmation, search, sign-in). A field left empty goes back to its original text. |
| **Website > Pages** | About Us, Travel Information, Privacy Policy, Terms, Photo Credits, and any new page; choose per page whether it appears in the header menu and/or footer |
| **Website > Testimonials / Gallery** | Reviews and photos (with photo credit) |
| **Settings > Site Settings** | Logos, backgrounds, footer text; phone, WhatsApp, email, address, map, social links; prices and payments (default currency, NPR rate, advance %, UPI, eSewa, Khalti, QR codes, bank accounts); email (SMTP, who gets notifications, *Send test email*); SEO (title, description, share image, business details for search engines, Google and Bing verification, Google Analytics) |
| **Settings > Users / Roles & Permissions** | Accounts and roles; tick exactly what each role may view, create, edit or delete |

## Roles

| Role | Access |
|---|---|
| **Super Admin** | Everything. Built-in; cannot be edited or deleted. |
| **Manager** | Bookings, enquiries, tours, pages, gallery, testimonials, page content, site settings; can see users but not change them or roles. |
| **Booking Staff** | View and update bookings and enquiries; look up packages and destinations (read-only). |
| **Content Editor** | Packages, destinations, tour types, pages, testimonials, gallery, page content. No bookings, users or settings. |
| **Customer** | Website only: books and sees their own bookings. Built-in. |

Only a Super Admin can make someone a Super Admin; nobody can change their own roles or delete their own account; the last Super Admin cannot be deleted. After an update: `php artisan access:sync`. If locked out: `php artisan access:sync --super-admin=you@example.com`.

## Search engines (SEO) and AI assistants (GEO)

Done for you:

- **One clear title and description per page**, a canonical address, Open Graph and X/Twitter cards (the page's own photo when shared on WhatsApp or Facebook), `lang="en-IN"`, one `<h1>` per page, breadcrumbs.
- **Structured data** (schema.org JSON-LD) that Google, Bing and AI assistants read:
  - every page: `TravelAgency` (name, contact, address and coordinates once filled in, social profiles);
  - homepage: `WebSite` with a search box;
  - destinations: `TouristDestination` + `PlaceOfWorship` with country, region, coordinates and altitude;
  - packages: `TouristTrip` with the itinerary day by day, the places visited and **offers in INR and NPR**;
  - questions: `FAQPage`; listings: `ItemList`; inner pages: `BreadcrumbList`.
- **`/sitemap.xml`** with every visible destination, package, tour type, country and page, its last-change date and its photo. Hidden items are left out automatically.
- **`/robots.txt`** points to the sitemap and keeps private pages (admin, account, bookings, search, sign-in) out of search results. Those pages also carry `noindex`.
- **`/llms.txt`**: a plain-text summary of the site (destinations by country, packages with length and price, information pages) for AI assistants.
- `geo.region`, `geo.placename` and `ICBM` tags once the office location is filled in.
- Fast pages: right-sized WebP images with `srcset`, lazy loading below the first screen, self-hosted fonts, a map that loads only when asked.

To do once the site is live on its domain:

1. **Site Settings > SEO**: add the registered business name, office town, region code (e.g. `NP-P3` for Bagmati) and office coordinates.
2. Add the site to **Google Search Console** and **Bing Webmaster Tools** (paste the verification codes in Site Settings > SEO), then submit `https://<your-domain>/sitemap.xml` in both.
3. Create a **Google Business Profile** for the office with the same name, address and phone as the website.
4. Add the **Google Analytics** ID (`G-…`) if you want visitor statistics.
5. Keep writing: each destination and package page ranks for its own name. Short, factual answers in the *Questions* tabs are what search results and AI assistants quote.

## Photos

The seeded photos come from **Wikimedia Commons** under Creative Commons or public-domain licences. Each photo's author and licence is stored with it and shown on the banner, in the gallery lightbox and on the **Photo Credits** page (linked in the footer). Creative Commons licences require this credit: keep it when you reuse a photo, and add one when you upload another Commons photo. Your own photos need no credit; just leave the credit fields empty.

## Emails

- **Booking request:** to the traveller (with the payment details in their currency) and to the booking notification address.
- **Enquiry:** to the notification address, plus an automatic reply to the traveller (can be switched off).

Set up sending in **Settings > Site Settings > Email** (SMTP from your host, Google Workspace or Gmail with an app password, Zoho, Brevo), save, then **Send test email**. Until then, emails are written to `storage/logs/laravel.log`. Bookings and enquiries are always saved, even if an email fails.

## Before launch checklist

- [ ] **Contact details** (Site Settings > Contact & Social): phone, WhatsApp, office address, hours. They are empty on purpose; the website leaves out any line that is not filled in.
- [ ] **Payment details** (Site Settings > Prices & Payments): UPI ID, number and QR code; eSewa / Khalti IDs and QR code; bank accounts in India and Nepal.
- [ ] **Package prices**: the seeded prices are samples. Check every package's INR price (and NPR price, if you do not want the 1.6 conversion). The two Kailash Mansarovar packages are *Price on request*.
- [ ] **Itineraries, inclusions and permits**: read each package against what you actually offer, especially the Kailash routes, where the rules for Indian passport holders change from season to season.
- [ ] **Pages**: About Us, Travel Information, Privacy Policy and Terms are drafts; add your company details, registration numbers and cancellation policy.
- [ ] **Coordinates** missing for Doleshwor Mahadev, Galeshwor Dham and Pumdikot Shiva Temple (Tours > Destinations > Key facts). Copy them from Google Maps.
- [ ] **Testimonials**: none are seeded, so the section stays hidden. Add real reviews from your travellers.
- [ ] **Email**: set up SMTP, send a test email, then make a test booking and a test enquiry from a customer account.
- [ ] Production `.env`: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://<your-domain>`.

## Deploying to a live server

1. Run `npm run build`, then upload the project (everything except `node_modules` and `frontend/node_modules`; `public/app` must be included: it is the website).
2. Point the domain's document root to the **`public/`** folder.
3. In `.env`: production settings as above, plus the database and mail settings.
4. Run:
   ```bash
   composer install --no-dev --optimize-autoloader
   php artisan migrate --seed --force   # first deploy only (use plain `migrate --force` afterwards)
   php artisan storage:link
   php artisan access:sync
   php artisan images:thumbs
   php artisan optimize
   php artisan filament:optimize
   ```
5. Install an SSL certificate (Let's Encrypt / cPanel AutoSSL). Search engines expect `https://`.
6. Enable OPcache in `php.ini` (`opcache.enable=1`, `opcache.memory_consumption=192`, `opcache.max_accelerated_files=20000`). Compression and browser caching for Apache are already in `public/.htaccess`.

## Useful commands

```bash
php artisan test                      # the automated tests
php artisan images:thumbs             # right-sized copies after adding images by hand
php artisan content:clean --dry-run   # tidy text pasted from Word or page builders (then without --dry-run)
npm run dev                           # rebuild the website (public/app) on every change; refresh the browser
npm run serve:app                     # or: Angular dev server on http://localhost:4200 (live reload), using the API on :8001
```

## Project structure

```
app/
  Http/Controllers/     ShellController (each page's SEO shell), SeoController (sitemap, robots, llms.txt)
  Http/Controllers/Api/ JSON for the website: Catalog, Content (pages, search, enquiries), Booking, Account
  Models/               Destination, Package, TourCategory, Booking, Inquiry, Testimonial,
                        GalleryImage, Page, Setting, User
  Support/              Settings, Menu (navigation from destinations and tour types),
                        Seo (structured data), Access (roles), Thumbnails, ContentCleaner,
                        Catalog (queries), Present (JSON shapes), SiteData (public settings + wording)
  Policies/             Who may view/create/edit/delete each section
  Filament/             Admin panel: Resources/, Pages/ (Site Settings, Page Content), Widgets/
  helpers.php           setting(), media_url(), inr(), npr(), money(), whatsapp_url(), clean_html()
config/site.php         Default values for every setting and page text
resources/views/       app.blade.php (the page shell), mail/, seo/, errors/403
frontend/src/app/       The Angular website
  core/                 site state (wording, currency, account), API helpers, SEO updates, types
  layout/               header (top bar, menu, search), footer, floating buttons
  shared/               cards, price, photo grid + viewer, forms, page banner, icons
  pages/                home/, destinations, destination, packages, package/, booking, contact,
                        gallery, search, cms-page, account/
frontend/src/styles.css Colours, fonts and shared styles (Tailwind CSS 4)
database/seeders/       DatabaseSeeder + data/ (destinations, packages, pages, photo credits) + assets/ (photos)
tests/                  `php artisan test`
```
