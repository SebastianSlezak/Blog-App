FROM php:7.4.33-fpm-alpine

RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

COPY . /app

VOLUME ["/app"]