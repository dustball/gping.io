FROM php:5.6-apache

RUN docker-php-ext-install mysqli
RUN docker-php-ext-install mysql

COPY www/ /var/www/html/
RUN mv /var/www/html/config-dist.php /var/www/html/config.php
RUN a2enmod rewrite

