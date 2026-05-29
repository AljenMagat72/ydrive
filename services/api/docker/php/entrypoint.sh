#!/bin/sh
set -e

# Bind mount may omit vendor/ on a fresh clone; Saloon is required for AutofleetApi (Connector).
if [ ! -f /var/www/vendor/saloonphp/saloon/src/Http/Connector.php ]; then
  echo "laravel-entrypoint: Saloon missing from vendor; running composer install..."
  (cd /var/www && composer install --no-interaction --prefer-dist --no-progress)
fi

# PHP-FPM runs as www-data; bind-mounted project files are often owned by the host UID.
# Ensure Laravel writable paths exist and are owned by www-data before FPM starts.
mkdir -p /var/www/storage/logs \
  /var/www/storage/framework/cache/data \
  /var/www/storage/framework/sessions \
  /var/www/storage/framework/views \
  /var/www/bootstrap/cache

chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R ug+rwx /var/www/storage /var/www/bootstrap/cache

exec docker-php-entrypoint "$@"
