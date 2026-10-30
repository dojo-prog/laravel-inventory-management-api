# Inventory Management API

A complete, production-structured REST API for managing product inventory — categories, suppliers, products, and a fully auditable stock movement history. Built with **Laravel 13** and **PHP 8.5**.

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.5-777BB4?logo=php&logoColor=white" alt="PHP 8.5">
  <img src="https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel&logoColor=white" alt="Laravel 13">
  <img src="https://img.shields.io/badge/PostgreSQL-16-4169E1?logo=postgresql&logoColor=white" alt="PostgreSQL">
  <img src="https://img.shields.io/badge/Tests-Pest-84CC16" alt="Pest">
</p>

---

## Table of Contents

- [Overview](#overview)
- [Key Features](#key-features)
- [Tech Stack](#tech-stack)
- [Architecture & Conventions](#architecture--conventions)
- [Getting Started](#getting-started)
- [Demo Accounts](#demo-accounts)
- [Seeded Demo Data](#seeded-demo-data)
- [Postman Collection](#postman-collection)
- [API Reference](#api-reference)
  - [Authentication](#authentication)
  - [Categories](#categories)
  - [Suppliers](#suppliers)
  - [Products](#products)
  - [Product–Supplier Links](#product--supplier-links)
  - [Stock Operations](#stock-operations)
  - [Stock Movement History](#stock-movement-history)
- [Example Requests](#example-requests)
- [Data Model](#data-model)
- [Roles & Authorization](#roles--authorization)
- [Testing](#testing)
- [Code Style](#code-style)
- [Project Structure](#project-structure)

---

## Overview

The **Inventory Management API** is a RESTful backend that tracks technology product stock end-to-end:

- Organize **products** into **categories**.
- Associate products with **suppliers**, each with its own supplier SKU and negotiated unit cost.
- Record every **stock movement** — `stock-in`, `stock-out`, and `adjustment` — in an append-only audit trail that reconciles with each product's current `stock_quantity`.
- Upload a **product image** to **Cloudinary** (signed server-side upload); replacing or deleting a product automatically schedules cleanup of the old asset.
- Filter, sort, and paginate all listings (e.g. "show me products below their reorder level").

All identifiers are **UUIDs**, all requests/ responses are JSON via **Eloquent API Resources** (versioned under `/api/v1`), and access is managed with **Laravel Sanctum** bearer tokens.

---

## Key Features

| Area | Highlights |
|---|---|
| **Authentication** | Register, login (Sanctum personal access token), logout, email verification, password reset |
| **Users** | Role-based accounts (`admin` / `staff`) |
| **Categories** | Full CRUD with unique names and search/filter |
| **Suppliers** | Full CRUD with contact details, search across name/contact/email; filter by supplied product |
| **Products** | Full CRUD, auto-generated SKUs, multiple filters (`search`, `category_id`, `supplier_id`, `status`, `min_stock`, `low_stock`), sorting and pagination |
| **Product Images** | Single image per product via **Cloudinary** — signed server-side upload (`multipart/form-data`), asset replaced on image change and destroyed when the product is deleted (queued background job) |
| **Product–Supplier** | Link products to suppliers with per-supplier SKU & unit cost (unique per pair, unique supplier SKU) |
| **Stock Operations** | `stock-in`, `stock-out` (with insufficient-stock guard), and `adjustment` executed transactionally |
| **Audit Trail** | Every operation writes a `stock_movements` record storing actor, quantity, previous/new stock, reason, and reference (e.g. `PO-2026-011`, `SO-2026-014`) |
| **Data Integrity** | UUID primary keys, unique constraints, and DB-level CHECK constraints (non-negative cost/stock, valid statuses) |

---

## Tech Stack

- **PHP 8.5** / **Laravel 13** (API-focused setup)
- **PostgreSQL** with UUID primary keys (`HasUuids`)
- **Laravel Sanctum** for token authentication
- **Eloquent API Resources** for response transforms (versioned `/api/v1`)
- **Form Requests** for validation + Policies for authorization
- **Cloudinary** for product image storage (REST Upload API, signed requests via `App\Services\CloudinaryService`)
- **Pest** for testing, **Laravel Pint** for code style
- Database seeding with deterministic demo data

---

## Architecture & Conventions

- **Versioned API**: all endpoints live under `api/v1` (`routes/api.php`, `routes/auth.php`).
- **Controllers** stay thin: authorize → validate (FormRequest) → perform → return resource.
- **Validation** in `App\Http\Requests\*` with explicit custom error messages.
- **Authorization** via `App\Policies\*` accessed through `AuthorizesRequests`.
- **Responses** via `App\Http\Resources\*`; single resources are wrapped in `data`, collections use standard Laravel pagination (`data`, `meta`, `links`).
- **Transactional writes**: stock operations update the product and write the audit record inside a `DB::transaction`.
- **SKU generation**: products receive a random SKU (`{CATEGORY}-{NAME}-{SUFFIX}`) automatically if one isn't provided (`App\Models\Product::generateSKU()`).

---

## Getting Started

### Requirements

- PHP 8.5+
- Composer
- PostgreSQL
- Node.js & npm (optional, for assets)

### Installation

```bash
# 1. Clone the repository
git clone <repository-url>
cd inventory-management-api

# 2. Install dependencies
composer install

# 3. Configure the environment
cp .env.example .env
php artisan key:generate

# 4. Set your PostgreSQL credentials in .env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=laravel_dev_inventory_management
DB_USERNAME=postgres
DB_PASSWORD=secret

# 4b. Set your Cloudinary credentials in .env
CLOUDINARY_CLOUD_NAME=your_cloud_name
CLOUDINARY_API_KEY=your_api_key
CLOUDINARY_API_SECRET=your_api_secret
CLOUDINARY_FOLDER=inventory-management

# 5. Run migrations and seed the demo data
php artisan migrate:fresh --seed

# 6. Start the API (defaults to http://localhost:8000)
php artisan serve
```

> The seeder requires a clean database — the unique constraints (`sku`, `email`, `category name`, …) will reject a second run. Use `php artisan migrate:fresh --seed` to reset and reseed.

---

## Demo Accounts

Seeded by `Database\Seeders\UserSeeder`:

| Name       | Email          | Role  | Password  |
|------------|----------------|-------|-----------|
| Dojo Admin | admin@dojo.com | admin | `testPass` |
| Dojo Staff | staff@dojo.com | staff | `testPass` |

Both accounts come pre-verified (`email_verified_at` is set), so you can log in immediately:

```http
POST /api/v1/login
{
    "email": "admin@dojo.com",
    "password": "testPass"
}
```

> These are demo credentials intended for local development only.

---

## Seeded Demo Data

`php artisan migrate:fresh --seed` produces fully **deterministic** demo data (no Faker randomness), so every fresh database looks identical:

| Seeder | Contents |
|---|---|
| `UserSeeder` | 1 admin + 1 staff (see [Demo Accounts](#demo-accounts)) |
| `CategorySeeder` | 6 technology categories: **Laptops, Smartphones, Monitors & Displays, Peripherals, Audio, Storage & Networking** |
| `SupplierSeeder` | 5 suppliers with full contact details (TechSource Distribution, Global Compute Supply, ScreenCraft Ltd., AudioHub Wholesale, NetLink Systems) |
| `ProductSeeder` | 12 technology products (2 per category — e.g. `LAP-MAC-0001`, `SMT-IPH-0003`, `AUD-SNY-0009`), explicit SKUs, realistic unit costs, reorder levels, and 15 product–supplier links with unique supplier SKUs |
| `StockMovementSeeder` | 25 movements (12 stock-in, 12 stock-out, 1 adjustment) with staggered timestamps that reconcile exactly with each product's `stock_quantity` |

Seeded catalog highlights:

- Every product has a deterministic SKU (e.g. `LAP-MAC-0001` = MacBook Pro 14" M3 Pro) and supplier SKUs like `TSD-10001`.
- One product is intentionally below its reorder level (`SMT-SAM-0004` — Galaxy S24 Ultra, 3/6) to demo the `low_stock` filter.
- One product is `inactive` and out of stock (`MON-LGP-0006` — LG UltraGear).
- One product carries an `adjustment` history (`STN-SAM-0011` — Samsung 990 Pro 1TB, damaged unit written off).

---

## Postman Collection

A ready-made Postman collection covering every endpoint is included:

```
docs/laravel-inventory-management.postman_collection.json
```

**To use it:**

1. In Postman: **Import** → select the JSON file.
2. The collection targets `http://localhost:8000` by default (matches `php artisan serve`).
3. Send the `Login` request with a demo account — the returned Sanctum token is used for collection-level Bearer auth.
4. Every folder (**Auth, Category, Supplier, Product, ProductSupplier, StockMovement**) contains pre-composed requests.

---

## API Reference

Base URL: `http://localhost:8000/api/v1`

All endpoints except register/login/forgot-password/reset-password require a Bearer token:

```http
Authorization: Bearer <your-token>
```

### Authentication

| Method | URI | Description |
|--------|-----|-------------|
| `POST` | `/register` | Create a user account (`name`, `email`, `password`, `password_confirmation`, optional `role`) — returns `204 No Content` and queues a verification email |
| `POST` | `/login` | Authenticate and receive a `token` + `user` payload |
| `GET` | `/user` | Return the authenticated user's profile |
| `POST` | `/logout` | Revoke the current access token |
| `GET` | `/verify-email/{id}/{hash}` | Verify e-mail address (signed URL, requires auth) |
| `POST` | `/email/verification-notification` | Resend the verification email |
| `POST` | `/forgot-password` | Request a password reset link (`email`) |
| `POST` | `/reset-password` | Reset the password (`token`, `email`, `password`, `password_confirmation`) |

### Categories

| Method | URI | Description |
|--------|-----|-------------|
| `GET` | `/categories` | List (paginate). **Filters:** `search`, `sort_by`, `sort_order`, `per_page`
| `POST` | `/categories` | Create (`name` required & unique, `description` optional) |
| `GET` | `/categories/{id}` | Show one |
| `PUT` / `PATCH` | `/categories/{id}` | Update |
| `DELETE` | `/categories/{id}` | Delete |

### Suppliers

| Method | URI | Description |
|--------|-----|-------------|
| `GET` | `/suppliers` | List (paginate). **Filters:** `search` (name / contact person / email), `product_id`, `sort_by`, `sort_order`, `per_page`
| `POST` | `/suppliers` | Create (`name`, `contact_person` required; unique as a pair; `email`, `phone`, `address` optional) |
| `GET` | `/suppliers/{id}` | Show one (with products when loaded) |
| `PUT` / `PATCH` | `/suppliers/{id}` | Update |
| `DELETE` | `/suppliers/{id}` | Delete |

### Products

| Method | URI | Description |
|--------|-----|-------------|
| `GET` | `/products` | List (paginate). **Filters:** `search` (name/sku), `category_id`, `supplier_id`, `status`, `min_stock`, `low_stock=true`, `sort_by` (`created_at`, `name`, `unit_cost`, `stock_quantity`), `sort_order`, `per_page` |
| `POST` | `/products` | Create (see fields below; `sku` optional — auto-generated; acceptable as multipart with optional image) |
| `GET` | `/products/{id}` | Show (with `category` and `suppliers`, plus `image_url`) |
| `PUT` / `PATCH` | `/products/{id}` | Update (multipart if an image is included) |
| `DELETE` | `/products/{id}` | Delete (the stored image is removed from Cloudinary in the background) |

Product fields:

| Field | Rules |
|-------|-------|
| `category_id` | required, must exist |
| `sku` | optional, unique (case-insensitive) — generated automatically when omitted |
| `name` | required, 2–100 chars, unique per category (case-insensitive) |
| `description` | optional, 10–300 chars |
| `image` | optional, image file (`jpeg`, `jpg`, `png`, `webp`), max 2 MB — multipart uploads only |
| `unit` | required (e.g. `pcs`), 2–50 chars |
| `unit_cost` | required, numeric, ≥ 0 |
| `stock_quantity` | required, integer ≥ 0 |
| `reorder_level` | required, integer ≥ 0, max 999 |
| `status` | required, `active` or `inactive` |

Each product response exposes `image_url` (Cloudinary CDN URL) and `image_public_id` (the Cloudinary asset identifier).

#### Product Image Handling

One image per product, stored in **Cloudinary** (`{CLOUDINARY_FOLDER}/products/`). Uploads are **signed server-side** — the client posts the file, and the backend computes the request signature, streams the file to the Cloudinary Upload API, and stores the returned `public_id` + `secure_url`.

| Event | Behavior |
|---|---|
| Create with `image` | File is uploaded; asset URL + public ID persisted with the product |
| Update with `image` | New file is uploaded first, product is updated, then the **old** asset is scheduled for destruction |
| Update without `image` | Existing image is left untouched |
| Product deleted | The stored asset is scheduled for destruction along with the row |

Cleanup runs through the `DestroyCloudinaryImage` queued job (database queue, 3 attempts with backoff, dispatched after commit), so Cloudinary outages never block or fail a request — the DB mutation always happens first. Assets that can't be removed within the retry window are recorded in the logs.

Multipart example (create a product with an image):

```http
POST /api/v1/products
Authorization: Bearer <token>
Content-Type: multipart/form-data

category_id=...&name=Wireless Mouse&unit=pcs&unit_cost=25.00&stock_quantity=50&reorder_level=10&status=active&image=@mouse.jpg
```

### Product–Supplier Links

Scoped to a product:

| Method | URI | Description |
|--------|-----|-------------|
| `GET` | `/products/{id}/suppliers` | List |
| `POST` | `/products/{id}/suppliers` | Attach a supplier (`supplier_id`, `supplier_sku`, `unit_cost`) |
| `GET` | `/products/{id}/suppliers/{supplierId}` | Show the link |
| `PUT` / `PATCH` | `/products/{id}/suppliers/{supplierId}` | Update `supplier_sku` / `unit_cost` |
| `DELETE` | `/products/{id}/suppliers/{supplierId}` | Detach |

Each (`product`, `supplier`) pair is unique and the `supplier_sku` is globally unique.

### Stock Operations

Applied transactionally; each op appends a movement and updates the product's stock.

| Method | URI | Description |
|--------|-----|-------------|
| `POST` | `/products/{id}/stock-in` | Add stock (`quantity` ≥ 1) |
| `POST` | `/products/{id}/stock-out` | Remove stock — rejected with `422` if it would go negative |
| `POST` | `/products/{id}/adjust` | **Set** the stock to an absolute `quantity` (e.g. stock count correction) |

Body for all three:

```json
{
    "quantity": 5,
    "reason": "Supplier restock",
    "reference": "PO-2026-001"
}
```

### Stock Movement History

Read-only audit log.

| Method | URI | Description |
|--------|-----|-------------|
| `GET` | `/stock-movements` | List (paginate). **Filters:** `product_id`, `user_id`, `type` (`stock-in`/`stock-out`/`adjustment`), `from`, `to`, `sort_by=created_at`, `sort_order` |
| `GET` | `/stock-movements/{id}` | Show one (with `product` and `user`) |

---

## Example Requests

### Login

```http
POST /api/v1/login
Content-Type: application/json

{
    "email": "admin@dojo.com",
    "password": "testPass"
}
```

**Response**

```json
{
    "data": {
        "user": {
            "id": "01a188d4-8f75-71f0-867e-db7e8e37efdf",
            "name": "Dojo Admin",
            "email": "admin@dojo.com",
            "role": "admin",
            "created_at": "...",
            "updated_at": "..."
        },
        "token": "1|XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX"
    }
}
```

### Stock In

```http
POST /api/v1/products/{productId}/stock-in
Authorization: Bearer <token>
Content-Type: application/json

{
    "quantity": 10,
    "reason": "Supplier restock",
    "reference": "PO-2026-013"
}
```

**Response (uses the full-flow shape)**

```json
{
    "data": {
        "product": {
            "id": "...",
            "sku": "SMT-SAM-0004",
            "name": "Galaxy S24 Ultra 512GB",
            "stock_quantity": 13,
            "category": { "...": "..." },
            "suppliers": [ { "...": "..." } ]
        },
        "stock_movement": {
            "id": "...",
            "type": "stock-in",
            "quantity": 10,
            "previous_quantity": 3,
            "new_quantity": 13,
            "reason": "Supplier restock",
            "reference": "PO-2026-013",
            "user": { "...": "..." }
        }
    }
}
```

---

## Data Model

```
users
├── uuid id (PK)
├── name, email (unique), password
├── role: admin | staff (default staff)
├── email_verified_at, remember_token
└── hasMany → stock_movements

categories
├── uuid id (PK)
├── name (unique), description
└── hasMany → products

suppliers
├── uuid id (PK)
├── name (+contact_person unique as pair)
├── email, phone, address
└── belongsToMany → products (via product_suppliers)

products
├── uuid id (PK)
├── category_id (UUID FK → categories, null-on-delete)
├── sku (unique), name (unique per category)
├── description, unit
├── unit_cost (≥ 0)
├── stock_quantity (≥ 0), reorder_level (≥ 0)
├── status: active | inactive
├── image_url, image_public_id (nullable — Cloudinary asset)
├── belongsTo → category
├── belongsToMany → suppliers (via product_suppliers)
└── hasMany → stock_movements

product_suppliers (pivot)
├── uuid id (PK)
├── product_id (FK, cascade delete)
├── supplier_id (FK, cascade delete)
├── supplier_sku (unique), unit_cost (≥ 0)
└── unique (product_id, supplier_id)

stock_movements (append-only audit log)
├── uuid id (PK)
├── product_id (FK, cascade delete)
├── user_id (FK, null-on-delete)
├── type: stock-in | stock-out | adjustment
├── quantity, previous_quantity, new_quantity (≥ 0)
├── reason, reference (nullable)
└── belongsTo → product, user
```

---

## Roles & Authorization

Two roles exist (`admin`, `staff`, defaulting to `staff`), enforced by the `role` PostgreSQL enum and managed in-app via `App\Models\User::hasRole()` with `App\Policies\*`. Authorization is currently permissive (all authenticated users may operate on inventory resources) — the role checks are scaffolded via `hasRole()` for use in policies and future endpoints. Movement records keep the `user_id` of whoever performed each stock op, so the audit trail shows exactly which account acted.

---

## Testing

The suite uses **Pest** with SQLite in-memory by default (`phpunit.xml`):

```bash
# Whole suite
vendor/bin/pest --compact

# A single file or filter
vendor/bin/pest tests/Feature/ExampleTest.php
vendor/bin/pest --filter=testName
```

```bash
php artisan test --compact
```

Tests run migrations against a dedicated test database and use model factories (`database/factories/*`).

---

## Code Style

[Laravel Pint](https://github.com/laravel/pint) is the formatting standard:

```bash
# Auto-fix all changed files
vendor/bin/pint --dirty

# Check without modifying
vendor/bin/pint --test
```

---

## Project Structure

```
app/
├── Http/
│   ├── Controllers/Api/V1/          # Versioned REST controllers
│   │   ├── CategoryController
│   │   ├── ProductController
│   │   ├── ProductQuantityController  # stock-in / stock-out / adjust
│   │   ├── ProductSupplierController
│   │   ├── StockMovementController
│   │   ├── SupplierController
│   │   └── Auth/                   # register, login, logout, verification, reset
│   ├── Requests/                   # FormRequest validation (Auth, Category, Product, …)
│   ├── Middleware/EnsureEmailIsVerified.php
│   └── Resources/                  # API resources (User, Category, Product, Supplier, …)
├── Models/                         # Eloquent models (UUIDs, relationships, SKU generation)
├── Policies/                       # Authorization policies
├── Services/                       # Third-party API clients
│   └── CloudinaryService.php       # Signed Cloudinary upload / destroy (REST, no SDK)
├── Jobs/                           # Queued jobs
│   └── DestroyCloudinaryImage.php  # Background cleanup of replaced / deleted product images
└── Providers/

database/
├── factories/                      # UserFactory
├── migrations/                     # Schema with UUIDs, unique & CHECK constraints
└── seeders/                        # UserSeeder → CategorySeeder → SupplierSeeder
                                    # → ProductSeeder → StockMovementSeeder

routes/
├── api.php                         # Protected /api/v1 resource routes
└── auth.php                        # Public/protected auth routes

docs/
└── laravel-inventory-management.postman_collection.json

tests/
├── Feature/                        # API + auth feature tests
├── Unit/
└── Pest.php
```

---

## License

Open-sourced under the [MIT license](https://opensource.org/licenses/MIT).
