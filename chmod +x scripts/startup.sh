#!/usr/bin/env bash
set -e

# install php deps (no dev)
composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# app key (if APP_KEY not set, generate one and print it)
if [ -z "$APP_KEY" ]; then
  echo "APP_KEY not set — generating one for the current container (not persisted)"
  php artisan key:generate --show
fi

# cache config and routes
php artisan config:cache || true
php artisan route:cache || true

# run migrations (force in CI / non-interactive)
php artisan migrate --force || true

# start php-fpm + nginx (this depends on your Dockerfile NGINX config)
# the Dockerfile should already configure the server to run (no explicit start here)
exec "$@"
