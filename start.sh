#!/bin/bash
set -e

echo "==> Setting up Laravel..."

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

# Run migrations
echo "==> Running migrations..."
php artisan migrate --force

# Cache config, routes, views for performance
echo "==> Caching..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Starting Apache..."
exec apache2-foreground
