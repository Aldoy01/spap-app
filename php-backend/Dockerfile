FROM php:7.4-apache

RUN apt-get update \
  && apt-get install -y --no-install-recommends libpq-dev libzip-dev unzip \
  && docker-php-ext-install pdo pdo_pgsql pgsql \
  && pecl install redis-5.3.7 \
  && docker-php-ext-enable redis \
  && a2dismod mpm_event mpm_worker || true \
  && a2enmod mpm_prefork \
  && a2enmod rewrite headers \
  && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY public /var/www/html

EXPOSE 80

CMD ["sh", "-c", "sed -i \"s/Listen 80/Listen ${PORT:-80}/\" /etc/apache2/ports.conf && sed -i \"s/:80>/:${PORT:-80}>/\" /etc/apache2/sites-available/000-default.conf && apache2-foreground"]
