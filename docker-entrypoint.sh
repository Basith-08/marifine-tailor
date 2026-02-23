#!/bin/sh
set -e

echo "🚀 Bootstrapping Laravel..."

# Cache config & routes untuk kecepatan di production
# Pastikan APP_KEY sudah ada di Environment Variable Railway
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Jalankan PHP-FPM di background (daemon mode)
echo "Starting PHP-FPM..."
php-fpm -D

# Jalankan Caddy di foreground sebagai process utama
# Jika Caddy mati, container akan otomatis restart
echo "Starting Caddy on port $PORT..."
exec caddy run --config /etc/caddy/Caddyfile --adapter caddyfile