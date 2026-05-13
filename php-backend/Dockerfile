FROM php:7.4-apache

RUN apt-get update \
  && apt-get install -y --no-install-recommends libpq-dev libzip-dev unzip \
  && docker-php-ext-install pdo pdo_pgsql pgsql \
  && pecl install redis-5.3.7 \
  && docker-php-ext-enable redis \
  && rm -f /etc/apache2/mods-enabled/mpm_event.* /etc/apache2/mods-enabled/mpm_worker.* \
  && a2dismod mpm_event mpm_worker || true \
  && a2enmod mpm_prefork \
  && a2enmod rewrite headers \
  && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY public /var/www/html

EXPOSE 80

CMD ["sh", "-c", "rm -f /etc/apache2/mods-enabled/mpm_event.* /etc/apache2/mods-enabled/mpm_worker.* && a2enmod mpm_prefork >/dev/null 2>&1 || true; sed -i \"s/Listen 80/Listen ${PORT:-80}/\" /etc/apache2/ports.conf && sed -i \"s/:80>/:${PORT:-80}>/\" /etc/apache2/sites-available/000-default.conf && apache2-foreground"]
