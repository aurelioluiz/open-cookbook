FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

RUN pecl install pcov && docker-php-ext-enable pcov

RUN echo "pcov.enabled=1" > /usr/local/etc/php/conf.d/pcov.ini \
    && echo "pcov.directory=/app" >> /usr/local/etc/php/conf.d/pcov.ini

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN composer install

EXPOSE 9000

CMD ["php-fpm"]