FROM php:8.2-fpm

WORKDIR /app

# Configurar fontes do apt para ignorar assinaturas temporariamente
RUN echo "Acquire::AllowInsecureRepositories \"true\";" > /etc/apt/apt.conf.d/99allow-insecure \
    && apt-get update && apt-get install -y \
        zip \
        unzip \
        git \
    && docker-php-ext-install pdo pdo_mysql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer install --optimize-autoloader

RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

CMD php-fpm