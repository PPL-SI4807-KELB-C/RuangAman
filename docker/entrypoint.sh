#!/bin/sh
set -e
cd /var/www/html

# Render/Railway memberi port lewat $PORT
PORT="${PORT:-8080}"
sed -ri "s/Listen [0-9]+/Listen ${PORT}/" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \*:[0-9]+>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

# Sertifikat CA database (Aiven/TiDB) boleh dikirim sebagai isi env DB_SSL_CA_CONTENT
if [ -n "$DB_SSL_CA_CONTENT" ]; then
    printf '%s\n' "$DB_SSL_CA_CONTENT" > /var/www/html/storage/db-ca.pem
    export MYSQL_ATTR_SSL_CA=/var/www/html/storage/db-ca.pem
fi

php artisan storage:link >/dev/null 2>&1 || true
php artisan config:cache
php artisan route:cache
php artisan view:cache

if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    php artisan migrate --force
    # Seed data awal hanya jika tabel users masih kosong
    if [ "$(php artisan tinker --execute='echo \App\Models\User::count();' 2>/dev/null | tail -n1)" = "0" ]; then
        php artisan db:seed --force
    fi
fi

chown -R www-data:www-data storage bootstrap/cache
exec apache2-foreground
