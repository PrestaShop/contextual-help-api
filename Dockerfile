FROM composer:2 as builder

WORKDIR /app
COPY . .
RUN rm vhost.conf
RUN composer update
RUN composer install --no-dev
#RUN composer install

# Dockerfile.development
FROM php:8.0-apache

# Setup Apache2 config
WORKDIR /var/www
COPY vhost.conf /etc/apache2/sites-available/000-default.conf
COPY --from=builder /app /var/www/
RUN a2enmod rewrite
RUN apt-get update && \
    apt-get install --force-yes -y vim && \
    rm -rf /var/lib/apt/lists/*

# use your users $UID and $GID below
RUN groupadd apache-www-volume -g 1000 && \
    useradd apache-www-volume -u 1000 -g 1000 && \
    chown -R apache-www-volume:apache-www-volume /var/www/
