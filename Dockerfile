FROM dunglas/frankenphp:1-php8.4-bookworm

RUN install-php-extensions \
    pdo_mysql \
    mysqli \
    mbstring \
    intl \
    zip \
    gd \
    opcache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN apt-get update && apt-get install -y --no-install-recommends nodejs npm \
    && npm ci \
    && npm run build \
    && npm prune --omit=dev \
    && composer install --no-dev --optimize-autoloader \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && apt-get remove -y nodejs npm \
    && apt-get autoremove -y \
    && rm -rf /var/lib/apt/lists/* /root/.npm /tmp/*

ENV SERVER_NAME=:8080
EXPOSE 8080

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
