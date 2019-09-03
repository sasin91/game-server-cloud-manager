FROM ubuntu:19.04

LABEL maintainer="Jonas Kervin Hansen"

RUN useradd -ms /bin/bash -u 1337 vessel
WORKDIR /var/www/html

ENV TZ=UTC
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN set -x \
    && apt-get update && apt-get install -y gnupg gosu \
    && gosu nobody true

RUN apt-get update \
    && apt-get install -y curl zip unzip git supervisor sqlite3 \
    && apt-get install -y nginx php-fpm php-cli \
    php-pgsql php-sqlite3 php-gd \
    php-curl php-memcached \
    php-imap php-mysql php-mbstring \
    php-xml php-zip php-bcmath php-soap \
    php-intl php-readline php-xdebug \
    php-msgpack php-igbinary php-ldap \
    composer \
    && mkdir /run/php \
    && apt-get -y autoremove \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* \
    && echo "daemon off;" >> /etc/nginx/nginx.conf

RUN ln -sf /dev/stdout /var/log/nginx/access.log \
    && ln -sf /dev/stderr /var/log/nginx/error.log

COPY h5bp /etc/nginx/h5bp
COPY default /etc/nginx/sites-available/default
COPY php-fpm.conf /etc/php//fpm/php-fpm.conf
COPY xdebug.ini /etc/php//mods-available/xdebug.ini
COPY vessel.ini /etc/php//fpm/conf.d/99-vessel.ini

EXPOSE 80

COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY start-container /usr/local/bin/start-container
RUN chmod +x /usr/local/bin/start-container

ENTRYPOINT ["start-container"]
