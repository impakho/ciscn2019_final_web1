FROM php:7.4-rc-apache

EXPOSE 80

RUN apt-get -y update

RUN apt-get -y install cmake vim nano netcat libffi-dev

RUN rm -rf /var/www/html/*

COPY source /var/www/html

RUN chown -R www-data:www-data /var/www/html

COPY flag.txt /flag

COPY start.sh /start.sh

RUN chmod +x /start.sh

WORKDIR /var/www/html

ENTRYPOINT ["/start.sh"]