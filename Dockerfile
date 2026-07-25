FROM richarvey/nginx-php-fpm:latest

COPY . .

RUN chmod +x scripts/00-laravel-deploy.sh

# Install dependencies at BUILD time, not every container start
RUN composer install --no-dev --optimize-autoloader --working-dir=/var/www/html

ENV SKIP_COMPOSER=1
ENV WEBROOT=/var/www/html/public
ENV PHP_ERRORS_STDERR=1
ENV RUN_SCRIPTS=1
ENV REAL_IP_HEADER=1
ENV COMPOSER_ALLOW_SUPERUSER=1

ENV APP_ENV=production
ENV APP_DEBUG=false
ENV LOG_CHANNEL=stderr

CMD ["/start.sh"]