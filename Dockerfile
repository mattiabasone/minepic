FROM php:8.1.14-fpm-alpine

ARG DEFAULT_USER_UID=1000
ARG APP_USER=app
ARG APP_PATH=/app

RUN apk update \
    && apk add --no-cache supervisor nginx mysql-client curl icu libpng freetype git libjpeg-turbo oniguruma-dev postgresql-dev libffi-dev libsodium libzip-dev \
    && apk add --no-cache --virtual build-dependencies freetds-dev icu-dev libxml2-dev freetype-dev libpng-dev libjpeg-turbo-dev libzip-dev g++ make autoconf libsodium-dev

RUN docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
    && pecl install redis xdebug \
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

COPY docker/entrypoint /entrypoint
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/php/app-fpm.conf /usr/local/etc/php-fpm.d/app.conf
COPY docker/supervisor/app.ini /etc/supervisor.d/app.ini

RUN chmod +x /entrypoint && \
    adduser -D ${APP_USER} -u ${DEFAULT_USER_UID} -s /bin/bash && \
    mkdir ${APP_PATH} && \
    chown -R ${APP_USER}:${APP_USER} ${APP_PATH} && \
    rm /usr/local/etc/php-fpm.d/www.conf /usr/local/etc/php-fpm.d/www.conf.default /usr/local/etc/php-fpm.d/docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf && \
    mkdir /var/run/php/ && \
    chown ${APP_USER}:${APP_USER} /var/run/php/

#RUN cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini \
#    && echo "[XDEBUG]" >> /usr/local/etc/php/php.ini \
#    && echo "xdebug.mode=coverage" >> /usr/local/etc/php/php.ini

CMD ["/entrypoint"]

EXPOSE 80