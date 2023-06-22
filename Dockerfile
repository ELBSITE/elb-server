FROM php:8.1-apache-bullseye

# Install MariaDB client
RUN apt-get update && export DEBIAN_FRONTEND=noninteractive \
    && apt-get install -y cron zip mariadb-client libzip-dev libxml2-dev libjpeg62-turbo-dev libpng-dev libxslt-dev libfreetype6-dev git \ 
    && apt-get clean -y && rm -rf /var/lib/apt/lists/*

# Install required dependency
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install mysqli pdo pdo_mysql bcmath zip intl soap sockets gd xsl

# enable apache rewrite module
RUN a2enmod rewrite && service apache2 restart

# SETUP COMPOSER
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('sha384', 'composer-setup.php') === 'e21205b207c3ff031906575712edab6f13eb0b361f2085f1f1237b7126d785e826a450292b6cfd1d64d92e6563bbde02') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer

WORKDIR /var/www/html
COPY . .
RUN chown -R 1000:www-data ./pub
RUN chown -R 1000:www-data ./var
RUN chown -R 1000:www-data ./vendor
RUN usermod -u 1000 www-data
RUN composer install
RUN bin/magento setup:static-content:deploy
RUN bin/magento cron:install
RUN mv app/etc/env.example.php app/etc/env.php 