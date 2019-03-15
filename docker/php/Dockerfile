FROM php:7.1.8-fpm

RUN apt-get update && apt-get install -y \
        libmcrypt-dev \
        curl nano zip unzip php-pclzip

RUN docker-php-ext-install \
    # DATABASE
	mcrypt pdo pdo_mysql

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer