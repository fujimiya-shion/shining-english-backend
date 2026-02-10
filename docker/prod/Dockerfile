FROM php:8.3-fpm-alpine AS base

RUN apk add --no-cache \
        icu-dev \
        libzip-dev \
        oniguruma-dev \
        freetype-dev \
        libjpeg-turbo-dev \
        libpng-dev \
        bash \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo_mysql mbstring bcmath intl zip gd

WORKDIR /var/www/html

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

COPY . .

RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

RUN chown -R www-data:www-data /var/www/html

USER www-data

EXPOSE 9000

CMD ["php-fpm", "-F"]
