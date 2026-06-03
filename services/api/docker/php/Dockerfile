FROM php:8.4-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libpq-dev \
    libonig-dev \
    libfreetype6-dev \
    libicu-dev \
    libzip-dev \
    locales \
    zip \
    vim \
    unzip \
    git \
    curl

# Configure extensions that need it
RUN docker-php-ext-configure gd --with-freetype --with-jpeg

# PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd zip intl

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
