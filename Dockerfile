# Dockerfile
FROM php:8.2-fpm

# Install necessary PHP extensions and dependencies
RUN apt-get update && \
    apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    sqlite3 \
    libsqlite3-dev \
    zip \
    unzip \
    git && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd pdo pdo_sqlite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy app code
WORKDIR /var/www/html
COPY . .

# Set environment variable to allow Composer to run as root
ENV COMPOSER_ALLOW_SUPERUSER=1

# Install project dependencies
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Copy Nginx configuration
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf

# Expose port 80
EXPOSE 80

# Start PHP-FPM (without migration command)
CMD ["php-fpm"]
