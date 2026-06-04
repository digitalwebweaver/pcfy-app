# pcfy.in — Certified Refurbished PC Store

A full-stack e-commerce platform for selling certified refurbished laptops, desktops, servers, and spare parts. Built with Laravel 12, featuring a B2B quote workflow, admin panel, customer accounts, wishlist, blog, and SEO tools.

---

## Tech Stack

| Layer | Technology |
|---|---|
| Framework | Laravel 12 (PHP 8.2+) |
| Database | MySQL (via XAMPP) |
| Auth & Roles | Spatie Laravel Permission |
| Media | Spatie Laravel Medialibrary |
| PDF Generation | barryvdh/laravel-dompdf |
| Excel Import | Maatwebsite/Laravel Excel |
| Sitemap | Spatie Laravel Sitemap |
| Slugs | Spatie Laravel Sluggable |
| Frontend | Vanilla JS, custom CSS (no framework) |
| Fonts | DM Sans, Barlow Condensed, DM Mono (Google Fonts) |

---

## Features

### Storefront
- **Homepage** — hero, category index, featured products, build-your-match widget, testimonials, trust badges
- **Shop / Category pages** — filter by brand, condition grade (A/B/C), processor, RAM, storage, price range; sort by relevance/price/newest
- **Product detail page** — image gallery, spec chips, grade badge, warranty info, add-to-quote
- **Live search** — AJAX suggestions dropdown with product thumbnails, name, grade, price
- **Blog** — posts with categories, tags, featured images, SEO meta
- **Static pages** — CMS-managed via admin
- **Sitemap & robots.txt** — auto-generated

### Quote System (B2B)
- **Quote cart** — session-based, add/update/remove items
- **Quote submission** — customer fills name, email, phone, company, GSTIN, message; form pre-filled for logged-in users
- **Quote confirmation** — unique quote number (PCF-YYYY-NNNNN format)
- **Email notifications** — confirmation email to customer on submission

### Customer Accounts
- Register / Login / Logout (`/account/...`)
- **Dashboard — Quotes tab** — view all submitted quotes with status (New / Reviewing / Quoted / Won / Lost) and pricing
- **Dashboard — Saved Items tab** — wishlist with one-click remove
- Wishlist toggle (♡ button on every product card) — AJAX, red heart when saved, redirects guests to login

### Admin Panel (`/adminhu`)
- **Dashboard** — stats overview
- **Products** — full CRUD, bulk CSV import, image upload & reorder (Spatie Medialibrary), quick specs, grade, warranty, stock status, SEO meta
- **Categories & Brands** — manage with slugs, images, descriptions
- **Quotes** — list, view, update status, respond, assign to staff, add line-item pricing, PDF export, convert to order, duplicate
- **Orders** — create from quote, manage status, add shipment tracking
- **Blog** — posts, categories, tags, featured image, SEO meta
- **Pages** — static CMS pages
- **Menus** — custom navigation menus with drag-reorder
- **Sections / Banners / Testimonials / Trust Items** — homepage content blocks
- **Media Library** — global media manager
- **Users** — manage admin staff with roles
- **Settings** — site name, SEO defaults, contact info, WhatsApp number, announcement bar, footer content, Google Analytics / GTM IDs

### Roles
| Role | Access |
|---|---|
| `super_admin` | Full admin access |
| `admin` | Full admin access |
| `sales` | Quotes, orders |
| `content` | Blog, pages, media |
| `products` | Products, categories, brands |
| `customer` | Customer account portal only |

---

## Database Tables

| Table | Purpose |
|---|---|
| `users` | Admin staff + customer accounts |
| `roles` / `permissions` | Spatie RBAC |
| `products` | Product catalogue |
| `product_specs` | Key-value spec chips |
| `categories` | Product categories |
| `brands` | Product brands |
| `media` | Spatie media attachments |
| `quotes` | Quote requests |
| `quote_items` | Line items per quote |
| `quote_responses` | Admin replies on quotes |
| `orders` | Converted orders |
| `order_items` | Line items per order |
| `order_shipments` | Shipment tracking per order |
| `wishlists` | User saved products |
| `blog_posts` | Blog articles |
| `blog_categories` | Blog categories |
| `blog_tags` | Blog tags |
| `pages` | CMS static pages |
| `menus` / `menu_items` | Navigation menus |
| `sections` | Homepage content sections |
| `banners` | Promotional banners |
| `testimonials` | Customer testimonials |
| `trust_items` | Trust/certification badges |
| `settings` | Key-value site settings |
| `seo_meta` | Per-page SEO overrides |
| `contact_submissions` | Contact form entries |

---

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/          # Admin panel controllers
│   │   └── Site/           # Frontend controllers
│   └── Middleware/
│       ├── Admin/AdminMiddleware.php
│       └── CustomerMiddleware.php
├── Models/                 # Eloquent models
├── Mail/                   # Mailable classes
├── Imports/                # Excel import classes
└── Providers/

public/
├── css/
│   ├── base.css            # Global layout, header, nav, cards
│   ├── home-a.css          # Hero section
│   ├── home-b.css          # Category index, product grid
│   ├── shop.css            # Shop/filter page
│   └── product.css         # Product detail page
└── js/
    ├── home-b.js
    ├── shop.js
    └── product.js

resources/views/
├── account/                # Customer login, register, dashboard
├── admin/                  # Admin panel views
├── blog/                   # Blog frontend
├── components/site/        # Reusable Blade components
├── emails/                 # Mail templates
├── layouts/site.blade.php  # Main site layout
├── pages/                  # Home, shop, product, cart, contact
└── quotes/                 # Quote submit & confirmation
```

---

## Local Setup

### Requirements
- PHP 8.2+
- MySQL
- Composer
- Node.js + npm
- XAMPP (or equivalent)

### Steps

```bash
# 1. Clone the repo
git clone https://github.com/digitalwebweaver/pcfy-app.git
cd pcfy-app

# 2. Install PHP dependencies
composer install

# 3. Copy and configure environment
cp .env.example .env
php artisan key:generate

# 4. Configure .env — set your DB credentials:
#    DB_DATABASE=pcfy
#    DB_USERNAME=root
#    DB_PASSWORD=

# 5. Run migrations
php artisan migrate

# 6. Seed initial data (optional)
php artisan db:seed

# 7. Link storage
php artisan storage:link

# 8. Create first admin user
php artisan tinker
# >>> \App\Models\User::create(['name'=>'Admin','email'=>'admin@pcfy.in','password'=>bcrypt('password')])->assignRole('super_admin');

# 9. Start the dev server
php artisan serve
```

Access the site at `http://localhost:8000`
Admin panel at `http://localhost:8000/adminhu`

---

## Environment Variables (key ones)

```env
APP_NAME="pcfy.in"
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pcfy
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_ADDRESS="hello@pcfy.in"
MAIL_FROM_NAME="pcfy.in"
```

---

## Key Routes

| Route | Description |
|---|---|
| `GET /` | Homepage |
| `GET /shop` | All products with filters |
| `GET /shop/{slug}` | Category-filtered shop |
| `GET /products/{slug}` | Product detail page |
| `GET /quote/cart` | Quote cart |
| `POST /quote/cart/add` | Add item to quote cart |
| `GET /quote/submit` | Quote submission form |
| `POST /quote/submit` | Submit quote |
| `GET /account/register` | Customer register |
| `GET /account/login` | Customer login |
| `GET /account` | Customer dashboard (auth) |
| `POST /wishlist/toggle` | Toggle saved product (AJAX) |
| `GET /blog` | Blog index |
| `GET /adminhu` | Admin dashboard |
| `GET /sitemap.xml` | Auto-generated sitemap |

---

## License

MIT
