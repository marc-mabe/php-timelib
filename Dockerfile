ARG PHP_VERSION=latest
ARG CODE_COVERAGE=false
FROM php:${PHP_VERSION}-cli-alpine
ARG CODE_COVERAGE

WORKDIR /workdir

# install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_HTACCESS_PROTECT=0
ENV COMPOSER_CACHE_DIR=/.composer

# install PHP extensions
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS linux-headers \
    && docker-php-ext-install calendar && docker-php-ext-enable calendar
