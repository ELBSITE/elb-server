FROM php:8.1-apache-bullseye

# Install MariaDB client
RUN apt-get update && export DEBIAN_FRONTEND=noninteractive \
    && apt-get install -y cron zip mariadb-client libzip-dev libxml2-dev libjpeg62-turbo-dev libpng-dev libxslt-dev libfreetype6-dev git \ 
    && apt-get clean -y && rm -rf /var/lib/apt/lists/*

# Install required dependency
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install mysqli pdo pdo_mysql bcmath zip intl soap sockets gd xsl

# memory limit fix
COPY ./.devcontainer/config/php.ini /usr/local/etc/php/conf.d/custom.ini

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

# set env
# RUN echo -n $env_data | base64 -d > ./app/etc/env.php
# RUN mv app/etc/config.example.php app/etc/config.php

# file permissions
RUN chown -R www-data:www-data ./pub/static
RUN chown -R www-data:www-data ./var
RUN chown -R www-data:www-data ./vendor
RUN chown -R www-data:www-data ./generated
RUN chown -R www-data:www-data ./app/etc
RUN chown -R www-data:www-data ./dev/tests/static

# switch user
USER www-data:www-data

# start application process
RUN composer install
CMD ["/var/www/html/bin/startup.sh"] 