FROM php:8.2-apache

# Install ekstensi PHP yang dibutuhkan Laravel & SQLite
RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo pdo_sqlite zip

# Fix Apache More than one MPM loaded error
RUN a2dismod mpm_event && a2enmod mpm_prefork

# Enable Apache Rewrite Module
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy semua file project ke container
COPY . /var/www/html

# Ubah document root Apache ke folder public Laravel
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install vendor dependencies
RUN composer install --no-dev --optimize-autoloader

# Atur hak akses folder storage dan bootstrap cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80