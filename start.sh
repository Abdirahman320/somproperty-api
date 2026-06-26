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

# Seed initial data — seeder uses insertOrIgnore so safe to run every time
echo "==> Seeding initial data (safe, skips existing rows)..."
php artisan db:seed --force || echo "Seed had warnings (non-fatal)"

# Cache config, routes, views for performance
echo "==> Caching..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Starting Apache..."
exec apache2-foreground
