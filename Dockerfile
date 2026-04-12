FROM php:8.2-cli

WORKDIR /app

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libsqlite3-dev \
    libzip-dev \
    nodejs \
    npm \
    && docker-php-ext-install pdo pdo_sqlite zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction
RUN npm ci && npm run build

RUN mkdir -p storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 8080

CMD sh -c "
touch database/database.sqlite &&
php artisan migrate --force &&
php artisan serve --host=0.0.0.0 --port=$PORT
"