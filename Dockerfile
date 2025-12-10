# Utilise PHP 8.2 + Apache
FROM php:8.2-apache

# Active mod_rewrite (souvent nécessaire pour les routes custom)
RUN a2enmod rewrite

# Installe mbstring (et au passage PDO MySQL)
RUN docker-php-ext-install mbstring pdo pdo_mysql

# Définit le dossier de travail
WORKDIR /var/www/html

# Copie tout le projet dans le conteneur
COPY . /var/www/html

# Donne les bons droits nécessaires pour Apache
RUN chown -R www-data:www-data /var/www/html
