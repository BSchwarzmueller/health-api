FROM php:8.0-apache

# Kopiere den Frontend-Code in den HTML-Ordner von Apache
COPY frontend/ /var/www/html/

# Setze das Backend in ein separates Verzeichnis
COPY backend/ /var/www/backend/

# Installiere benötigte PHP-Erweiterungen
RUN docker-php-ext-install pdo pdo_mysql

# Expose port (optional für Dokumentation)
EXPOSE 80
