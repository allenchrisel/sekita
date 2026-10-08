#!/usr/bin/env bash

# 1. Pastikan folder database ada
mkdir -p /var/www/html/database

# 2. Paksa buat file database.sqlite jika belum ada atau ukurannya 0
if [ ! -f /var/www/html/database/database.sqlite ]; then
    touch /var/www/html/database/database.sqlite
    echo "File database.sqlite berhasil dibuat."
fi

# 3. Berikan izin penuh agar bisa dibaca dan ditulis oleh aplikasi
chmod 777 /var/www/html/database/database.sqlite
chmod -R 777 /var/www/html/storage
chmod -R 777 /var/www/html/bootstrap/cache

# 4. Jalankan migrasi database
php artisan migrate --force

# 5. Clear cache agar konfigurasi segar
php artisan config:clear
php artisan cache:clear

# 6. Jalankan server PHP
exec php -S 0.0.0.0:${PORT:-8080} -t public