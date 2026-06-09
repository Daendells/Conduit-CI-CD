# =========================
# Stage 1: Build Vite Assets
# =========================
FROM node:20-alpine AS node_builder

WORKDIR /app

COPY package*.json ./

RUN npm install

COPY . .

RUN npm run build

# =========================
# Stage 2: Laravel + PHP
# =========================
FROM php:8.2-apache

# Install dependencies + MongoDB PHP extension
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
    libzip-dev \
    libssl-dev \
    pkg-config \
    autoconf \
    gcc \
    make \
    && docker-php-ext-install zip pdo pdo_mysql \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable Apache rewrite
RUN a2enmod rewrite

WORKDIR /var/www/html

# Copy project
COPY . .

# Ensure static CSS and JS assets are present in the final image
COPY public/css ./public/css
COPY public/js ./public/js

# Copy built frontend assets
COPY --from=node_builder /app/public/build ./public/build

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# Storage & cache permissions (explicit mkdir — brace expansion not supported in /bin/sh)
RUN mkdir -p storage/framework/sessions \
    && mkdir -p storage/framework/views \
    && mkdir -p storage/framework/cache \
    && mkdir -p storage/logs \
    && mkdir -p bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Apache config
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf

RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/apache2.conf \
    /etc/apache2/conf-available/*.conf

# Cloud Run uses port 8080
RUN sed -i 's/^Listen 80$/Listen 8080/' /etc/apache2/ports.conf
RUN sed -i 's/<VirtualHost \*:80>/<VirtualHost *:8080>/' /etc/apache2/sites-available/000-default.conf

# Copy and set entrypoint
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 8080

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
