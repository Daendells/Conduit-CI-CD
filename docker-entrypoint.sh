#!/bin/bash
set -e

echo "==> Clearing config cache..."
php artisan config:clear
php artisan cache:clear

echo "==> Running migrations..."
php artisan migrate --force

echo "==> Setting storage permissions..."
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

echo "==> Starting Apache..."
exec apache2-foreground
