# Dùng PHP + Apache + Composer + SQLite
FROM php:8.2-apache

# Cài extension
RUN apt-get update && apt-get install -y \
    libzip-dev unzip zip curl sqlite3 libsqlite3-dev git \
    && docker-php-ext-install zip pdo pdo_sqlite

# Cài Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Tạo thư mục project
WORKDIR /var/www/html

# Copy source
COPY . .

# Cài thư viện Laravel
RUN composer install --optimize-autoloader --no-dev

# Tạo app key và cache config
RUN php artisan config:clear && php artisan key:generate

# Set quyền cho storage
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 80
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=80"]
