# ==========================
# STAGE 1: Build assets (Vite)
# ==========================
FROM node:18-alpine AS build-assets
WORKDIR /app

# Set timezone untuk Node.js stage
RUN apk add --no-cache tzdata \
 && cp /usr/share/zoneinfo/Asia/Jakarta /etc/localtime \
 && echo "Asia/Jakarta" > /etc/timezone \
 && apk del tzdata

# Cache deps (copy package files first for better layer caching)
COPY package*.json vite.config.js ./
RUN npm ci

# Copy source and build
COPY resources ./resources
COPY public ./public
RUN npm run build

# ==========================
# STAGE 2: PHP-FPM + Nginx
# ==========================
FROM php:8.3-fpm-alpine

# Set timezone untuk PHP stage
RUN apk add --no-cache tzdata \
 && cp /usr/share/zoneinfo/Asia/Jakarta /etc/localtime \
 && echo "Asia/Jakarta" > /etc/timezone \
 && apk del tzdata

# Install sistem & ekstensi (kombinasi RUN untuk reduce layers)
RUN apk add --no-cache \
    nginx supervisor git unzip postgresql-client \
    libpq-dev oniguruma-dev libxml2-dev libzip-dev \
    libjpeg-turbo-dev libpng-dev freetype-dev icu-dev \
    sqlite-dev \
 && apk add --no-cache --virtual .build-deps \
    autoconf gcc g++ make \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install -j$(nproc) \
    pdo pdo_mysql pdo_pgsql pgsql pdo_sqlite \
    mbstring pcntl zip bcmath gd exif intl \
 && pecl install redis \
 && docker-php-ext-enable redis opcache \
 && apk del .build-deps \
 && rm -rf /var/cache/apk/*

WORKDIR /var/www/html

# Composer (copy dari official image untuk cache)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Cache composer dependencies (copy composer files first)
COPY composer.json composer.lock ./
RUN composer install --optimize-autoloader --no-interaction --prefer-dist --no-scripts \
 && composer clear-cache

# Copy application code
COPY . .

# Copy environment dan built assets
COPY .env.example .env
COPY --from=build-assets /app/public/build ./public/build

# Setup Laravel (kombinasi RUN untuk reduce layers)
RUN rm -rf bootstrap/cache/*.php \
 && mkdir -p bootstrap/cache storage/logs storage/app/public \
 && rm -rf public/storage \
 && chown -R www-data:www-data bootstrap/cache storage \
 && chmod -R 775 bootstrap/cache storage \
 && php artisan config:clear \
 && composer dump-autoload --optimize \
 && php artisan storage:link \
 && chown -R www-data:www-data public/storage

# Configure PHP timezone
RUN echo "date.timezone = Asia/Jakarta" > /usr/local/etc/php/conf.d/timezone.ini

# Disable runtime signature/header that reveals PHP version
RUN echo "expose_php = Off" > /usr/local/etc/php/conf.d/security.ini

# Copy config files
COPY docker/nginx-laravel.conf /etc/nginx/http.d/laravel.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/entrypoint.sh /entrypoint.sh

# Setup Nginx dan Supervisor (kombinasi untuk reduce layers)
RUN rm -f /etc/nginx/http.d/default.conf \
 && mkdir -p /var/log/nginx /run/nginx \
 && chmod +x /entrypoint.sh

# Expose Nginx (bukan FPM)
EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]