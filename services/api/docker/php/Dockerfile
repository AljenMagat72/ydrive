# ==========================================
# 1. BASE STAGE: Shared dependencies and setup
# ==========================================
FROM dunglas/frankenphp:php8.4-bookworm AS base

ENV SERVER_NAME="http://"
ENV COMPOSER_ALLOW_SUPERUSER=1

ARG APP_ENV
ARG APP_URL
ENV APP_ENV=${APP_ENV}
ENV APP_URL=${APP_URL}

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl unzip zip vim \
    libpng-dev libjpeg62-turbo-dev libpq-dev \
    libonig-dev libfreetype6-dev libicu-dev libzip-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN install-php-extensions \
    intl zip ctype curl dom fileinfo filter \
    hash mbstring openssl pcre pdo session \
    tokenizer xml pdo_pgsql opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
<<<<<<< HEAD
WORKDIR /var/www
=======

RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs \
    && npm i -g corepack@latest && corepack enable

WORKDIR /app

# Copy codebase and install core dependencies
COPY . .

# ==========================================
# 2. DEVELOPMENT STAGE: Runs without the production entrypoint
# ==========================================
FROM base AS development
# In development, FrankenPHP's default entrypoint/cmd will run,
# or you can override CMD here if you use a dev-specific script.
EXPOSE 80

# ==========================================
# 3. PRODUCTION STAGE: Only runs your production entrypoint
# ==========================================
FROM base AS production

RUN composer install --optimize-autoloader --no-scripts --no-interaction
RUN npm ci

# Setup production-specific permissions
RUN mkdir -p storage/framework/{sessions,views,cache,testing} storage/logs bootstrap/cache \
    && chmod -R a+rw storage

COPY docker/php/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80

CMD ["/entrypoint.sh"]
>>>>>>> dev
