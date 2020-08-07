FROM php:7.4-cli
RUN docker-php-ext-install json

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
COPY . /usr/src/ex1
WORKDIR /usr/src/ex1

CMD [ "php", "./index.php" ]