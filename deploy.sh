#!/usr/bin/env bash
# ============================================================
# ClincOne — Production Deployment Script (shared hosting safe)
# Run from the project root after uploading new files.
# ============================================================
set -e

echo "==> [1/8] Installing Composer dependencies (no dev, optimised autoloader)..."
composer install --no-dev --optimize-autoloader --no-interaction --quiet

echo "==> [2/8] Building frontend assets..."
npm ci --silent
npm run build

echo "==> [3/8] Running database migrations..."
php artisan migrate --force --no-interaction

echo "==> [4/8] Caching configuration..."
php artisan config:cache

echo "==> [5/8] Caching routes..."
php artisan route:cache

echo "==> [6/8] Caching views..."
php artisan view:cache

echo "==> [7/8] Running artisan optimize (combines config+route+view caches + event discovery)..."
php artisan optimize

echo "==> [8/8] Clearing stale application cache..."
php artisan cache:clear

echo ""
echo "✓ Deployment complete."
echo ""
echo "  Reminder: ensure .env on the server has:"
echo "    APP_ENV=production"
echo "    APP_DEBUG=false"
echo "    CACHE_STORE=file"
echo "    SESSION_DRIVER=file"
echo "    QUEUE_CONNECTION=sync"
echo "    LOG_LEVEL=error"
