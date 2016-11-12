FROM php:5.6-apache

RUN docker-php-ext-install mysqli
RUN docker-php-ext-install mysql
RUN a2enmod rewrite

COPY docker/www/php.ini /usr/local/etc/php/
