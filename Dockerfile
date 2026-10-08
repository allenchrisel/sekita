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

# Clear dan reset cache Laravel agar tidak error 500
RUN php artisan config:clear
RUN php artisan cache:clear

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

ENV PORT=8080
EXPOSE 8080

CMD php -S 0.0.0.0:$PORT -t public