FROM php:8.2-apache

# Installation des dépendances système robustes
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
    && docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-install intl pdo pdo_mysql zip opcache gd mbstring \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb

# Activation du module Apache Rewrite pour Symfony
RUN a2enmod rewrite

# Configuration du dossier public pour Symfony
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/conf-available/*.conf

COPY . /var/www/html/
WORKDIR /var/www/html/

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ON MODIFIE CETTE LIGNE (Ajout de --ignore-platform-reqs)
RUN composer install --no-dev --optimize-autoloader --no-scripts --ignore-platform-reqs

RUN chown -R www-data:www-data /var/www/html