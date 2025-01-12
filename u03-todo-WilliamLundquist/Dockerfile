FROM php:8.2-apache

RUN apt-get update \
    && apt-get install -y \
    zip libzip-dev

RUN docker-php-ext-install pdo pdo_mysql zip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ENV PATH=/root/.composer/vendor/bin:$PATH

COPY ./src /var/www/html/src