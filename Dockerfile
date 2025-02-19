# Use the official PHP image with Alpine Linux
FROM php:8.2-fpm-alpine

# Set working directory inside the container
WORKDIR /var/www/html

# Install necessary dependencies
RUN apk add --no-cache \
    bash \
    curl \
    git \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    icu-dev \
    g++ \
    make \
    autoconf \
    openssl-dev \
    oniguruma-dev \
    mysql-client

# Install PHP extensions
RUN docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install pdo_mysql zip gd intl bcmath opcache

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy the Laravel application files into the container
COPY . .

# Install Laravel dependencies
RUN composer install --optimize-autoloader --no-dev

# Set permissions for Laravel storage and bootstrap directories
RUN chmod -R 775 storage bootstrap/cache

# Expose port 9000 for PHP-FPM
EXPOSE 9000

# Start PHP-FPM service
CMD ["php-fpm"]