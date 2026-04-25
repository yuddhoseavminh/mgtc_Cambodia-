# Stage 1 – Composer dependencies (vendor/)
FROM composer:2.7 AS composer

WORKDIR /app

COPY composer.json composer.lock ./

# Install production + dev dependencies (needed for tests)
RUN composer install \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist \
    --optimize-autoloader \
    --ignore-platform-reqs

# ============================================================
#  Stage 2 – Node / Vite assets (public/build)
# ============================================================
FROM node:20-alpine AS node

WORKDIR /app

COPY package.json package-lock.json vite.config.js ./
COPY resources/ resources/

RUN npm ci --silent && npm run build

# Stage 3 – Final PHP runtime image
FROM php:8.4-fpm-alpine

LABEL maintainer="army_from_register"
LABEL description="Laravel 12 – Army Registration System"

# ---------- OS / PHP extensions ----------
# Use install-php-extensions for reliable, automated dependency resolution
ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN apk add --no-cache \
        bash \
        curl \
        nginx \
        supervisor \
        mariadb-client \
        zip \
        unzip \
    && install-php-extensions \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        opcache

# ---------- PHP configuration ----------
COPY docker/php/php.ini   /usr/local/etc/php/conf.d/custom.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# ---------- Nginx configuration ----------
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf

# ---------- Supervisor configuration ----------
COPY docker/supervisor/supervisord.conf /etc/supervisord.conf

WORKDIR /var/www/html

# Copy application source
COPY . .

# Copy pre-built vendor & public assets from previous stages
COPY --from=composer /app/vendor ./vendor
COPY --from=node     /app/public/build ./public/build

# ---------- Permissions ----------
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 storage bootstrap/cache

# ---------- Entrypoint ----------
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
