#!/bin/sh
set -e

# PHP-FPM runs as www-data; bind-mounted storage must be writable by it.
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R ug+rwx /var/www/storage /var/www/bootstrap/cache

exec docker-php-entrypoint "$@"
