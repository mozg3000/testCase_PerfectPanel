FROM php:8.1-fpm-alpine

#ENV XDEBUG_VERSION 3.2
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN apk update \
    && apk upgrade \
  && apk add --no-cache autoconf g++ make fcgi

RUN apk add --no-cache pcre-dev

RUN apk add --update linux-headers
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug


RUN mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

COPY ./php-fpm/conf.d /usr/local/etc/php/conf.d


WORKDIR /app
