FROM php:7.4-cli-alpine

LABEL maintainer="Mattia Basone mattia.basone@gmail.com"

ARG DEFAULT_USER_UID=1000
ARG APP_USER=app
ARG APP_PATH=/app

RUN apk update \
    && apk add --no-cache mysql-client curl icu imagemagick-dev libpng freetype libjpeg-turbo oniguruma-dev postgresql-dev libffi-dev libsodium libzip-dev \
    && apk add --no-cache --virtual build-dependencies freetds-dev icu-dev libxml2-dev freetype-dev libpng-dev libjpeg-turbo-dev libzip-dev g++ make autoconf libsodium-dev

RUN docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
    && docker-php-ext-configure pdo_dblib --with-libdir=lib \
    && pecl install imagick redis swoole \
    && docker-php-ext-install \
        bcmath \
        dom \
        iconv \
        mbstring \
        intl \
        gd \
        pgsql \
        mysqli \
        pdo_pgsql \
        pdo_mysql \
        pdo_dblib \
        sockets \
        zip \
        pcntl \
        tokenizer \
        xml \
    && docker-php-ext-enable \
        imagick \
        redis \
        opcache \
        swoole

COPY ./entrypoint /entrypoint
RUN chmod +x /entrypoint
RUN adduser -D ${APP_USER} -u ${DEFAULT_USER_UID} -s /bin/bash
RUN mkdir ${APP_PATH} && \
    chown -R ${APP_USER}:${APP_USER} ${APP_PATH}

WORKDIR ${APP_PATH}

CMD ["/entrypoint"]

EXPOSE 1215