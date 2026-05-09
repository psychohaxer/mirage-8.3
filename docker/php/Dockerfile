FROM php:8.3-fpm-alpine

# Mengizinkan pengaturan UID/GID dari argumen build
ARG APP_UID=1000
ARG APP_GID=1000

# Install system dependencies
RUN apk add --no-cache \
    bash \
    shadow \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    icu-dev \
    oniguruma-dev \
    nodejs \
    npm

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql gd zip intl bcmath mbstring exif opcache pcntl

# Tambahkan konfigurasi batas upload hingga 100MB
RUN echo -e "upload_max_filesize = 100M\npost_max_size = 100M\nvariables_order = EGPCS" > /usr/local/etc/php/conf.d/uploads.ini

# Sinkronisasi user www-data dengan host secara dinamis
RUN usermod -u ${APP_UID} www-data && groupmod -g ${APP_GID} www-data

# Dapatkan Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
