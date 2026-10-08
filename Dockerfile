FROM php:8.2-cli

# Install ekstensi PHP yang dibutuhkan Laravel & SQLite
RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo pdo_sqlite zip

# Set working directory
WORKDIR /var/www/html

# Copy semua file project ke container
COPY . /var/www/html

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install vendor dependencies
RUN composer install --no-dev --optimize-autoloader

# Atur hak akses folder storage dan bootstrap cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Gunakan port dari environment Railway atau default ke 8080
ENV PORT=8080
EXPOSE 8080

# Jalankan Laravel menggunakan built-in server PHP mengarah ke folder public
CMD php -S 0.0.0.0:$PORT -t public