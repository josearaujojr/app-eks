FROM ubuntu:24.10

# Instala dependências básicas
RUN apt-get update && apt-get upgrade -y && \
    apt-get install -y apache2 mysql-client php libapache2-mod-php php-mysql curl unzip git

# Instala Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Instala AWS SDK via Composer
RUN mkdir -p /var/www/sdk
WORKDIR /var/www/sdk
RUN composer require aws/aws-sdk-php
RUN cp -r /var/www/sdk/vendor /var/www/html/

# Configuração do Apache
RUN a2enmod rewrite
RUN mkdir -p /var/www/html
RUN rm /var/www/html/index.html

# Copia os arquivos da aplicação
COPY html /var/www/html/
COPY custom-apache.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 80
CMD ["apache2ctl", "-D", "FOREGROUND"]