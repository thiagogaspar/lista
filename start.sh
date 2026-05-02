#!/bin/bash
set -x
echo "=== Starting server ==="
echo "PORT=$PORT"
echo "PHP version:"
php -v
echo "PHP modules:"
php -m
echo "Testing Laravel..."
cd /app
php artisan about --no-interaction 2>&1 || echo "Artisan about failed"
echo "Starting PHP server..."
exec php -S 0.0.0.0:$PORT -t /app/public
