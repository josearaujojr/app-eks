FROM ubuntu:22.04

RUN apt-get update && apt-get install -y \
    apache2 \
    mysql-client \
    php \
    libapache2-mod-php \
    php-mysql \
    php-xml \
    php-curl \
    php-zip \
    curl \
    unzip \
    git && \
    rm -rf /var/lib/apt/lists/*

# Instala Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Configura o diretório de trabalho e instala o AWS SDK
WORKDIR /var/www/html
RUN composer require aws/aws-sdk-php:^3.258

# Configuração do Apache
RUN a2enmod rewrite && \
    rm /var/www/html/index.html

# Copia os arquivos da aplicação
COPY html /var/www/html/
COPY custom-apache.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 80
CMD ["apache2ctl", "-D", "FOREGROUND"]