# ===============================
# Stage 1 — Build Frontend
# ===============================
FROM node:20 AS node_builder

WORKDIR /app
COPY package*.json ./
RUN npm ci

COPY . .
RUN npm run build


# ===============================
# Stage 2 — PHP Dependencies
# ===============================
FROM composer:2 AS composer_builder

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts

COPY . .
RUN composer dump-autoload --optimize


# ===============================
# Stage 3 — Runtime (Caddy + PHP)
# ===============================
FROM caddy:2-alpine

# Install PHP-FPM inside Caddy image
RUN apk add --no-cache \
    php82 \
    php82-fpm \
    php82-pdo \
    php82-pdo_pgsql \
    php82-intl \
    php82-mbstring \
    php82-session \
    php82-opcache \
    php82-ctype \
    php82-fileinfo \
    php82-tokenizer \
    php82-dom \
    php82-xml \
    php82-simplexml \
    php82-curl \
    php82-zip \
    php82-openssl \
    php82-phar \
    bash

WORKDIR /var/www

COPY . .
COPY --from=composer_builder /app/vendor ./vendor
COPY --from=node_builder /app/public/build ./public/build

COPY Caddyfile /etc/caddy/Caddyfile

RUN mkdir -p /run/php

EXPOSE 80

CMD php82-fpm -D && caddy run --config /etc/caddy/Caddyfile --adapter caddyfile