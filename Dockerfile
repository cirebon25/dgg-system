FROM php:8.3-apache

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        zip \
        exif \
        intl \
        gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy source
COPY . .

ENV COMPOSER_ALLOW_SUPERUSER=1

# Install composer packages
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --prefer-dist \
    --no-scripts

# Pastikan folder Laravel ada
RUN mkdir -p \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

# Permission untuk Apache (www-data)
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Generate cache Laravel
RUN php artisan package:discover --ansi || true

RUN php artisan optimize:clear

RUN php artisan config:cache

RUN php artisan route:cache || true

RUN php artisan view:cache || true

# ==== FIX APACHE MPM ERROR ====
# php:8.3-apache image bisa memuat lebih dari satu MPM module (event & prefork)
# yang menyebabkan "More than one MPM loaded". PHP module (mod_php) butuh
# mpm_prefork (non-threaded), jadi disable module MPM lain dan enable prefork saja.
RUN a2dismod mpm_event || true \
    && a2dismod mpm_worker || true \
    && a2enmod mpm_prefork \
    && a2enmod rewrite

COPY .docker/vhost.conf /etc/apache2/sites-available/000-default.conf

# ==== FIX PORT UNTUK RAILWAY ====
# Railway inject env var PORT secara dinamis, Apache default listen di 80.
# Ubah ports.conf & vhost config agar mengikuti $PORT saat container start.
RUN sed -i 's/80/${PORT}/g' /etc/apache2/ports.conf \
    && sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf

ENV PORT=80
EXPOSE 80

CMD ["sh", "-c", "apache2-foreground"]