# Production image for the Thunder (Albarq) Laravel storefront.
# Local XAMPP dev is untouched; this is only used for staging/production builds.
# Build: docker build -t thunder . / Run migrations separately, never on boot.

# --- Frontend build ---
FROM node:20-alpine AS frontend
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci && npm install --no-save @rollup/rollup-linux-x64-musl lightningcss-linux-x64-musl @tailwindcss/oxide-linux-x64-musl
COPY resources resources
COPY vite.config.js ./
COPY public public
RUN npm run build

# --- Backend ---
FROM php:8.2-apache AS backend
WORKDIR /var/www/html

# System deps + PHP extensions required by Laravel 12 + bcmath (WalletService).
# $0 free tier: Neon/Supabase Postgres needs pdo_pgsql; keep pdo_mysql for local XAMPP compat.
RUN apt-get update && apt-get install -y --no-install-recommends \
    git unzip libzip-dev libpng-dev libonig-dev libxml2-dev libpq-dev \
    && docker-php-ext-install pdo_mysql pdo_pgsql bcmath mbstring zip exif pcntl \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Apache must serve Laravel's public/ directory.
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
# Render injects PORT=10000 and scans it; Apache defaults to 80 (invisible → "No open ports").
ENV PORT=10000
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --no-scripts

COPY . .
COPY --from=frontend /app/public/build public/build

RUN composer dump-autoload --optimize \
    && php artisan storage:link || true \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 10000
CMD ["sh", "-c", "sed -i \"s/Listen 80/Listen ${PORT:-10000}/\" /etc/apache2/ports.conf && sed -i \"s/:80>/:${PORT:-10000}>/\" /etc/apache2/sites-available/000-default.conf && apache2-foreground"]
