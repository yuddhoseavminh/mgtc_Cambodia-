#!/bin/bash
set -e

echo "──────────────────────────────────"
echo "  Army Registration – Container  "
echo "──────────────────────────────────"

# Copy .env if not present
if [ ! -f /var/www/html/.env ]; then
    echo "[entrypoint] Creating .env from .env.example"
    cp /var/www/html/.env.example /var/www/html/.env
fi

# Override DB env vars if passed
sed -i "s|^DB_HOST=.*|DB_HOST=${DB_HOST:-127.0.0.1}|" /var/www/html/.env
sed -i "s|^DB_DATABASE=.*|DB_DATABASE=${DB_DATABASE:-army_db}|" /var/www/html/.env
sed -i "s|^DB_USERNAME=.*|DB_USERNAME=${DB_USERNAME:-root}|" /var/www/html/.env
sed -i "s|^DB_PASSWORD=.*|DB_PASSWORD=${DB_PASSWORD:-}|" /var/www/html/.env

# Generate app key if empty
APP_KEY_VAL=$(grep "^APP_KEY=" /var/www/html/.env | cut -d '=' -f2)
if [ -z "$APP_KEY_VAL" ]; then
    echo "[entrypoint] Generating application key"
    php /var/www/html/artisan key:generate --force
fi

# Run migrations
echo "[entrypoint] Running migrations"
php /var/www/html/artisan migrate --force --no-interaction

# Clear caches
echo "[entrypoint] Clearing caches"
php /var/www/html/artisan config:clear
php /var/www/html/artisan cache:clear
php /var/www/html/artisan view:clear

# Fix permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

echo "[entrypoint] Starting services…"
exec "$@"
