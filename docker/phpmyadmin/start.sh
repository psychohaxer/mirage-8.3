#!/bin/sh

# Start PHP-FPM
php-fpm83 --nodaemonize &

# Start Nginx (foreground)
nginx -g 'daemon off;'
