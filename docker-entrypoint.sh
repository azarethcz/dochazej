#!/bin/sh
set -e

# Create .env from the example on first run if it doesn't exist yet
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Generate an app key only if one isn't already set
if ! grep -q "^APP_KEY=base64" .env 2>/dev/null; then
    php artisan key:generate --force
fi

# Make sure the SQLite file exists (volumes can start empty)
mkdir -p database
touch database/database.sqlite

php artisan config:clear
php artisan migrate --force

exec "$@"
