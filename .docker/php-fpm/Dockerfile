FROM php:7.2.28-fpm

RUN apt-get update \
    && apt-get install -y --no-install-recommends vim curl debconf subversion git apt-transport-https apt-utils \
    build-essential locales acl mailutils wget zip unzip \
    gnupg gnupg1 gnupg2

RUN docker-php-ext-install pdo pdo_mysql
COPY php7.2.28.ini /etc/php/7.2.28/php.ini
COPY php-fpm-pool.conf /etc/php/7.2.28/pool.d/www.conf

RUN curl -sSk https://getcomposer.org/installer | php -- --disable-tls && \
   mv composer.phar /usr/local/bin/composer

USER root

***REMOVED*** Install Opcache
RUN docker-php-ext-install opcache
***REMOVED*** Copy opcache configration
COPY ./opcache.ini /usr/local/etc/php/conf.d/opcache.ini

***REMOVED*** Install APCu
RUN pecl install apcu;
RUN docker-php-ext-enable apcu

***REMOVED*** Install igbinary
RUN pecl install -o igbinary \
&&  docker-php-ext-enable igbinary

***REMOVED*** Install Php Redis Extension
RUN printf "\n" | pecl install -o -f redis \
&&  rm -rf /tmp/pear \
&&  docker-php-ext-enable redis

COPY ./php.custom.ini /usr/local/etc/php/conf.d

***REMOVED*** Clean up
RUN apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* && \
    rm /var/log/lastlog /var/log/faillog

RUN usermod -u 1000 www-data

WORKDIR /var/www

EXPOSE 9000
CMD ["php-fpm"]
