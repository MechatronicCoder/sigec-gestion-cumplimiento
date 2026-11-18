FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
    bash git curl libpng-dev libxml2-dev libzip-dev oniguruma-dev freetype-dev \
    icu-dev zip unzip \
    && docker-php-ext-configure gd --with-freetype \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

EXPOSE 9000
CMD ["php-fpm"]
