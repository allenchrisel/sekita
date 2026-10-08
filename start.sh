#!/usr/bin/env sh
set -eu

if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    database_path="${DB_DATABASE:-/var/www/html/database/database.sqlite}"
    mkdir -p "$(dirname "$database_path")"
    if [ ! -f "$database_path" ]; then
        touch "$database_path"
    fi
fi

mkdir -p storage/app/private_documents storage/app/public \
    storage/framework/cache/data storage/framework/sessions \
    storage/framework/views storage/logs bootstrap/cache

php artisan migrate --force

exec php -S "0.0.0.0:${PORT:-8080}" -t public
