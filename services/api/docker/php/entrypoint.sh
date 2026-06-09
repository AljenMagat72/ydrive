#!/bin/bash
set -e

php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache

npm run build

php artisan queue:work & exec frankenphp run --config /app/Caddyfile
