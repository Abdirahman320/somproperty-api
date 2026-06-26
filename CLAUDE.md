# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

SOM Property Management is a multi-tenant SaaS platform for property owners to manage apartments, tenants, rent billing, utility readings, complaints, and maintenance assets. It serves three user roles through separate web portals and also exposes a REST API for a companion Flutter mobile app.

**Roles:**
- **Admin** (`/admin/*`) — platform operator; creates owners, manages plans and subscriptions
- **Owner** (`/owner/*`) — property manager; manages units, tenants, billing, complaints
- **Tenant** (`/tenant/*`) — renter; views bills, submits complaints, reads notifications

## Local Development

```bash
composer install
cp .env.example .env
php artisan key:generate
# Edit .env with MySQL credentials (DB_DATABASE=som_property, DB_USERNAME=root, DB_PASSWORD=...)
php artisan migrate --seed
php artisan serve

# Second terminal — required for billing notification emails
php artisan queue:work --queue=emails,default
```

**Default credentials after seeding:**

| Role         | Email                   | Password     |
|--------------|-------------------------|--------------|
| System Admin | admin@somproperty.com   | Admin@12345  |
| Demo Owner   | owner@demo.com          | Owner@12345  |
| Demo Tenant  | tenant@demo.com         | Tenant@12345 |

**Useful artisan commands:**
```bash
php artisan migrate:fresh --seed   # Reset and reseed entire database
php artisan view:clear             # Clear compiled Blade templates (run after template edits if output looks wrong)
php artisan config:clear           # Clear config cache
php artisan queue:work --queue=emails,default  # Process email jobs
```

**Scheduled tasks** (run via cron `php artisan schedule:run`):
- `billing:send-overdue-reminders` — daily 09:00
- `billing:generate-monthly` — monthly on 25th
- `contracts:expiry-warnings` — daily
- `billing:mark-overdue` — daily 00:05

## Architecture

### Three-Guard Authentication

Auth is handled by **four separate Laravel guards** defined in `config/auth.php`:

| Guard    | Model       | Provider  |
|----------|-------------|-----------|
| `admin`  | `AdminUser` | `admins`  |
| `owner`  | `Owner`     | `owners`  |
| `tenant` | `Tenant`    | `tenants` |
| `web`    | `User`      | `users`   |

All three `Authenticatable` models store the password in a **`password_hash` column** (not `password`). Each model overrides `getAuthPassword()`:
```php
public function getAuthPassword() { return $this->password_hash; }
```

Auth controllers (`app/Http/Controllers/Auth/`) do **not** use `Auth::attempt()`. They manually fetch the user by email, call `Hash::check(trim($password), $model->password_hash)`, then call `auth('guard')->login($model)`.

### Middleware Stack (`bootstrap/app.php`)

Middleware is registered in `bootstrap/app.php` (Laravel 11 — there is no `Http/Kernel.php`):

| Alias                 | Class                    | What it does |
|-----------------------|--------------------------|--------------|
| `auth.admin`          | `AuthAdmin`              | Redirects to `/admin/login` if not authenticated; merges `admin` into request |
| `auth.owner`          | `AuthOwner`              | Redirects to `/owner/login`; merges `owner` object into `$request->owner` |
| `auth.tenant`         | `AuthTenant`             | Redirects to `/tenant/login`; merges `tenant` object into `$request->tenant` |
| `active.subscription` | `ActiveSubscription`     | Only blocks `suspended` owners — `trial` status passes through |
| `plan.limit`          | `PlanLimit`              | Blocks unit creation when `owner->isAtPlanLimit()` returns true |

Owner routes chain `['auth.owner', 'active.subscription']`. The authenticated owner is always accessed as `$request->owner` in owner controllers, never via `auth('owner')->user()` directly.

### Controller Namespaces

```
app/Http/Controllers/
├── Auth/          ← Login/logout for all three roles
├── Admin/         ← Admin web portal
├── Owner/         ← Owner web portal
├── Tenant/        ← Tenant web portal
└── Api/
    ├── Owner/     ← REST API for Flutter (owner actions)
    └── Tenant/    ← REST API for Flutter (tenant actions)
```

### REST API

Base: `/api/v1` — authenticated via **Laravel Sanctum** Bearer token (not session). API controllers live in `app/Http/Controllers/Api/` and mirror the web controllers but return JSON. The API is used exclusively by the Flutter mobile app.

### BillingService

`app/Services/BillingService.php` centralises all billing logic:
- `generateMonthlyBills(int $ownerId, Carbon $month)` — creates `TenantBill` rows for all active contracts, calculates utility charges from `UtilityReading`
- `recordPayment(TenantBill $bill, array $data, int $recordedBy)` — wrapped in DB transaction; creates `Payment` row and updates bill status
- `updateBillUtilities(int $ownerId, int $unitId, Carbon $month)` — recalculates utility amounts on an existing bill after meter readings are updated

`BillingController` injects both `BillingService` and `GmailService` via constructor.

### GmailService

Each owner configures their own Gmail address + App Password in Settings. The password is AES-256 encrypted in the `owners` table. `GmailService` decrypts it at runtime and uses `PHPMailer` to send from the owner's own Gmail account. Billing notifications go through `SendBillingNotification` job dispatched to the `emails` queue.

### Models — Key Quirks

**`TenantNotification` — `$timestamps = false`:** The `tenant_notifications` table has no `created_at`/`updated_at` columns. Never use `->latest()` or `->orderBy('created_at')` on this model or its relation. Use `->orderByDesc('id')` instead.

**`AuditLog` — `$timestamps = false` with manual `created_at`:** The column exists in the DB with `DEFAULT CURRENT_TIMESTAMP`. `->latest()` works here. The model has no `updated_at` column.

**`TenantBill` accessor:** `$bill->balance_due` → computed as `max(0, total_amount - amount_paid)`.

## Coding Conventions

### Blade Templates

**No global helper functions exist.** There is no `app/Helpers/` directory. Never call `formatCurrency()`, `money()`, or similar — they will throw. Use `${{ number_format($value, 2) }}` directly.

**Always use null-safe operator on relations in views:**
```blade
{{-- Wrong — throws if tenant is null --}}
{{ $bill->tenant->full_name }}

{{-- Correct --}}
{{ $bill->tenant?->full_name ?? '—' }}
{{ $bill->unit?->property?->name ?? '—' }}
```

**Inline `@php` blocks must be on their own line.** If `@php...@endphp` and `{{ }}` appear on the same line, Blade outputs the raw `@__raw_block_0__` placeholder. Always do:
```blade
{{-- Wrong --}}
<div>@php $x = $a + $b; @endphp{{ $x > 0 ? $x : 0 }}</div>

{{-- Correct --}}
@php $x = $a + $b; @endphp
<div>{{ $x > 0 ? $x : 0 }}</div>
```

**After editing any view, run `php artisan view:clear`** if the output looks wrong — stale compiled views persist in `storage/framework/views/`.

### Layouts

Three layouts in `resources/views/layouts/`:
- `layouts.admin` — used by all admin views
- `layouts.owner` — used by all owner views; displays `session('new_creds')` as a copy-able credential card when set
- `layouts.tenant` — used by all tenant views

Flash `new_creds` as an array when creating users so the layout displays credentials cleanly:
```php
->with('new_creds', ['name' => $owner->full_name, 'email' => $owner->email, 'password' => $password])
```

### Adding a New Owner Feature

1. Add route inside the `Route::middleware(['auth.owner','active.subscription'])` group in `routes/web.php`
2. Create controller in `app/Http/Controllers/Owner/`
3. Access the authenticated owner via `$request->owner` (injected by `AuthOwner` middleware)
4. Scope all queries to the owner: `->where('owner_id', $owner->id)` — never trust route model binding alone for cross-owner data
5. Create view in `resources/views/owner/` extending `layouts.owner`
6. Mirror it in `app/Http/Controllers/Api/Owner/` if the Flutter app also needs it

### Adding a New Tenant Feature

Same pattern — use `$request->tenant` (injected by `AuthTenant` middleware) and scope queries to `tenant_id`.

### Plan Enforcement

Apply the `plan.limit` middleware to any route that creates apartments/units. Owners at their plan limit get redirected back with an error message. The limit is `owner->max_apartments` vs `owner->usedApartments()` (counts non-disposed units).

### Database

All tables are created in a single migration file: `database/migrations/2026_01_01_000001_create_som_tables.php`.

Demo data is seeded via `database/seeders/DatabaseSeeder.php` → `DemoPropertySeeder.php`.

`database/plans_setup.sql` contains the subscription plan seed data (run manually or via seeder if plans table is empty).

The `CACHE_STORE=file` and `SESSION_DRIVER=file` env vars must both be set (Laravel 11 reads `CACHE_STORE`, not just `CACHE_DRIVER`).
