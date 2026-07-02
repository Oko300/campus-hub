# Use an official PHP image as a base
FROM php:8.3-fpm-alpine

# Install system dependencies and PHP extensions
RUN apk add --no-cache \
    nginx \
    postgresql-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Copy Nginx configuration
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Copy PHP-FPM configuration
COPY docker/php-fpm.conf /etc/php83/php-fpm.d/www.conf

# Set working directory
WORKDIR /var/www/html

# Copy application code
COPY . /var/www/html

# Expose port 80 for Nginx
EXPOSE 80

# Start Nginx and PHP-FPM
CMD sh -c "nginx && php-fpm"