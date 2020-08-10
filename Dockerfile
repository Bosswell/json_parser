FROM php:7.4-cli

RUN apt-get update && \
    apt-get install -y \
        zlib1g-dev \
        libzip-dev \ 
    && docker-php-ext-install zip \
    && docker-php-ext-install json

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
COPY . /usr/src/json_parser
WORKDIR /usr/src/json_parser

RUN composer install

CMD [ "php", "./index.php" ]