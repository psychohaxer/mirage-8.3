FROM alpine:latest AS downloader

RUN apk add --no-cache curl tar

ENV PHPMYADMIN_VERSION=5.2.3
ENV PHPMYADMIN_SHA256=03de2640bb25c9a6f96bc94eae316080b5fd5bd58d769e1318ba9dd94c83364c

WORKDIR /tmp

RUN curl -fsSL "https://files.phpmyadmin.net/phpMyAdmin/${PHPMYADMIN_VERSION}/phpMyAdmin-${PHPMYADMIN_VERSION}-english.tar.gz" -o phpmyadmin.tar.gz \
    && echo "${PHPMYADMIN_SHA256}  phpmyadmin.tar.gz" | sha256sum -c - \
    && tar -xzf phpmyadmin.tar.gz \
    && mv phpMyAdmin-${PHPMYADMIN_VERSION}-english phpmyadmin \
    && rm phpmyadmin.tar.gz

FROM nginx:alpine

RUN apk add --no-cache \
    php83 \
    php83-fpm \
    php83-mysqli \
    php83-pdo_mysql \
    php83-mbstring \
    php83-xml \
    php83-zip \
    php83-gd \
    php83-bz2 \
    php83-opcache \
    php83-session \
    php83-ctype \
    php83-json \
    fcgi

COPY --from=downloader /tmp/phpmyadmin /var/www/html

RUN mkdir -p /var/www/html/tmp \
    && chmod 777 /var/www/html/tmp

COPY config.inc.php /var/www/html/config.inc.php

COPY nginx-php.conf /etc/nginx/conf.d/default.conf

COPY php-fpm.conf /etc/php83/php-fpm.d/www.conf

COPY uploads.ini /etc/php83/conf.d/uploads.ini

RUN mkdir -p /run/nginx

EXPOSE 80

# Start script
COPY start.sh /start.sh
RUN chmod +x /start.sh

CMD ["/start.sh"]
