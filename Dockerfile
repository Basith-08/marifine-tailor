# ===============================
# Stage 1 — Build Frontend (Vite)
# ===============================
FROM node:20 AS node_builder

WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY . .
RUN npm run build


# ===============================
# Stage 2 — Install PHP Deps
# ===============================
FROM composer:2 AS composer_builder

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts

COPY . .
RUN composer dump-autoload --optimize


# ===============================
# Stage 3 — Production Runtime
# ===============================
FROM php:8.2-fpm-alpine

# Install system deps
RUN apk add --no-cache \
    bash \
    libpq-dev \
    icu-dev \
    oniguruma-dev \
    zip \
    unzip \
    curl \
    caddy \
    && docker-php-ext-install pdo pdo_pgsql intl

WORKDIR /var/www

# Copy application
COPY . .

# Copy vendor from composer stage
COPY --from=composer_builder /app/vendor ./vendor

# Copy built assets
COPY --from=node_builder /app/public/build ./public/build

# Permissions
RUN chown -R www-data:www-data storage bootstrap/cache

# Copy Caddyfile
COPY Caddyfile /etc/caddy/Caddyfile

EXPOSE 80

CMD php artisan migrate --force && php-fpm -D && caddy run --config /etc/caddy/Caddyfile --adapter caddyfile