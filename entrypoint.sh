#!/bin/bash
set -e

echo "⏳ Waiting for Postgres database at ${DB_HOST}:${DB_PORT}..."

# Wait for Postgres to be ready
until pg_isready -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USERNAME"; do
  sleep 3
done

echo "✅ Database is ready!"

# Run Laravel migrations
php artisan migrate --force || true

# Cache Laravel config, routes, views
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "🚀 Starting Apache..."
exec apache2-foreground
