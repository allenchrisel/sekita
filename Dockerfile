FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo pdo_sqlite zip

WORKDIR /var/www/html
COPY . /var/www/html

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Berikan izin eksekusi ke script start.sh
COPY start.sh /var/www/html/start.sh
RUN chmod +x /var/www/html/start.sh

# Atur hak akses folder
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/database /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/database

ENV PORT=8080
EXPOSE 8080

CMD ["/var/www/html/start.sh"]