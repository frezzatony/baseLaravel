FROM php:8.1-apache

RUN apt-get update && apt-get install -f -y \
    curl \
    g++ \
    git \
    libbz2-dev \
    libfreetype6-dev \
    libicu-dev \
    libjpeg-dev \
    libmcrypt-dev \
    libpng-dev \
    libreadline-dev \
    libpq-dev \
    sudo \
    unzip \
    zip \
    zlib1g-dev \
    libzip-dev \
    libreoffice \
    supervisor 

RUN apt-get install -f -y libpng-dev libwebp-dev unzip git && \
    apt-get clean && \
    docker-php-ext-install zip exif pcntl sockets && \
    docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg --with-webp && \
    docker-php-ext-install gd 

RUN a2enmod rewrite headers

RUN docker-php-ext-install \
    bcmath \
    bz2 \
    calendar \
    iconv \
    intl \
    opcache \
    pdo_mysql \
    zip \
    pdo_pgsql \
    pgsql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/

ENV APACHE_RUN_USER=www-data \
    APACHE_RUN_GROUP=www-data \
    APACHE_DOCUMENT_ROOT=/var/www/ \
    ABSOLUTE_APACHE_DOCUMENT_ROOT=/var/www

RUN chown -R www-data:www-data /var/www

ENTRYPOINT ["/bin/bash","-c","service supervisor start && apache2-foreground"]

