#!/bin/sh
set -e

# Production session defaults (overridable via env)
export SESSION_SECURE_COOKIE="${SESSION_SECURE_COOKIE:-true}"
export SESSION_SAME_SITE="${SESSION_SAME_SITE:-lax}"

echo "=== LISTA bootstrap ==="

# Ensure writable directories
mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs
mkdir -p bootstrap/cache
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# Cache config (non-fatal)
php artisan config:cache && echo "[OK] config:cache" || echo "[FAIL] config:cache"

# Run migrations
php artisan migrate --force --no-interaction && echo "[OK] migrate" || echo "[FAIL] migrate"

# Ensure admin user exists with correct role
php artisan app:create-admin-user && echo "[OK] create-admin-user" || echo "[FAIL] create-admin-user"

echo "=== Starting server ==="
exec frankenphp php-server --root=/app/public --listen=:8080
