FROM php:7.2.28-fpm

# Replace shell with bash so we can source files
RUN rm /bin/sh && ln -s /bin/bash /bin/sh

RUN apt-get update \
    && apt-get install -y --no-install-recommends vim curl debconf subversion git apt-transport-https apt-utils \
    build-essential locales acl mailutils wget zip unzip \
    gnupg gnupg1 gnupg2

RUN docker-php-ext-install pdo pdo_mysql
COPY php7.2.28.ini /etc/php/7.2.28/php.ini
COPY php-fpm-pool.conf /etc/php/7.2.28/pool.d/www.conf

RUN curl -sSk https://getcomposer.org/installer | php -- --disable-tls && \
   mv composer.phar /usr/local/bin/composer

# Instal NODE
ENV NVM_DIR /usr/local/nvm
ENV NODE_VERSION 13.9.0

# Install nvm with node and npm
RUN curl https://raw.githubusercontent.com/creationix/nvm/v0.30.1/install.sh | bash \
    && source $NVM_DIR/nvm.sh \
    && nvm install $NODE_VERSION \
    && nvm alias default $NODE_VERSION \
    && nvm use default

ENV NODE_PATH $NVM_DIR/v$NODE_VERSION/lib/node_modules
ENV PATH      $NVM_DIR/versions/node/v$NODE_VERSION/bin:$PATH

# Install Yarn
RUN curl -o- -L https://yarnpkg.com/install.sh | bash -s -- --version 1.12.3 && \
  ln -sf $HOME/.yarn/bin/yarn /usr/local/bin/yarn && \
  ln -sf $HOME/.yarn/bin/yarnpkg /usr/local/bin/yarnpkg

# Install Opcache
RUN docker-php-ext-install opcache
# Copy opcache configration
COPY ./opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Install APCu
RUN pecl install apcu;
RUN docker-php-ext-enable apcu

# Install igbinary
RUN pecl install -o igbinary \
&&  docker-php-ext-enable igbinary

# Install Php Redis Extension
RUN printf "\n" | pecl install -o -f redis \
&&  rm -rf /tmp/pear \
&&  docker-php-ext-enable redis

COPY ./php.custom.ini /usr/local/etc/php/conf.d

# Clean up
RUN apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* && \
    rm /var/log/lastlog /var/log/faillog

RUN usermod -u 1000 www-data

WORKDIR /var/www/smite-stats

EXPOSE 9000
CMD ["php-fpm"]
