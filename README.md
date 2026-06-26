# SOM Property Management — Web Application
**PHP 8.2 + Laravel 11 + MySQL 8 + Redis**

## Quick Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
# Configure .env with your MySQL credentials, then:
php artisan migrate --seed
php artisan serve
# In a second terminal:
php artisan queue:work --queue=emails,default
```

## Default Credentials

| Role          | Email                     | Password      |
|---------------|---------------------------|---------------|
| System Admin  | admin@somproperty.com     | Admin@12345   |
| Demo Owner    | owner@demo.com            | Owner@12345   |

## URL Map

| URL                        | Role   | Page                        |
|----------------------------|--------|-----------------------------|
| `/admin/login`             | Admin  | Admin login                 |
| `/admin/dashboard`         | Admin  | System dashboard            |
| `/admin/owners`            | Admin  | Create & manage owners      |
| `/admin/subscriptions`     | Admin  | MRR & subscriptions         |
| `/admin/analytics`         | Admin  | Platform analytics          |
| `/admin/plans`             | Admin  | Plans & pricing             |
| `/admin/revenue`           | Admin  | Revenue breakdown           |
| `/admin/settings`          | Admin  | System settings             |
| `/admin/audit`             | Admin  | Audit logs                  |
| `/owner/login`             | Owner  | Owner login                 |
| `/owner/dashboard`         | Owner  | Owner dashboard             |
| `/owner/properties`        | Owner  | Properties & units          |
| `/owner/tenants`           | Owner  | Tenant management           |
| `/owner/billing`           | Owner  | Billing manager             |
| `/owner/notifications`     | Owner  | Send notifications          |
| `/owner/assets`            | Owner  | Assets & issues             |
| `/owner/complaints`        | Owner  | Complaint management        |
| `/owner/reports`           | Owner  | Financial reports           |
| `/owner/settings`          | Owner  | Account & Gmail settings    |
| `/tenant/login`            | Tenant | Tenant login                |
| `/tenant/home`             | Tenant | Home & contract info        |
| `/tenant/billing`          | Tenant | View bills & receipts       |
| `/tenant/complaints`       | Tenant | Submit & track complaints   |
| `/tenant/notifications`    | Tenant | Notifications               |

## REST API (for Flutter mobile app)

Base URL: `https://yourdomain.com/api/v1`
Auth: Laravel Sanctum Bearer token

| Method | Endpoint                            | Description                        |
|--------|-------------------------------------|------------------------------------|
| POST   | `/auth/owner/login`                 | Owner login → MySQL owners table   |
| POST   | `/auth/tenant/login`                | Tenant login → MySQL tenants table |
| GET    | `/owner/dashboard`                  | Dashboard stats from MySQL         |
| GET    | `/owner/properties`                 | Properties list                    |
| GET    | `/owner/tenants`                    | Tenants list                       |
| GET    | `/owner/billing`                    | Bills for current month            |
| POST   | `/owner/billing/generate`           | Generate monthly bills             |
| POST   | `/owner/billing/notify-all`         | Queue email notifications          |
| POST   | `/owner/billing/bills/{id}/pay`     | Record payment                     |
| POST   | `/owner/billing/utility-readings`   | Save meter reading                 |
| GET    | `/owner/complaints`                 | Complaints list                    |
| PUT    | `/owner/complaints/{id}`            | Update complaint status            |
| POST   | `/owner/complaints/{id}/reply`      | Reply to complaint                 |
| GET    | `/owner/assets`                     | Assets & open issues               |
| POST   | `/owner/notifications/send`         | Send bulk notifications via Gmail  |
| GET    | `/tenant/home`                      | Home screen data                   |
| GET    | `/tenant/bills`                     | Tenant bill history                |
| GET    | `/tenant/complaints`                | Tenant complaints                  |
| POST   | `/tenant/complaints`                | Submit new complaint               |
| GET    | `/tenant/notifications`             | Tenant notifications               |
| PUT    | `/tenant/notifications/{id}/read`   | Mark notification read             |

## Subscription Plans

| Plan    | Price  | Max Apartments | Features                                            |
|---------|--------|----------------|-----------------------------------------------------|
| Pro     | $20/mo | 13             | All core features                                   |
| Premium | $30/mo | 27             | + Asset register, technical issues                  |
| Maxi    | $50/mo | 49             | + Bulk notifications, financial analytics           |
| Maxi-2  | $100/mo| 99             | + Multi-property, priority support                  |
| Maxi-3  | $150/mo| 149            | + API access, custom branding, dedicated support    |

**All plans include:** Tenant portal, Rent billing, Email/Gmail notifications,
Complaint tracking, Advanced reports, Water & electric billing,
Contract management, PDF bill exports.

## Gmail SMTP Setup Per Owner

1. Owner → Settings → Gmail Configuration
2. Enter Gmail address + App Password
   (Google Account → Security → 2-Step Verification → App Passwords)
3. System encrypts password with AES-256 and stores in MySQL `owners` table
4. All billing/notification emails sent from the owner's own Gmail

## Production Deployment

```bash
# Ubuntu 22.04 server
sudo apt install -y nginx mysql-server php8.2-fpm php8.2-mysql php8.2-redis php8.2-gd php8.2-zip php8.2-curl redis-server

# Set up cron for scheduled tasks
* * * * * cd /var/www/som-property && php artisan schedule:run >> /dev/null 2>&1

# Queue worker as systemd service
# /etc/systemd/system/som-queue.service
# ExecStart=php /var/www/som-property/artisan queue:work --queue=emails,default --sleep=3 --tries=3
```

## Scheduled Tasks

| Schedule         | Task                                      |
|------------------|-------------------------------------------|
| Daily 09:00      | Send overdue payment reminders            |
| Monthly 25th     | Auto-generate next month's bills          |
| Daily            | Warn owners of contracts expiring in 30d  |
| Daily 00:05      | Auto-mark overdue bills                   |
| Weekly           | Clean up old notification logs            |
