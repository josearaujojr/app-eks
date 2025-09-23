FROM php:8.2-apache

RUN docker-php-ext-install mysqli pdo pdo_mysql xml curl zip

# Instala utilitários extras
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl && \
    rm -rf /var/lib/apt/lists/*

# Instala Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html
RUN composer require aws/aws-sdk-php:^3.258

COPY html /var/www/html/
COPY custom-apache.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 80
CMD ["apache2-foreground"]
