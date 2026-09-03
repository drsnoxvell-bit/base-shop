ARG PHP_VERSION=8.3
ARG NODE_VERSION=22
ARG COMPOSER_VERSION=2

FROM node:${NODE_VERSION}-bookworm AS node
FROM composer:${COMPOSER_VERSION} AS composer

FROM php:${PHP_VERSION}-fpm-bookworm

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        curl \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libzip-dev \
        libicu-dev \
        libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" gd pdo_mysql zip bcmath intl opcache pcntl exif \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY --from=node /usr/local/bin/node /usr/local/bin/node
COPY --from=node /usr/local/lib/node_modules /usr/local/lib/node_modules

RUN ln -sf /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && ln -sf /usr/local/lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx

WORKDIR /var/www/html

COPY docker/entrypoint.sh /usr/local/bin/shop-entrypoint
RUN sed -i 's/\r$//' /usr/local/bin/shop-entrypoint \
    && chmod +x /usr/local/bin/shop-entrypoint

ENTRYPOINT ["shop-entrypoint"]
CMD ["php-fpm"]
