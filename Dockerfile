FROM php:8.3-apache

# Install dependencies and PostgreSQL driver
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    unzip \
    git \
    curl \
    nodejs \
    npm \
    && docker-php-ext-install pdo_pgsql zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Update Apache port to match Render's $PORT environment variable
RUN sed -i "s/80/\${PORT:-80}/g" /etc/apache2/ports.conf /etc/apache2/sites-available/*.conf

# Change DocumentRoot to /var/www/html/public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

WORKDIR /var/www/html

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy project files
COPY . .

# Install PHP and Node dependencies, then build frontend assets
RUN composer install --optimize-autoloader --no-dev \
    && npm install \
    && npm run build

# Set permissions for Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Create a startup script that handles Laravel caching, migrations, and starts Apache
RUN echo '#!/bin/bash\n\
php artisan config:cache\n\
php artisan route:cache\n\
php artisan view:cache\n\
php artisan migrate --force\n\
apache2-foreground' > /usr/local/bin/start.sh \
    && chmod +x /usr/local/bin/start.sh

# Run startup script
CMD ["/usr/local/bin/start.sh"]
