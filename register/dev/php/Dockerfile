FROM php:7.1-apache

RUN { \
    echo '[PHP]'; \
    echo 'post_max_size = 12M'; \
    echo 'upload_max_filesize = 12M'; \
} > /usr/local/etc/php/php.ini

RUN apt-get update \
  && apt-get install -y git zlib1g-dev unzip libpng-dev libfreetype6-dev libjpeg62-turbo-dev libicu-dev libxml2-dev cifs-utils\
  && docker-php-ext-configure gd --with-freetype-dir=/usr/lib/x86_64-linux-gnu/ --with-jpeg-dir=/usr/lib/x86_64-linux-gnu/ \
  && docker-php-ext-install pdo_mysql mbstring zip gd intl xml

RUN a2enmod rewrite

WORKDIR /var/www
RUN rm -rf html && ln -s /vagrant/public html
