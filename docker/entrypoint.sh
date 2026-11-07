#!/bin/sh
# Production entrypoint: cache config/routes/views, optionally migrate, then exec CMD.
set -e

php artisan storage:link --quiet 2>/dev/null || true
php artisan optimize
php artisan filament:optimize

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    php artisan migrate --force
fi

exec "$@"
