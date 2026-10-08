#!/usr/bin/env bash

echo "Starting deployment setup..."

# 1. Buat folder database jika belum ada
mkdir -p /var/www/html/database

# 2. Buat file database.sqlite kosong jika belum ada
if [ ! -f /var/www/html/database/database.sqlite ]; then
    touch /var/www/html/database/database.sqlite
    echo "Database SQLite file created successfully."
fi

# 3. Pastikan hak akses file dan folder benar-benar terbuka untuk ditulis
chown -R www-data:www-data /var/www/html/storage /var/www/html/database /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/database

# 4. Jalankan migrasi database
echo "Running database migrations..."
php artisan migrate --force

# 5. Bersihkan cache config
php artisan config:clear
php artisan cache:clear

echo "Setup completed. Starting PHP server..."

# 6. Jalankan server PHP bawaan Laravel
exec php -S 0.0.0.0:${PORT:-8080} -t public