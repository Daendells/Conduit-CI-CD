#!/bin/bash
# Do NOT use 'set -e' — Apache must always start even if artisan commands fail

echo "==> Clearing all caches (config, view, route)..."
php artisan config:clear 2>&1 || true
php artisan cache:clear 2>&1 || true
php artisan view:clear 2>&1 || true
php artisan route:clear 2>&1 || true

echo "==> Running migrations (non-blocking)..."
php artisan migrate --force 2>&1 || echo "[WARN] Migrations failed — continuing startup"

echo "==> Setting storage permissions..."
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache 2>&1 || true
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>&1 || true

echo "==> Starting Apache on port 8080..."
exec apache2-foreground
