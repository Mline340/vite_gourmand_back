FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libicu-dev \
    libpq-dev \
    libzip-dev \
    libssl-dev \
    libpng-dev \
    libonig-dev \
    libjpeg-dev \
    unzip \
    git \
    default-mysql-client \
    && docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-install intl pdo pdo_mysql zip opcache gd mbstring \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite

WORKDIR /var/www/html/

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./
COPY . .

COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

RUN COMPOSER_MEMORY_LIMIT=-1 composer install \
    --no-dev \
    --optimize-autoloader \
    --no-scripts \
    --ignore-platform-reqs \
    --no-interaction

ENV APP_ENV=prod

RUN php bin/console importmap:install \
    && php bin/console asset-map:compile \
    && php bin/console cache:clear --env=prod

RUN chown -R www-data:www-data /var/www/html

CMD ["apache2-foreground"]