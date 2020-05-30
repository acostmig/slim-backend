FROM trafex/alpine-nginx-php7:latest
COPY --from=composer /usr/bin/composer /usr/bin/composer
WORKDIR /var/www
COPY --chown=nobody ./app          /var/www
COPY --chown=nobody ./app/public   /var/www/html
# Install composer from the official image

USER root
# Run composer install to install the dependencies
RUN composer install --optimize-autoloader --no-interaction --no-progress

RUN apk add php7-pdo php7-pdo_mysql php7-mysqli php7-mysqlnd 

USER nobody


EXPOSE 8080