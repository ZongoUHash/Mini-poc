FROM composer:2 AS dependencies

WORKDIR /app

COPY . ./

RUN composer install --no-dev --no-interaction --no-progress --prefer-dist --optimize-autoloader \
    && composer dump-autoload --no-dev --classmap-authoritative

FROM node:22-alpine AS assets

WORKDIR /app

COPY package.json package-lock.json ./

RUN npm ci

COPY resources ./resources
COPY public ./public
COPY vite.config.js ./

RUN npm run build

FROM php:8.4-cli-alpine

WORKDIR /var/www/html

RUN apk add --no-cache libpq-dev \
    && docker-php-ext-install pdo_pgsql

COPY --from=dependencies /app ./
COPY --from=assets /app/public/build ./public/build

RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 10000

CMD ["sh", "-c", "php artisan migrate --force && php artisan db:seed --force && php -S 0.0.0.0:${PORT:-10000} -t public public/index.php"]
