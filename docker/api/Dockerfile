FROM php:8.2-apache

RUN cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini

RUN apt update \
 && apt install -y libicu-dev git libzip-dev unzip libpng-dev libjpeg-dev libfreetype6-dev \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install exif intl zip pdo_mysql gd \
 && a2enmod rewrite

RUN echo "xdebug.mode=debug" >> $PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.client_host=host.docker.internal" >> $PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.idekey=PHPSTORM" >> $PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini

COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
