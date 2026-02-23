# ===============================
# Stage 1 — Composer (PHP ready)
# ===============================
FROM composer:2 AS composer_builder

WORKDIR /app

COPY . .

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader

# Generate wayfinder types (butuh artisan & vendor)
RUN php artisan wayfinder:generate --with-form


# ===============================
# Stage 2 — Node build
# ===============================
FROM node:20-alpine AS node_builder

WORKDIR /app

ENV SKIP_WAYFINDER=true

COPY package*.json ./
RUN npm ci

COPY . .
COPY --from=composer_builder /app/vendor ./vendor

RUN npm run build

# ===============================
# Stage 3 — Runtime (Caddy + PHP)
# ===============================
FROM caddy:2-alpine

RUN apk add --no-cache \
    php83 \
    php83-fpm \
    php83-pdo \
    php83-pdo_pgsql \
    php83-intl \
    php83-mbstring \
    php83-session \
    php83-opcache \
    php83-ctype \
    php83-fileinfo \
    php83-tokenizer \
    php83-dom \
    php83-xml \
    php83-simplexml \
    php83-curl \
    php83-zip \
    php83-openssl \
    php83-phar \
    php83-iconv \
    bash

WORKDIR /var/www

COPY . .
COPY --from=composer_builder /app/vendor ./vendor
COPY --from=node_builder /app/public/build ./public/build

COPY Caddyfile /etc/caddy/Caddyfile

RUN mkdir -p /run/php

EXPOSE 80

CMD php-fpm83 -D && caddy run --config /etc/caddy/Caddyfile --adapter caddyfile