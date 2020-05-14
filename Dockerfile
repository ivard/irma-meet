FROM php:7
RUN apt-get update -y && apt-get install -y openssl zip unzip git
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN docker-php-ext-install pdo #mbstring
RUN mkdir /app
WORKDIR /app

CMD composer install && php artisan serve -vv --host=0.0.0.0 --port=8181
EXPOSE 8181
