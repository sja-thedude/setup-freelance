FROM php:7.1-fpm-buster

RUN docker-php-ext-install pdo_mysql

RUN apt-get update -y && apt-get install -y libwebp-dev libjpeg62-turbo-dev libpng-dev libxpm-dev \
    libfreetype6-dev
RUN apt-get update && \
    apt-get install -y \
        zlib1g-dev

RUN docker-php-ext-install mbstring

RUN apt-get install -y libzip-dev
RUN docker-php-ext-install zip

RUN docker-php-ext-configure gd --with-gd --with-webp-dir --with-jpeg-dir \
    --with-png-dir --with-zlib-dir --with-xpm-dir --with-freetype-dir \
    --enable-gd-native-ttf

RUN docker-php-ext-install gd

# Install wkhtmltopdf
RUN apt-get install -y wkhtmltopdf xvfb

# Install imagick
RUN apt-get update && apt-get install -y \
    libmagickwand-dev --no-install-recommends \
    && pecl install imagick \
	&& docker-php-ext-enable imagick

# Install Supervisor
RUN apt install -y supervisor
COPY ./supervisord.conf /etc/supervisor/conf.d/supervisord.conf
RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer
ENTRYPOINT ["/usr/bin/supervisord"]

#CMD ["php-fpm"]

EXPOSE 9000
