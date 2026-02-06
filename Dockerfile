FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    zip \
    && docker-php-ext-enable pdo pdo_mysql pdo_pgsql zip

# Enable Apache modules
RUN a2enmod rewrite headers

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data . \
    && chmod -R 755 . \
    && chmod -R 775 var/ public/

# Configure Apache
RUN cp -f config/apache/vhost.conf /etc/apache2/sites-available/000-default.conf \
    || echo "VirtualHost config optional"

# Expose port
EXPOSE 80 443

# Start Apache
CMD ["apache2-foreground"]
