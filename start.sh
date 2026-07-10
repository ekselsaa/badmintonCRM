#!/bin/bash
set -e

echo "=== Running migrations ==="
php artisan migrate --force

echo "=== Running database seeds ==="
php artisan db:seed --force


echo "=== Creating storage link ==="
php artisan storage:link || true

echo "=== Optimizing application ==="
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "=== Starting server ==="
php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
