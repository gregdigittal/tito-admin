# Build stage: create Laravel app and install deps
FROM composer:2 AS builder
WORKDIR /app

RUN composer create-project laravel/laravel . --no-interaction \
    && composer require livewire/livewire guzzlehttp/guzzle --no-interaction

# Overlay Tito Admin code (merge into Laravel skeleton)
COPY config/ ./config/
COPY app/Services ./app/Services
COPY app/Http/Middleware ./app/Http/Middleware
COPY app/Livewire ./app/Livewire
COPY app/Providers/AppServiceProvider.php ./app/Providers/AppServiceProvider.php
COPY bootstrap/app.php ./bootstrap/app.php
COPY routes/ ./routes/
COPY resources/ ./resources/
COPY .env.example .env

# Runtime stage (PHP only; use artisan serve so Render PORT works)
FROM php:8.2-cli
RUN apt-get update && apt-get install -y --no-install-recommends zip unzip libzip-dev libonig-dev libxml2-dev \
    && docker-php-ext-install zip pcntl mbstring xml ctype json tokenizer \
    && rm -rf /var/lib/apt/lists/*

COPY --from=builder /app /var/www/html
WORKDIR /var/www/html

# Render sets PORT; default 8080 for local
ENV PORT=8080
EXPOSE 8080
CMD php artisan serve --host=0.0.0.0 --port=${PORT}
