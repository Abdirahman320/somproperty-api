#!/bin/bash
set -e

echo "==> Setting up Laravel..."
echo "DEBUG: DB_HOST=${DB_HOST} APP_KEY_SET=${APP_KEY:+yes}"

# Create .env if missing — artisan needs a file to write APP_KEY into
if [ ! -f /var/www/html/.env ]; then
  touch /var/www/html/.env
fi

# Regenerate package manifest (removes dev-only providers like Sail)
php artisan package:discover --ansi

# Generate app key only if not already provided by the host environment
if [ -z "$APP_KEY" ]; then
  php artisan key:generate --force
fi

# Run migrations — only apply new ones, never wipe existing data
echo "==> Running migrations..."
php artisan migrate --force

# Seed only if the admin_users table is empty (first deploy)
ADMIN_COUNT=$(php artisan tinker --no-interaction --execute="echo \App\Models\AdminUser::count();" 2>/dev/null | tail -1 | tr -d '[:space:]')
if [ "$ADMIN_COUNT" = "0" ] || [ -z "$ADMIN_COUNT" ]; then
  echo "==> Seeding initial data (first deploy)..."
  php artisan db:seed --force
fi

# Cache config, routes, views for performance
echo "==> Caching..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Starting Apache..."
exec apache2-foreground
