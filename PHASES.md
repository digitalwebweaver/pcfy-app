# pcfy.in — Laravel Project Phases

> **Project**: B2B corporate website for certified refurbished hardware (pcfy.in)
> **Stack**: Laravel 12, MySQL (MariaDB 10.4), Blade + Alpine.js, Spatie packages
> **Admin URL**: `/adminhu`
> **App URL**: `http://localhost/pcfy-app/public`
> **Last updated**: 2026-06-02 — ALL 7 PHASES COMPLETE 🎉

---

## Quick Start (Resuming After Restart)

```bash
# 1. Start XAMPP (Apache + MySQL)
# 2. Navigate to project
cd C:\xampp\htdocs\pcfy-app

# 3. Clear caches and restart
php artisan config:clear
php artisan cache:clear
php artisan route:cache
php artisan view:clear

# 4. Open browser
# http://localhost/pcfy-app/public/adminhu/login
# Email: admin@pcfy.in  |  Password: Admin@pcfy2026
```

---

## Credentials

| Item | Value |
|---|---|
| Admin login | admin@pcfy.in |
| Admin password | Admin@pcfy2026 |
| DB name | pcfy |
| DB user | root |
| DB password | Dww@123 |

---

## Phase Status Overview

| Phase | Name | Status |
|---|---|---|
| 1 | Foundation — Laravel setup, DB, admin shell | ✅ COMPLETE |
| 2 | Products — CRUD, categories, brands, bulk import | ✅ COMPLETE |
| 3 | Frontend — Blade templates (homepage, shop, product, cart) | ✅ COMPLETE |
| 4 | Quotes — Cart, submission, management, email, PDF | ✅ COMPLETE |
| 5 | Content — Blog, pages, menus, homepage sections | ✅ COMPLETE |
| 6 | SEO & Settings — Per-entity SEO, sitemap, global settings | ✅ COMPLETE |
| 7 | Polish — Analytics, contact inbox, WhatsApp, announcements | ✅ COMPLETE |

---

## Phase 1 — Foundation ✅ COMPLETE

**Goal**: Working Laravel project with admin shell, database, authentication, roles.

### What was built
- [x] Laravel 12 project created at `C:\xampp\htdocs\pcfy-app`
- [x] MySQL database `pcfy` created
- [x] `.env` configured (DB, mail, app URL, admin prefix)
- [x] All packages installed:
  - `spatie/laravel-permission` — roles & permissions
  - `spatie/laravel-medialibrary` — image management
  - `spatie/laravel-sluggable` — SEO slugs
  - `spatie/laravel-sitemap` — XML sitemap
  - `barryvdh/laravel-dompdf` — PDF quote generation
  - `maatwebsite/excel` — bulk CSV import
  - `intervention/image` — image processing
- [x] All database migrations created & run:
  - `users` (Laravel default + Spatie roles)
  - `roles`, `permissions`, `model_has_roles` (Spatie)
  - `media` (Spatie Media Library)
  - `settings` (global CMS settings key/value)
  - `brands` (product brands)
  - `categories` (nested product categories)
  - `products` (full product schema with grade, specs JSON, SEO)
  - `product_specs` (structured spec rows)
  - `quotes` (quote requests with status flow)
  - `quote_items` (line items per quote)
  - `quote_responses` (email thread per quote)
  - `contact_submissions` (contact form inbox)
- [x] Roles seeded: `super_admin`, `admin`, `sales`, `content`, `products`
- [x] 25 permissions seeded and assigned to roles
- [x] Super Admin user seeded (`admin@pcfy.in`)
- [x] Admin middleware (`AdminMiddleware`) — protects all `/adminhu/*` routes
- [x] Admin layout (`resources/views/admin/layouts/app.blade.php`):
  - Collapsible sidebar (220px) with full nav
  - Sticky header with breadcrumb, user chip, view site link
  - Mobile responsive (sidebar collapses to hamburger at 900px)
  - Alpine.js for sidebar toggle
  - Full CSS design system (tokens, cards, tables, forms, badges, pagination)
- [x] Admin login page (`/adminhu/login`)
- [x] Dashboard (`/adminhu`) with live stats (quotes, products, contacts)
- [x] User management (`/adminhu/users`) — list, create, edit, delete, assign role
- [x] All resource routes registered and route-cached
- [x] Storage link created (`public/storage → storage/app/public`)
- [x] Placeholder views for Phase 2–7 features (no broken routes)

### Key files
```
app/Http/Controllers/Admin/
  AuthController.php        — login, logout, profile
  DashboardController.php   — dashboard stats
  UserController.php        — full CRUD with role assignment

app/Http/Middleware/Admin/
  AdminMiddleware.php       — guards all /adminhu routes

app/Models/
  User.php                  — HasRoles trait added
  Quote.php, Product.php, Category.php, Brand.php
  ContactSubmission.php, Setting.php

resources/views/admin/
  layouts/app.blade.php     — main admin shell
  auth/login.blade.php      — login page
  dashboard/index.blade.php — dashboard
  users/index.blade.php     — user list
  users/create.blade.php    — add user
  users/edit.blade.php      — edit user
  users/profile.blade.php   — my profile
  placeholder.blade.php     — stub for unbuilt sections

routes/web.php              — all admin routes registered
database/seeders/DatabaseSeeder.php — roles + super admin
```

---

## Phase 2 — Products ✅ COMPLETE

**Goal**: Full product catalogue management with images, categories, brands, and bulk CSV import.

### What was built
- [x] **Brand CRUD** — name, slug, logo upload, active/inactive
- [x] **Category CRUD** — nested (parent/child), icon SVG, description, sort order, visibility toggle
- [x] **Product CRUD**:
  - Basic info (name, SKU, brand, category, grade A/B/C)
  - Pricing (price in ₹, MRP, auto-calculate discount %)
  - Stock status (In Stock / Low Stock / Out of Stock)
  - Warranty months
  - Short description + full HTML description
  - Quick specs chips (pipe-separated → JSON array)
  - Structured spec rows (group → label → value), dynamically add/remove
  - SEO fields (meta title, meta description)
  - Active / inactive toggle, Featured toggle
  - Sort order
- [x] **Product images** (Spatie Media Library):
  - Multiple image upload
  - Drag-to-reorder with SortableJS (first = primary)
  - Delete individual images via AJAX
  - Auto-generates thumb (300×220) and medium (800×600) conversions
- [x] **Bulk CSV/XLSX import** (Maatwebsite Excel):
  - Download sample CSV with column guide
  - Upload CSV — validates required fields per row
  - Auto-creates brands/categories if they don't exist
  - Downloads images from URL column automatically
  - Updates existing products if SKU matches
  - Returns per-row error report on partial failures
- [x] **Product list** — search, filter (category/brand/grade/stock), sort, paginated
- [x] **Bug fixed**: BOM encoding stripped from all PHP files written via PowerShell

### Key files created
```
app/Models/
  Brand.php, Category.php, Product.php, ProductSpec.php  — with traits

app/Http/Controllers/Admin/
  BrandController.php       — full CRUD
  CategoryController.php    — full CRUD (nested categories)
  ProductController.php     — full CRUD + images + import + sample CSV

app/Imports/
  ProductsImport.php        — Maatwebsite import with validation + image download

resources/views/admin/
  brands/index.blade.php, brands/form.blade.php
  categories/index.blade.php, categories/form.blade.php
  products/index.blade.php  — with search + filter bar
  products/form.blade.php   — tabbed form (Info / Specs / SEO) + image manager
  products/import.blade.php — upload + column guide + error report

routes/web.php              — 12 product routes + image delete/reorder + import/sample
```

### Notes
- Price stored in paise (₹ × 100) for precision, displayed in ₹ throughout UI
- Product slug is generated on create only (`doNotGenerateSlugsOnUpdate`) to preserve SEO URLs
- Image conversions run non-queued (sync) — move to queued for production
  - Image column supports URL (auto-downloads) or filename (from zip)
  - Validation report (rows imported / rows failed with reasons)
  - Duplicate SKU handling (skip or update)
- [ ] **Product list**:
  - Search by name/SKU
  - Filter by category, brand, grade, stock status
  - Bulk actions (activate, deactivate, delete)
  - Inline stock status toggle
  - Sort by name, price, date, stock

### Key files to create
```
app/Http/Controllers/Admin/
  ProductController.php    — full CRUD + import
  CategoryController.php   — full CRUD
  BrandController.php      — full CRUD

app/Imports/
  ProductsImport.php       — Maatwebsite Excel import class

resources/views/admin/
  products/index.blade.php
  products/create.blade.php
  products/edit.blade.php
  categories/index.blade.php
  categories/create.blade.php
  categories/edit.blade.php
  brands/index.blade.php
  brands/form.blade.php
```

---

## Phase 3 — Frontend (Blade Templates) ✅ COMPLETE

**Goal**: Replace the existing static HTML files with Laravel Blade templates pulling real data.

### What was built
- [x] CSS/JS assets copied to `public/css/` and `public/js/`
- [x] **`layouts/site.blade.php`** — full site shell: topbar, header (search + cart count), nav (from DB categories), mobile nav drawer, footer
- [x] **`ViewServiceProvider`** — shares `$siteCategories` globally to all site views (nav + footer + mobile drawer)
- [x] **`components/site/product-card.blade.php`** — reusable product card with add-to-quote form
- [x] **Homepage** (`/`) — real featured products + categories from DB, hero, grade scale, eco/B2B sections, newsletter
- [x] **Shop/Listing** (`/shop`, `/shop/{slug}`) — real products with sidebar filters (category, brand, grade, price range), sort, pagination, mobile toolbar + filter drawer + sort sheet
- [x] **Product Detail** (`/products/{slug}`) — real images (Spatie Media), spec accordion, grade selector, add-to-quote button, related products, sticky mobile buy bar
- [x] **Quote Cart** (`/quote/cart`) — session-based items list, qty update, remove, clear, cart count in header
- [x] **Contact page** (`/contact`) — basic contact form (full inbox in Phase 7)
- [x] **Bug fixed**: Blade `@foreach` with inline `['key'=>'val']` arrays causes PHP parse errors — extracted to `@php` blocks
- [x] All pages smoke-tested: `/` `/shop` `/contact` `/quote/cart` → all 200 OK

### Key files created
```
app/Http/Controllers/Site/
  HomeController.php         — featured products + categories
  ShopController.php         — listing with filters/sort
  ProductPageController.php  — product detail + related
  CartController.php         — session cart CRUD

app/Providers/
  ViewServiceProvider.php    — shares siteCategories to all views

resources/views/
  layouts/site.blade.php
  components/site/product-card.blade.php
  pages/home.blade.php
  pages/shop.blade.php
  pages/product.blade.php
  pages/cart.blade.php
  pages/contact.blade.php

public/css/   — base.css, home-b.css, shop.css, product.css (copied)
public/js/    — home-b.js, shop.js, product.js (copied)
```

### Notes
- Nav built from DB categories (top-level, visible, sort_order) — add categories in admin to populate nav
- Cart stores: product_id, product_name, grade, qty, price, thumb in session
- Product detail uses original product.css class names exactly (stage, buy, gopt, trustrow, etc.)
- Quote form placeholder on cart page — Phase 4 replaces it with full quote submission + email + quote number

### Key files to create
```
resources/views/
  layouts/site.blade.php        — frontend layout
  components/product-card.blade.php
  components/breadcrumb.blade.php
  pages/home.blade.php
  pages/shop.blade.php
  pages/product.blade.php

app/Http/Controllers/
  HomeController.php
  ShopController.php
  ProductController.php (frontend)
  CartController.php

public/css/          — copy from C:\xampp\htdocs\pcfy\css\
public/js/           — copy from C:\xampp\htdocs\pcfy\js\
```

---

## Phase 4 — Quotes ✅ COMPLETE

**Goal**: Complete quote system — frontend cart, submission, admin management, email responses, PDF.

### What was built
- [x] **Quote models** updated — Quote, QuoteItem, QuoteResponse with full relationships, scopes, computed attributes
- [x] **Quote number generation** — `PCF-YYYY-NNNNN` format, auto-increment per year
- [x] **Frontend quote submission** (`/quote/submit`) — form: name, email, phone, company, GSTIN, message; order summary panel
- [x] **Quote confirmation page** (`/quote/confirmation/{number}`) — quote number displayed prominently, what-happens-next steps, WhatsApp link
- [x] **Session cart** linked to real submit flow — "Request a Quote" button on cart page
- [x] **Email on submission** — `QuoteReceivedMail` sends branded Markdown email to customer with quote number + items table
- [x] **Admin quote list** (`/adminhu/quotes`) — status tabs (All/New/Reviewing/Quoted/Won/Lost) with counts, search, date range filter, agent filter
- [x] **Admin quote detail** (`/adminhu/quotes/{id}`) — customer info panel, items table with editable unit prices + qty, estimated total auto-calculated, status change (dropdown), assign to team member
- [x] **Response thread** — send email to customer OR add internal note (yellow, hidden from customer); full thread history displayed
- [x] **QuoteResponseMail** — sends admin reply to customer with quote reference
- [x] **WhatsApp quick-link** — pre-filled message with quote number, available on detail + confirmation pages
- [x] **PDF quote download** (`/adminhu/quotes/{id}/pdf`) — branded A4 PDF via DomPDF: logo, quote number, customer info, items table, totals, terms & conditions
- [x] **Auth middleware fix** — removed conflicting `auth` middleware, AdminMiddleware handles all auth checks

### Key files created
```
app/Http/Controllers/Site/QuoteSubmitController.php
app/Http/Controllers/Admin/QuoteController.php     — full implementation
app/Mail/QuoteReceivedMail.php
app/Mail/QuoteResponseMail.php
resources/views/quotes/submit.blade.php
resources/views/quotes/confirmation.blade.php
resources/views/admin/quotes/index.blade.php
resources/views/admin/quotes/show.blade.php
resources/views/pdf/quote.blade.php
resources/views/emails/quote-received.blade.php
resources/views/emails/quote-response.blade.php
```

### Notes
- Mail driver is `log` — emails appear in `storage/logs/laravel.log` in dev; configure SMTP in Phase 6 settings
- PDF uses DomPDF — prices shown as "—" if admin hasn't filled them in yet
- WhatsApp number is hardcoded in Quote model's `whatsapp_url` — update when real number added in Phase 6 settings
- `/quote/submit` redirects to cart if cart is empty (correct behavior)

### What was built
  - "Add to Quote" button on product cards/detail page
  - Floating cart icon with item count badge
  - Cart drawer / page showing all quoted items
  - Adjust quantity, remove item
  - Submit quote form (name, email, phone, company, GSTIN, message)
  - Quote confirmation page with unique quote number
  - Auto-email to customer on submission
- [ ] **Quote number generation**: `PCF-YYYY-NNNNN` (e.g. PCF-2026-00142), auto-increment
- [ ] **Admin — Quote list** (`/adminhu/quotes`):
  - Status tabs: All / New / Reviewing / Quoted / Won / Lost
  - Search by quote number, customer name, email, company
  - Filter by status, date range, assigned user
  - Bulk status update
  - Color-coded status badges
- [ ] **Admin — Quote detail** (`/adminhu/quotes/{id}`):
  - Customer info panel (name, email, phone, company, GSTIN)
  - Quoted items list with editable unit price (admin fills after review)
  - Estimated total (auto-calculated)
  - Status change dropdown with confirmation
  - Assign to team member
  - **Response thread** (email history, like a helpdesk):
    - Send email to customer (subject + body, rich text)
    - Use email templates or freeform
    - Reply shows sent/received direction
    - Internal notes (hidden from customer, yellow background)
  - WhatsApp quick-link button (opens wa.me with pre-filled message)
  - **Generate PDF quote** (branded, itemised, with quote number)
  - Download or email PDF directly from admin
- [ ] **Email templates**:
  - Quote received confirmation (to customer)
  - Quote response (admin to customer)
  - Status update notification

### Key files to create
```
app/Http/Controllers/
  QuoteCartController.php   — add/remove/view cart
  QuoteSubmitController.php — submit + confirmation

app/Http/Controllers/Admin/
  QuoteController.php       — full management (replace stub)

app/Mail/
  QuoteReceivedMail.php     — confirmation to customer
  QuoteResponseMail.php     — admin reply to customer

app/PDF/
  QuotePdf.php              — DomPDF quote template

resources/views/
  quotes/cart.blade.php     — frontend cart
  quotes/submit.blade.php   — quote form
  quotes/confirmation.blade.php
  admin/quotes/index.blade.php
  admin/quotes/show.blade.php
  pdf/quote.blade.php       — PDF template
```

---

## Phase 5 — Content Management ✅ COMPLETE

**Goal**: Full CMS — blog, static pages, drag-and-drop menu builder, homepage sections manager.

### What was built
- [x] **Blog admin** — Post CRUD with Quill rich editor, featured image (Spatie Media), categories, tags, status (Draft/Published/Scheduled), SEO fields, author tracking
- [x] **Blog frontend** — `/blog` list with featured post + category sidebar, `/blog/{slug}` article detail with related posts, `/blog/category/{slug}` filtered list
- [x] **Static Pages admin** — Page CRUD with Quill editor, template selector (default/full-width/landing), publish/nav toggles, sort order, SEO fields
- [x] **Static Pages frontend** — `/page/{slug}` renders any published page
- [x] **Menu Manager** — Multiple menus by location (primary, footer, mobile, sidebar), add items (URL/Page/Category types), SortableJS drag-to-reorder via AJAX
- [x] **Sections Manager** — Homepage content blocks grouped by page, visible/hidden toggle, content fields (heading, subheading, body, CTA)
- [x] **Banners** — Full CRUD with theme (navy/amber/white/dark), eyebrow text, schedule (starts_at/ends_at), active toggle
- [x] **Testimonials** — CRUD with name, role, city, body, star rating, verified badge, active toggle
- [x] **Trust Bar items** — Icon SVG, title, subtitle, sort order, visibility
- [x] **Media Library** — Grid view of all Spatie Media files, upload (multi-file), delete, copy URL
- [x] **Phase 5 seeder** — Default menus (primary + footer), 4 homepage sections, 4 trust items, 4 blog categories

### Key files created
```
app/Http/Controllers/Admin/
  BlogController.php         — posts CRUD + categories management
  PageController.php         — static pages CRUD
  MenuController.php         — menus + items + drag reorder
  SectionController.php      — sections CRUD
  BannerController.php       — banners CRUD
  TestimonialController.php  — testimonials CRUD
  TrustItemController.php    — trust items CRUD
  MediaController.php        — media library

app/Http/Controllers/Site/
  BlogFrontController.php    — index, show, category
  PageFrontController.php    — show by slug

app/Models/
  BlogPost.php, BlogCategory.php, BlogTag.php
  Page.php, Menu.php, MenuItem.php
  Section.php, Banner.php, Testimonial.php, TrustItem.php

resources/views/admin/
  blog/index.blade.php, blog/form.blade.php, blog/categories.blade.php
  pages/index.blade.php, pages/form.blade.php
  menus/index.blade.php, menus/form.blade.php, menus/items.blade.php
  sections/index.blade.php, sections/form.blade.php
  banners/index.blade.php, banners/form.blade.php
  testimonials/index.blade.php, testimonials/form.blade.php
  trust/index.blade.php, trust/form.blade.php
  media/index.blade.php

resources/views/blog/
  index.blade.php, show.blade.php, category.blade.php

resources/views/pages/
  page.blade.php

database/seeders/Phase5ContentSeeder.php
```

### Frontend routes added
```
GET /blog                    — blog listing
GET /blog/{slug}             — article detail
GET /blog/category/{slug}    — category filtered listing
GET /page/{slug}             — static page by slug
```

---

## Phase 6 — SEO & Settings ✅ COMPLETE

**Goal**: Per-entity SEO, XML sitemap auto-generation, global settings, footer manager, announcement bar.

### What was built
- [x] **`seo_meta` table** — polymorphic (meta_title, meta_description, og_title, og_image, canonical, noindex, schema_json)
- [x] **`SeoMeta` model** + **`HasSeoMeta` trait** — applied to Product, BlogPost, Page, Category
- [x] **Trait helpers** — `getSeoTitle()`, `getSeoDescription()`, `getOgImage()`, `isNoindex()`, `saveSeoMeta()`
- [x] **JSON-LD schema** — Product schema on product detail, BlogPosting schema on blog articles
- [x] **OG / Twitter card meta** — injected dynamically in `layouts/site.blade.php` using `@section('og_image')`
- [x] **Canonical tag** — auto-set to current URL on every frontend page
- [x] **XML Sitemap** (`/sitemap.xml`) — Products, Categories, BlogPosts, Pages with priorities + changefreqs
- [x] **robots.txt** (`/robots.txt`) — content served from settings DB (editable in admin)
- [x] **Global Settings admin** (`/adminhu/settings`) — 8-tab UI:
  - **Brand** — site name, tagline, accent colour
  - **Contact** — phone, email, address, WhatsApp number + message
  - **Social** — Instagram, LinkedIn, Facebook, Twitter, YouTube
  - **SEO** — default meta title/description, OG image, robots.txt editor
  - **Email/SMTP** — driver, host, port, credentials, from name/email
  - **Analytics** — Google Analytics (G-XXXX) + GTM (GTM-XXXX)
  - **Footer** — copyright text, 3 column headings
  - **Announcement bar** — enable/disable, items (one per line → JSON)
- [x] **`Setting` model** — static `get()`, `set()`, `group()`, `saveMany()` with cache
- [x] **Frontend layout updated** — all hardcoded contact/phone/email replaced with `Setting::get()`, announcement bar items from DB, GA/GTM scripts injected conditionally, Blog added to nav
- [x] **Phase 6 seeder** — 32 default settings across all groups
- [x] **Admin sidebar** — added Banners, Testimonials, Trust Bar, Sitemap links

### Key files
```
app/Models/SeoMeta.php
app/Traits/HasSeoMeta.php
app/Models/Setting.php                    — with static cache helpers
app/Http/Controllers/Admin/SettingsController.php
app/Http/Controllers/Site/SitemapController.php
resources/views/admin/settings/index.blade.php
database/seeders/Phase6SettingsSeeder.php
database/migrations/2026_06_02_*_create_seo_meta_table.php
```

---

## Phase 7 — Polish & Advanced ✅ COMPLETE

**Goal**: Analytics dashboard, contact inbox, advanced quote features, WhatsApp integration, notifications.

### What was built
- [x] **Analytics dashboard** — fully upgraded with Chart.js:
  - 12-month quotes line chart
  - Quote funnel (New → Reviewing → Quoted → Won → Lost) with counts
  - Revenue pipeline card (₹ value of reviewing + quoted quotes)
  - Top 10 most-quoted products with bar chart
  - Recent contacts widget on dashboard
- [x] **Contact form inbox** (`/adminhu/contacts`):
  - List with status tabs (Active / Unread / Read / Replied / Archived)
  - Search by name, email, message
  - Amber unread indicator dots
  - Show detail with full message
  - Reply via email directly from admin (uses Settings SMTP config)
  - Archive / mark unread / delete actions
  - WhatsApp quick-link if phone number provided
- [x] **Advanced quote features**:
  - **Duplicate quote** — clone any quote as a new draft with new PCF number
  - **Export quotes to CSV** — filtered by current status/date range, button on quotes index
- [x] **Notification bell** — in admin header showing unread quote + contact counts; dropdown with quick links
- [x] **WhatsApp integration** — fully wired from Settings:
  - Quote model `whatsapp_url` reads number + message from `settings` table
  - Contact page shows WhatsApp button using Settings number
  - Contact detail page shows WhatsApp button if phone provided
- [x] **ContactSubmission model** — filled out with fillable, casts, scopes, `markRead()`, `initials`

### Key files
```
app/Http/Controllers/Admin/DashboardController.php  — full analytics
app/Http/Controllers/Admin/ContactController.php    — inbox + reply + archive
app/Http/Controllers/Admin/QuoteController.php      — + duplicate() + export()
app/Models/ContactSubmission.php                    — model with helpers
resources/views/admin/dashboard/index.blade.php     — Chart.js + funnel + top products
resources/views/admin/contacts/index.blade.php      — inbox list
resources/views/admin/contacts/show.blade.php       — detail + reply form
resources/views/admin/quotes/show.blade.php         — + Duplicate button
resources/views/admin/quotes/index.blade.php        — + Export CSV button
resources/views/admin/layouts/app.blade.php         — + notification bell
resources/views/pages/contact.blade.php             — + WhatsApp button from Settings
```

---

## File Structure Reference

```
C:\xampp\htdocs\pcfy-app\
├── app/
│   ├── Http/
│   │   ├── Controllers/Admin/     — all admin controllers
│   │   └── Middleware/Admin/      — AdminMiddleware.php
│   ├── Models/                    — all Eloquent models
│   ├── Mail/                      — (Phase 4) Mailable classes
│   └── Imports/                   — (Phase 2) Excel import classes
├── database/
│   ├── migrations/                — all table schemas
│   └── seeders/DatabaseSeeder.php — roles + super admin
├── resources/views/
│   ├── admin/
│   │   ├── layouts/app.blade.php  — admin shell
│   │   ├── auth/login.blade.php   — login page
│   │   ├── dashboard/             — dashboard
│   │   └── users/                 — user management
│   └── (Phase 3+) site views
├── routes/web.php                 — all routes
├── public/
│   ├── css/                       — (Phase 3) frontend CSS
│   ├── js/                        — (Phase 3) frontend JS
│   └── storage -> storage/app/public (symlink)
└── .env                           — environment config
```

---

## Packages Installed

| Package | Version | Purpose |
|---|---|---|
| spatie/laravel-permission | ^6 | Roles & permissions |
| spatie/laravel-medialibrary | ^11 | Image management |
| spatie/laravel-sluggable | ^3 | Auto SEO slugs |
| spatie/laravel-sitemap | ^8 | XML sitemap |
| barryvdh/laravel-dompdf | ^3 | PDF generation |
| maatwebsite/excel | ^3 | CSV/XLSX import |
| intervention/image | ^3 | Image processing |

---

## Notes for Next Session

- Phase 1 is fully functional — login works, routes cached, DB migrated
- All Phase 2–7 controllers exist as stubs (return placeholder views) — no broken routes
- To start Phase 2: implement `ProductController`, `CategoryController`, `BrandController` + their views
- The `Quote` model and all quote migrations are ready — Phase 4 just needs the controller logic and views
- Font stack is `Inter` in admin, `Barlow Condensed + DM Sans` on frontend (matches existing CSS)
- Admin design language: minimal, dense, 14px base, navy + amber accent, no rounded corners (4–6px radius)
