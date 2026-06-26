#!/bin/bash
set -e

echo "==> Setting up Laravel..."

# Regenerate package manifest (removes dev-only providers like Sail)
php artisan package:discover --ansi

# Generate app key if not set
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:GENERATE_WITH_php_artisan_key:generate" ]; then
  php artisan key:generate --force
fi

# Run migrations
echo "==> Running migrations..."
php artisan migrate --force

# Cache config, routes, views for performance
echo "==> Caching config..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Starting Apache..."
exec apache2-foreground
