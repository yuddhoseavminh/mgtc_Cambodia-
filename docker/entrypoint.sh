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

# Override env vars if passed
sed -i "s|^APP_ENV=.*|APP_ENV=${APP_ENV:-production}|" /var/www/html/.env
sed -i "s|^APP_DEBUG=.*|APP_DEBUG=${APP_DEBUG:-false}|" /var/www/html/.env
sed -i "s|^APP_URL=.*|APP_URL=${APP_URL:-http://localhost}|" /var/www/html/.env
sed -i "s|^LOG_LEVEL=.*|LOG_LEVEL=${LOG_LEVEL:-warning}|" /var/www/html/.env
sed -i "s|^DB_HOST=.*|DB_HOST=${DB_HOST:-127.0.0.1}|" /var/www/html/.env
sed -i "s|^DB_DATABASE=.*|DB_DATABASE=${DB_DATABASE:-army_db}|" /var/www/html/.env
sed -i "s|^DB_USERNAME=.*|DB_USERNAME=${DB_USERNAME:-root}|" /var/www/html/.env
sed -i "s|^DB_PASSWORD=.*|DB_PASSWORD=${DB_PASSWORD:-}|" /var/www/html/.env
sed -i "s|^FILESYSTEM_DISK=.*|FILESYSTEM_DISK=${FILESYSTEM_DISK:-local}|" /var/www/html/.env
sed -i "s|^UPLOADS_DISK=.*|UPLOADS_DISK=${UPLOADS_DISK:-uploads}|" /var/www/html/.env
sed -i "s|^LEGACY_UPLOADS_DISK=.*|LEGACY_UPLOADS_DISK=${LEGACY_UPLOADS_DISK:-local}|" /var/www/html/.env

UPLOADS_ROOT_PATH="${UPLOADS_ROOT:-/var/www/html/storage/app/uploads}"

if grep -q "^UPLOADS_ROOT=" /var/www/html/.env; then
    sed -i "s|^UPLOADS_ROOT=.*|UPLOADS_ROOT=${UPLOADS_ROOT_PATH}|" /var/www/html/.env
else
    printf "\nUPLOADS_ROOT=%s\n" "$UPLOADS_ROOT_PATH" >> /var/www/html/.env
fi

# Generate app key if empty
APP_KEY_VAL=$(grep "^APP_KEY=" /var/www/html/.env | cut -d '=' -f2)
if [ -z "$APP_KEY_VAL" ]; then
    echo "[entrypoint] Generating application key"
    php /var/www/html/artisan key:generate --force
fi

# Run migrations
echo "[entrypoint] Running migrations"
php /var/www/html/artisan migrate --force --no-interaction

mkdir -p "$UPLOADS_ROOT_PATH"

# Fix permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chown -R www-data:www-data "$UPLOADS_ROOT_PATH"

APP_ENV_VAL=$(grep "^APP_ENV=" /var/www/html/.env | cut -d '=' -f2)
APP_DEBUG_VAL=$(grep "^APP_DEBUG=" /var/www/html/.env | cut -d '=' -f2)

if [ "$APP_ENV_VAL" = "production" ] && [ "$APP_DEBUG_VAL" = "false" ]; then
    echo "[entrypoint] Caching production configuration"
    php /var/www/html/artisan config:cache
    php /var/www/html/artisan route:cache
    php /var/www/html/artisan view:cache
else
    echo "[entrypoint] Clearing caches"
    php /var/www/html/artisan config:clear
    php /var/www/html/artisan cache:clear
    php /var/www/html/artisan view:clear
fi

echo "[entrypoint] Starting services…"
exec "$@"
