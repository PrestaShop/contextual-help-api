# Dockerfile.development
FROM php:8.0-apache

# Setup Apache2 config
COPY vhost.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite
RUN apt-get update && apt-get install --force-yes -y vim

# use your users $UID and $GID below
RUN groupadd apache-www-volume -g 1000
RUN useradd apache-www-volume -u 1000 -g 1000

WORKDIR /var/www
