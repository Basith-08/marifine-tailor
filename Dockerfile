############################
# Stage 1: Composer Builder
############################
FROM composer:2 AS composer_builder
WORKDIR /app
# Copy hanya file pendukung dulu agar cache layer efisien
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

COPY . .
RUN composer install --no-dev --optimize-autoloader

############################
# Stage 2: Node Builder
############################
FROM node:20-alpine AS node_builder
WORKDIR /app
COPY package*.json ./
# Tambahkan build tool jika ada library sass/native
RUN apk add --no-cache python3 make g++ 
RUN npm ci
COPY . .
RUN npm run build

############################
# Stage 3: Runtime
############################
FROM php:8.3-fpm-alpine

# Install runtime dependencies & Caddy
RUN apk add --no-cache \
    libpq \
    libpng \
    oniguruma \
    zip \
    unzip \
    bash \
    caddy

# Install & compile PHP extensions, lalu hapus build-deps agar image kecil
RUN apk add --no-cache --virtual .build-deps \
    postgresql-dev \
    libpng-dev \
    oniguruma-dev \
    && docker-php-ext-install \
    pdo \
    pdo_pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    opcache \
    && apk del .build-deps

WORKDIR /var/www

# Copy vendor dari composer_builder
COPY --from=composer_builder /app/vendor ./vendor
# Copy hasil build dari node_builder (Vite/Mix)
COPY --from=node_builder /app/public/build ./public/build
# Copy sisa source code
COPY . .

# Copy konfigurasi Caddy
COPY Caddyfile /etc/caddy/Caddyfile

# Set permission agar Laravel bisa nulis log/cache
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Setup Entrypoint
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80

ENTRYPOINT ["docker-