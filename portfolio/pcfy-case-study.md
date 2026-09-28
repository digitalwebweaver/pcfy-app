# pcfy.in — Certified Refurbished PC Store & B2B Quote Platform

**Category:** E-commerce / B2B Web Application
**Client:** pcfy.in
**Role:** Lead Developer (digitalwebweaver team)
**Stack:** Laravel 12, PHP 8.2+, MySQL, Blade, Alpine.js, Vite

---

## Overview

pcfy.in is a full-stack e-commerce platform built for a certified refurbished-hardware retailer selling laptops, desktops, servers, and spare parts. Unlike a standard storefront, the platform is built around a **B2B quote workflow** — bulk and corporate buyers request quotes instead of checking out directly, and the admin team manages pricing, negotiation, and conversion to orders through a dedicated backend.

digitalwebweaver led the end-to-end build: architecture, database design, storefront, admin panel, and the quote-to-order pipeline that is the core of the business model.

---

## The Problem

Refurbished IT hardware sales for B2B/bulk buyers don't fit a standard cart-and-checkout flow — pricing varies by quantity, condition grade, and negotiation, and corporate buyers need GSTIN capture, formal quotes, and a paper trail (PDF, email, status tracking). The client needed:

- A public catalog that could showcase graded inventory (Grade A/B/C condition) with full specs, not just a price and a photo.
- A quoting system that replaces "add to cart → pay" with "request a quote → admin prices it → convert to order."
- A single admin panel flexible enough for non-technical staff to manage products, content, and the entire sales pipeline without touching code.
- SEO and content tooling (blog, CMS pages, sitemaps) so the site could be a search-discoverable, standalone marketing asset — not just a backend for existing traffic.

---

## Solution & Architecture

Built on **Laravel 12** with a modular monolith structure — clean separation between public storefront controllers (`Site/`) and the admin backend (`Admin/`), sharing one Eloquent data layer.

**Key architectural decisions:**

- **Role-based access via Spatie Permission** — six distinct roles (`super_admin`, `admin`, `sales`, `content`, `products`, `customer`) scope what each admin user can touch, so sales staff can manage quotes without being able to edit product pricing rules or site settings.
- **Spatie Medialibrary** for all image handling (product galleries, banners, blog media, brand logos) — consistent upload, storage, and reordering behavior across every content type instead of one-off file handling per feature.
- **Session-based quote cart**, separate from a traditional order cart — customers build a quote request, not a checkout, which matches the B2B sales motion.
- **Structured + freeform specs** on products: quick "spec chips" (pipe-separated tags) for scannability, plus grouped structured spec rows (group → label → value) for full technical detail sheets — both editable without code changes.
- **PDF quote generation** (barryvdh/laravel-dompdf) and **bulk CSV import** (Maatwebsite/Excel) so the admin team can operate at catalog scale without manual data entry.
- **Auto-generated sitemap.xml and robots.txt** (Spatie Sitemap) plus per-entity SEO meta overrides, since organic discovery was a stated goal, not an afterthought.
- **Vanilla JS + custom CSS frontend** (no heavy JS framework) — kept the public site fast and lightweight, with Alpine.js reserved for lightweight interactivity in the admin panel only.

---

## Core Features

### Storefront
- Homepage with hero, category index, featured products, a "build-your-match" configurator widget, testimonials, and trust badges
- Shop/category pages with faceted filtering (brand, condition grade, processor, RAM, storage, price range) and sorting
- Product detail pages with image galleries, spec chips, grade badges, and warranty info
- Live AJAX search with thumbnail/price suggestions
- Blog with categories, tags, and full SEO meta
- CMS-managed static pages

### B2B Quote System
- Session-based quote cart (add/update/remove items pre-submission)
- Quote submission form (name, email, phone, company, GSTIN) — pre-filled for logged-in users
- Auto-generated quote numbers (`PCF-YYYY-NNNNN`)
- Automated confirmation emails
- Full lifecycle tracking: New → Reviewing → Quoted → Won/Lost

### Customer Accounts
- Registration/login/dashboard
- Quote history with live status and pricing
- Wishlist with one-click AJAX save/remove (guest users redirected to login)

### Admin Panel (`/adminhu`)
- Dashboard with key stats
- Product CRUD with bulk CSV import, drag-reorder image galleries, quick + structured specs, SEO fields
- Category & brand management (nested categories, slugs, images)
- Quote management: assign to staff, add line-item pricing, PDF export, convert directly to an order
- Order management with shipment tracking
- Blog, static pages, and drag-reorder navigation menus
- Homepage content blocks (sections, banners, testimonials, trust badges) — fully editable without a deploy
- Global media library
- Staff/role management
- Site-wide settings: SEO defaults, contact info, WhatsApp number, announcement bar, footer, GA/GTM tracking IDs

---

## Data Model

24 Eloquent models covering the full domain: products, product specs, categories, brands, media, quotes, quote items/responses, orders, order items/shipments, wishlists, blog posts/categories/tags, CMS pages, menus/menu items, homepage sections, banners, testimonials, trust items, site settings, SEO meta, and contact submissions — reflecting a genuinely full-featured commerce + CMS + CRM-lite system rather than a template storefront.

---

## Engineering Highlights

- **Clean domain separation** between storefront and admin without duplicating models or business logic — one Eloquent layer serving two very different UIs and permission contexts.
- **Quote-to-order pipeline** designed as a first-class flow (not bolted onto a cart system), matching how the client's sales team actually operates.
- **Fully admin-editable homepage** — every section, banner, and testimonial block is CMS-driven, so the client's team can run campaigns and update messaging without developer involvement.
- **SEO built into the data layer**, not retrofitted — every content type carries meta fields and slugs from day one.
- **RBAC that maps to real job functions** (sales vs. content vs. products vs. admin) rather than a single generic "admin" flag, reducing the operational risk of one team touching another's data.

---

## Outcome

Delivered as a fully-featured, production-ready platform covering the entire path from product discovery through quote negotiation to order fulfillment — plus the content and SEO tooling needed to make the site a self-sufficient marketing channel, not just a backend.

---

## Tech Stack Summary

| Layer | Technology |
|---|---|
| Framework | Laravel 12 (PHP 8.2+) |
| Database | MySQL |
| Auth & Roles | Spatie Laravel Permission |
| Media Management | Spatie Laravel Medialibrary |
| PDF Generation | barryvdh/laravel-dompdf |
| Bulk Import | Maatwebsite/Laravel Excel |
| SEO / Sitemap | Spatie Laravel Sitemap, Spatie Laravel Sluggable |
| Frontend | Blade, vanilla JS, custom CSS, Alpine.js (admin) |
| Build Tooling | Vite, Tailwind CSS 4 |
| Fonts | DM Sans, Barlow Condensed, DM Mono |
