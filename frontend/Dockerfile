FROM php:7.4-apache

RUN a2dismod mpm_event mpm_worker || true \
  && a2enmod mpm_prefork

COPY index.php /var/www/html/index.php
COPY assets /var/www/html/assets

EXPOSE 80

CMD ["sh", "-c", "sed -i \"s/Listen 80/Listen ${PORT:-80}/\" /etc/apache2/ports.conf && sed -i \"s/:80>/:${PORT:-80}>/\" /etc/apache2/sites-available/000-default.conf && apache2-foreground"]
