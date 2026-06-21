#!/bin/bash
set -e

echo "── Deploying myhome-map ──────────────────────────"

git pull origin main

echo "→ Installing dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "→ Running migrations..."
php artisan migrate --force

echo "→ Clearing & caching config/routes/views..."
php artisan optimize:clear
php artisan optimize

echo "→ Fixing storage permissions..."
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache database/

echo "── Done ✓ ───────────────────────────────────────"
