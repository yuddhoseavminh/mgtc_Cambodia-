#!/bin/bash
set -e

APP_DIR="/var/www/html"
ENV_FILE="$APP_DIR/.env"

echo "----------------------------------------"
echo "  Army Registration - Container"
echo "----------------------------------------"

set_env() {
    local key="$1"
    local value="$2"

    php -r '
        $file = $argv[1];
        $key = $argv[2];
        $value = $argv[3];
        $lines = file_exists($file) ? file($file, FILE_IGNORE_NEW_LINES) : [];
        $found = false;

        foreach ($lines as &$line) {
            if (preg_match("/^".preg_quote($key, "/")."=/", $line)) {
                $line = $key."=".$value;
                $found = true;
            }
        }
        unset($line);

        if (! $found) {
            $lines[] = $key."=".$value;
        }

        file_put_contents($file, implode(PHP_EOL, $lines).PHP_EOL);
    ' "$ENV_FILE" "$key" "$value"
}

if [ ! -f "$ENV_FILE" ]; then
    ENV_TEMPLATE="$APP_DIR/.env.example"

    if [ "${APP_ENV:-production}" = "production" ] && [ -f "$APP_DIR/.env.production.example" ]; then
        ENV_TEMPLATE="$APP_DIR/.env.production.example"
    fi

    echo "[entrypoint] Creating .env from $(basename "$ENV_TEMPLATE")"
    cp "$ENV_TEMPLATE" "$ENV_FILE"
fi

set_env APP_ENV "${APP_ENV:-production}"
set_env APP_DEBUG "${APP_DEBUG:-false}"
set_env APP_URL "${APP_URL:-http://localhost}"
set_env LOG_LEVEL "${LOG_LEVEL:-warning}"
set_env DB_HOST "${DB_HOST:-127.0.0.1}"
set_env DB_PORT "${DB_PORT:-3306}"
set_env DB_DATABASE "${DB_DATABASE:-army_db}"
set_env DB_USERNAME "${DB_USERNAME:-root}"
set_env DB_PASSWORD "${DB_PASSWORD:-}"
set_env SESSION_DRIVER "${SESSION_DRIVER:-database}"
set_env QUEUE_CONNECTION "${QUEUE_CONNECTION:-database}"
set_env CACHE_STORE "${CACHE_STORE:-database}"
set_env FILESYSTEM_DISK "${FILESYSTEM_DISK:-local}"
set_env UPLOADS_DISK "${UPLOADS_DISK:-uploads}"
set_env LEGACY_UPLOADS_DISK "${LEGACY_UPLOADS_DISK:-local}"

if [ -n "${APP_KEY:-}" ]; then
    set_env APP_KEY "$APP_KEY"
fi

if [ -n "${TELEGRAM_ENABLED:-}" ]; then
    set_env TELEGRAM_ENABLED "$TELEGRAM_ENABLED"
fi

if [ -n "${TELEGRAM_BOT_TOKEN:-}" ]; then
    set_env TELEGRAM_BOT_TOKEN "$TELEGRAM_BOT_TOKEN"
fi

if [ -n "${TELEGRAM_CHAT_ID:-}" ]; then
    set_env TELEGRAM_CHAT_ID "$TELEGRAM_CHAT_ID"
fi

UPLOADS_ROOT_PATH="${UPLOADS_ROOT:-$APP_DIR/storage/app/uploads}"
set_env UPLOADS_ROOT "$UPLOADS_ROOT_PATH"

APP_KEY_VAL=$(grep "^APP_KEY=" "$ENV_FILE" | cut -d '=' -f2-)
if [ -z "$APP_KEY_VAL" ]; then
    echo "[entrypoint] Generating application key"
    php "$APP_DIR/artisan" key:generate --force
fi

mkdir -p \
    "$APP_DIR/storage/app/public" \
    "$APP_DIR/storage/framework/cache/data" \
    "$APP_DIR/storage/framework/sessions" \
    "$APP_DIR/storage/framework/views" \
    "$APP_DIR/storage/logs" \
    "$UPLOADS_ROOT_PATH"

chown -R www-data:www-data "$APP_DIR/storage" "$APP_DIR/bootstrap/cache" "$UPLOADS_ROOT_PATH"

echo "[entrypoint] Running migrations"
php "$APP_DIR/artisan" migrate --force --no-interaction

APP_ENV_VAL=$(grep "^APP_ENV=" "$ENV_FILE" | cut -d '=' -f2-)
APP_DEBUG_VAL=$(grep "^APP_DEBUG=" "$ENV_FILE" | cut -d '=' -f2-)

if [ "$APP_ENV_VAL" = "production" ] && [ "$APP_DEBUG_VAL" = "false" ]; then
    echo "[entrypoint] Caching production configuration"
    php "$APP_DIR/artisan" optimize:clear
    php "$APP_DIR/artisan" config:cache
    php "$APP_DIR/artisan" route:cache
    php "$APP_DIR/artisan" view:cache
else
    echo "[entrypoint] Clearing caches"
    php "$APP_DIR/artisan" optimize:clear
fi

echo "[entrypoint] Starting services..."
exec "$@"
