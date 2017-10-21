FROM phpdockerio/php7-fpm:latest

RUN apt-get update && apt-get install -y --no-install-recommends \
        php7.0-xdebug php7.0-mbstring \
        && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# PHP Settings
COPY php-fpm/php.ini /usr/local/etc/php/

RUN mkdir -p /app

COPY app /app

WORKDIR /app