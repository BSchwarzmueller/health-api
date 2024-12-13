FROM php:8.3-apache

# Kopiere den public Ordner in das Apache Document Root
COPY public/ /var/www/html/

# Kopiere den vendor Ordner
COPY vendor/ /var/www/vendor/

# Kopiere den src Ordner
COPY src/ /var/www/src/

# Installiere benötigte PHP-Erweiterungen
RUN docker-php-ext-install pdo pdo_mysql

RUN a2enmod rewrite

# Expose port (optional für Dokumentation)
EXPOSE 80