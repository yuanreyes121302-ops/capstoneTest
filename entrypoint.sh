#!/bin/bash
set -e

# Wait for database
until mysql -h"$DB_HOST" -u"$DB_USERNAME" -p"$DB_PASSWORD" -e "select 1" &> /dev/null; do
  echo "⏳ Waiting for database..."
  sleep 3
done

# Run migrations
php artisan migrate --force || true

# Start Apache
exec apache2-foreground
