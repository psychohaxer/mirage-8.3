FROM php:8.3-fpm-alpine AS builder

RUN apk add --no-cache --virtual .build-deps \
    $PHPIZE_DEPS \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libxml2-dev \
    libzip-dev \
    oniguruma-dev

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        zip \
        exif \
        pcntl \
        gd \
        bcmath \
        opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
    libpng \
    libjpeg-turbo \
    freetype \
    libxml2 \
    libzip \
    oniguruma \
    nodejs \
    npm \
    shadow \
    bash \
    git

COPY --from=builder /usr/local/lib/php/extensions /usr/local/lib/php/extensions
COPY --from=builder /usr/local/etc/php/conf.d /usr/local/etc/php/conf.d
COPY --from=builder /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

ARG APP_UID=1000
ARG APP_GID=1000
RUN set -eux; \
    if ! getent group "${APP_GID}" >/dev/null 2>&1; then groupadd -g "${APP_GID}" app; fi; \
    if ! id -u app >/dev/null 2>&1; then useradd -u "${APP_UID}" -g "${APP_GID}" -m -s /bin/bash app; fi; \
    mkdir -p /home/app/.composer && chown -R app:app /home/app

COPY uploads.ini /usr/local/etc/php/conf.d/uploads.ini

USER app
