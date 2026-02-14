FROM php:8.2-apache

# Install dependencies and extensions
RUN apt-get update && apt-get install -y \
    libzip-dev \
    git \
    unzip \
    && docker-php-ext-install pdo pdo_mysql zip \
    && pecl install redis \
    && docker-php-ext-enable redis

# Enable Apache Rewrite Module
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Configure Apache DocumentRoot to /var/www/html/public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Copy composer files first for caching
COPY composer.json composer.lock ./

# Install dependencies (only those that don't need the rest of the code yet)
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-autoloader

# Copy application files
COPY . .

# Finish composer install (dump autoload to include the copied files)
RUN composer dump-autoload --optimize

# Set permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
