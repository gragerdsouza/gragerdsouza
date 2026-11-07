# syntax=docker/dockerfile:1.7
#
# Multi-stage image for the portfolio.
#   base  -> PHP 8.5 + FrankenPHP + extensions shared by every stage
#   dev   -> base + Composer, Node, PCOV (used by compose.yaml and CI)
#   vendor/assets -> build stages
#   prod  -> slim runtime image (deploy to any container host)
#
# Bump PHP_VERSION to 8.6 once it is GA (scheduled 2026-11-19).
ARG PHP_VERSION=8.5
ARG NODE_VERSION=22

############################
FROM dunglas/frankenphp:1-php${PHP_VERSION}-bookworm AS base

RUN install-php-extensions \
        bcmath \
        intl \
        opcache \
        pcntl \
        pdo_pgsql \
        pdo_sqlite \
        redis \
        zip \
    && apt-get update \
    && apt-get install -y --no-install-recommends git unzip \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app
ENV SERVER_NAME=":8000"
EXPOSE 8000

############################
FROM base AS dev
ARG NODE_VERSION
ARG UID=1000
ARG GID=1000

RUN install-php-extensions pcov \
    && curl -fsSL https://deb.nodesource.com/setup_${NODE_VERSION}.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    && rm -rf /var/lib/apt/lists/* \
    && cp "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN groupadd -g ${GID} app 2>/dev/null || true \
    && useradd -m -u ${UID} -g ${GID} -s /bin/bash app 2>/dev/null || true \
    && chown -R ${UID}:${GID} /app /data/caddy /config/caddy

USER ${UID}:${GID}
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]

############################
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --ignore-platform-reqs
COPY . .
RUN composer dump-autoload --no-dev --optimize --classmap-authoritative

############################
FROM node:${NODE_VERSION}-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
COPY --from=vendor /app/vendor ./vendor
RUN npm run build

############################
FROM base AS prod

RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY docker/php/prod.ini $PHP_INI_DIR/conf.d/zz-app.ini

COPY --chown=www-data:www-data . .
COPY --chown=www-data:www-data --from=vendor /app/vendor ./vendor
COPY --chown=www-data:www-data --from=assets /app/public/build ./public/build

RUN chown -R www-data:www-data storage bootstrap/cache /data/caddy /config/caddy \
    && chmod +x docker/entrypoint.sh

USER www-data
HEALTHCHECK --interval=30s --timeout=3s --start-period=20s \
    CMD curl -fsS http://127.0.0.1:8000/up || exit 1

ENTRYPOINT ["docker/entrypoint.sh"]
CMD ["php", "artisan", "octane:frankenphp", "--host=0.0.0.0", "--port=8000"]


