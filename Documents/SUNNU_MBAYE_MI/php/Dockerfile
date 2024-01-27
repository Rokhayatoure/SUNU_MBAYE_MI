# Utilise une image PHP avec Apache
FROM php:8.2-apache

# Installe les dépendances nécessaires
RUN apt-get update && \
    apt-get install -y \
    libicu-dev \
    zlib1g-dev \
    libzip-dev \
    unzip \
    && docker-php-ext-install -j$(nproc) intl pdo_mysql zip

# Configure Apache
RUN a2enmod rewrite && \
    service apache2 restart

RUN echo "DocumentRoot /var/www/html/public" >> /etc/apache2/apache2.conf
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Définit le répertoire de travail
WORKDIR /var/www/html

# Copie les fichiers de l'application dans le conteneur
COPY . /var/www/html/

# Installe les dépendances Symfony
RUN curl -sS https://getcomposer.org/installer | php -- --disable-tls
RUN mv composer.phar /usr/local/bin/composer

# Expose le port 80
EXPOSE 80

# Commande pour lancer Apache
CMD ["apache2-foreground"]
