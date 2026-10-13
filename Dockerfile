# --- Tahap 1: build aset frontend ---
FROM node:20-alpine AS assets
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# --- Tahap 2: dependency PHP ---
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist
COPY . .
RUN composer dump-autoload --optimize --no-dev

# --- Tahap 3: runtime ---
FROM php:8.3-fpm-alpine
RUN docker-php-ext-install pdo_mysql opcache
WORKDIR /var/www/html
COPY --from=vendor /app .
COPY --from=assets /app/public/build public/build
RUN chown -R www-data:www-data storage bootstrap/cache
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]
CMD ["php-fpm"]
