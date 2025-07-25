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

# Copy .env
COPY .env.example .env

# Cài thư viện Laravel
RUN composer install --optimize-autoloader --no-dev

# Set quyền cho thư mục cần thiết
RUN chmod -R 775 storage bootstrap/cache

# Chạy các lệnh Artisan
RUN php artisan config:clear && php artisan key:generate

EXPOSE 80
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=80"]
