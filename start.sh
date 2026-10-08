#!/usr/bin/env bash

# Pastikan folder database ada dan buat file sqlite kosong jika belum ada
mkdir -p database
if [ ! -f database/database.sqlite ]; then
    touch database/database.sqlite
    echo "Database SQLite created."
fi

# Jalankan migrasi database
php artisan migrate --force

# Bersihkan dan cache ulang config
php artisan config:clear
php artisan cache:clear

# Jalankan server PHP bawaan Laravel
php -S 0.0.0.0:${PORT:-8080} -t public