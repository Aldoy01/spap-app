FROM php:8.2-apache

RUN apt-get -o Acquire::Retries=5 update \
  && apt-get -o Acquire::Retries=5 install -y --fix-missing --no-install-recommends libcurl4-openssl-dev libpq-dev libzip-dev unzip \
  && docker-php-ext-install curl pdo pdo_pgsql pgsql \
  && pecl install redis \
  && docker-php-ext-enable redis \
  && echo 'ServerName localhost' > /etc/apache2/conf-available/servername.conf \
  && a2enconf servername \
  && rm -rf /var/lib/apt/lists/*

RUN php -m | grep -x curl \
  && php -m | grep -x pdo_pgsql \
  && php -m | grep -x pgsql

RUN rm -f /etc/apache2/mods-enabled/mpm_event.* /etc/apache2/mods-enabled/mpm_worker.* \
  && a2dismod mpm_event mpm_worker || true

RUN a2enmod mpm_prefork rewrite headers

WORKDIR /var/www/html

COPY public /var/www/html

EXPOSE 80

CMD ["sh", "-c", "rm -f /etc/apache2/mods-enabled/mpm_event.* /etc/apache2/mods-enabled/mpm_worker.* && a2enmod mpm_prefork >/dev/null 2>&1 || true; sed -i \"s/Listen 80/Listen ${PORT:-80}/\" /etc/apache2/ports.conf && sed -i \"s/:80>/:${PORT:-80}>/\" /etc/apache2/sites-available/000-default.conf && apache2-foreground"]
