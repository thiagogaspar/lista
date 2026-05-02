FROM dunglas/frankenphp:1-php8.4-bookworm

ARG APP_KEY
ARG APP_ENV=production
ENV APP_DEBUG=true

RUN install-php-extensions pdo_mysql mysqli mbstring intl zip gd opcache @composer

RUN apt-get update && apt-get install -y --no-install-recommends curl ca-certificates unzip \
    && curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY . .
RUN npm run build && npm prune --omit=dev

RUN composer install --no-dev --optimize-autoloader

RUN php artisan route:cache && php artisan view:cache

ENV SERVER_NAME=:8080
EXPOSE 8080

CMD mkdir -p /app/database && touch /app/database/database.sqlite && php artisan config:cache && php artisan cache:clear --no-interaction && php artisan migrate --force --no-interaction && php artisan serve --host=0.0.0.0 --port=8080
