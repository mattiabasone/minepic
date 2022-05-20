FROM php:8.1.6-cli-alpine

ARG DEFAULT_USER_UID=1000
ARG APP_USER=app
ARG APP_PATH=/app

RUN apk update \
    && apk add --no-cache mysql-client curl icu libpng freetype git libjpeg-turbo oniguruma-dev postgresql-dev libffi-dev libsodium libzip-dev \
    && apk add --no-cache --virtual build-dependencies freetds-dev icu-dev libxml2-dev freetype-dev libpng-dev libjpeg-turbo-dev libzip-dev g++ make autoconf libsodium-dev

RUN docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
    && pecl install redis swoole xdebug \
    && docker-php-ext-install \
        bcmath \
        dom \
        mbstring \
        intl \
        gd \
        pgsql \
        mysqli \
        pdo_pgsql \
        pdo_mysql \
        sockets \
        zip \
        pcntl \
        xml \
    && docker-php-ext-enable \
        redis \
        opcache \
        swoole \
        # xdebug \
    && curl -s -o /usr/bin/composer https://getcomposer.org/download/2.3.2/composer.phar \
    && chmod +x /usr/bin/composer

# Imagick Setup
RUN set -ex \
    && apk add --no-cache --virtual .phpize-deps $PHPIZE_DEPS imagemagick-dev libtool \
    && export CFLAGS="$PHP_CFLAGS" CPPFLAGS="$PHP_CPPFLAGS" LDFLAGS="$PHP_LDFLAGS" \
    && pecl install imagick \
    && docker-php-ext-enable imagick \
    && apk add --no-cache --virtual .imagick-runtime-deps imagemagick \
    && apk del .phpize-deps

COPY ./entrypoint /entrypoint
RUN chmod +x /entrypoint
RUN adduser -D ${APP_USER} -u ${DEFAULT_USER_UID} -s /bin/bash
RUN mkdir ${APP_PATH} && \
    chown -R ${APP_USER}:${APP_USER} ${APP_PATH}

#RUN cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini \
#    && echo "[XDEBUG]" >> /usr/local/etc/php/php.ini \
#    && echo "xdebug.mode=coverage" >> /usr/local/etc/php/php.ini

WORKDIR ${APP_PATH}

USER ${APP_USER}

CMD ["/entrypoint"]

EXPOSE 1215