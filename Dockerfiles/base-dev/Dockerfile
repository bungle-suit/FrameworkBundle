FROM php:7.4.5-fpm-buster

COPY sources.list /etc/apt/sources.list
RUN apt-get update && apt-get install -y libicu-dev

RUN docker-php-ext-install intl && docker-php-ext-enable intl
RUN docker-php-ext-enable opcache
COPY my-php-tweak.ini $PHP_INI_DIR/conf.d/

RUN groupadd -g 1000 work && useradd --uid 1000 --gid 1000 --create-home work

COPY ./symfony-installer /tmp 
COPY ./symfony_linux_amd64.gz /tmp
RUN /tmp/symfony-installer

USER work
COPY config.json /home/work/.config/composer

ENV PATH=/home/work/.symfony/bin/:/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin
EXPOSE 8000
VOLUME /home/work/app
VOLUME /home/work/.config
WORKDIR /home/work/app
ENTRYPOINT ["/home/work/app/bin/console"]
CMD ["list"]
