FROM php:7.2-fpm

ENV WORKDIR=/srv/www
WORKDIR $WORKDIR

RUN mkdir -p data/cache


# Install PHP extensions
# Install Postgre PDO
RUN apt-get update -y \
    && apt-get install --no-install-recommends -y \
    libpq-dev librabbitmq-dev libmemcached-dev zlib1g-dev git \
    libjpeg-dev libpng-dev libfreetype6-dev supervisor \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql bcmath zip sockets \
    && rm -r /var/lib/apt/lists/*

RUN pecl install amqp \
    && echo "extension=$(find /usr/local/lib/php/extensions/ -name amqp.so)" > /usr/local/etc/php/conf.d/amqp.ini
RUN pecl install memcached \
    && echo "extension=$(find /usr/local/lib/php/extensions/ -name memcached.so)" > /usr/local/etc/php/conf.d/memcached.ini

# Install the PHP gd library
RUN docker-php-ext-configure gd \
        --enable-gd-native-ttf \
        --with-jpeg-dir=/usr/lib \
        --with-freetype-dir=/usr/include/freetype2  \
    &&  docker-php-ext-install gd


RUN echo "php_admin_flag[log_errors] = on" >>  /usr/local/etc/php-fpm.d/www.conf
RUN echo "php_admin_value[error_log] = /proc/self/fd/2" >>  /usr/local/etc/php-fpm.d/www.conf
RUN echo "catch_workers_output = yes" >>  /usr/local/etc/php-fpm.d/www.conf
RUN echo "php_admin_value[error_reporting] = E_ALL & ~E_NOTICE" >> /usr/local/etc/php-fpm.d/www.conf



COPY bin                bin
COPY config             config
COPY module             module
COPY etc/supervisor     /etc/supervisor
COPY public             public
COPY vendor             vendor

COPY composer.json  composer.json
COPY entrypoint.sh  /entrypoint.sh
RUN chmod +x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]
EXPOSE 9000 9999
CMD ["php-fpm"]