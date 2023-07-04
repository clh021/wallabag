FROM php:7.4-fpm AS rootless

ARG DEBIAN_FRONTEND=noninteractive
ARG NODE_VERSION=16

RUN curl -sSL https://gitee.com/clh21/sh/raw/master/mirror.sh | sh
RUN apt-get update \
    && apt-get install -y \
      ca-certificates \
      curl \
      gnupg \
      lsb-release \
      openssl \
      software-properties-common

RUN curl 'https://deb.nodesource.com/gpgkey/nodesource.gpg.key' | apt-key add - \
    && echo "deb https://deb.nodesource.com/node_${NODE_VERSION}.x $(lsb_release -cs) main" > /etc/apt/sources.list.d/nodesource.list

RUN apt-get update && apt-get install -y \
        libmcrypt-dev \
        libicu-dev \
        libpq-dev \
        libxml2-dev \
        libpng-dev \
        libjpeg-dev \
        libwebp-dev \
        libsqlite3-dev \
        imagemagick \
        libmagickwand-dev \
        libtidy-dev \
        libonig-dev \
        libzip-dev \
        libfreetype6-dev \
        zlib1g-dev \
        git \
        build-essential \
        nodejs
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp
RUN docker-php-ext-install -j "$(nproc)" \
        bcmath \
        gd \
        gettext \
        iconv \
        intl \
        mbstring \
        opcache \
        pdo \
        pdo_mysql \
        pdo_pgsql \
        pdo_sqlite \
        sockets \
        tidy \
        zip

RUN pecl install redis; \
    pecl install imagick; \
    pecl install xdebug-3.1.6; \
    docker-php-ext-enable \
        redis \
        imagick \
        xdebug \
    ;

RUN npm config set registry https://registry.npmmirror.com
RUN npm install -g yarn

# RUN curl -L -o /usr/local/bin/envsubst https://github.com/a8m/envsubst/releases/download/v1.1.0/envsubst-`uname -s`-`uname -m`; \
#     chmod +x /usr/local/bin/envsubst
COPY envsubst-Linux-x86_64 /usr/local/bin/envsubst
RUN chmod +x /usr/local/bin/envsubst

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

COPY entrypoint.sh /entrypoint.sh
COPY config/ /opt/wallabag/config/

RUN mkdir -p \
        /var/www/html/app/config/ \
        /var/www/html/var/cache \
        /var/www/html/web/assets \
        /var/www/html/data \
        /var/www/html/data/db \
        /var/www/.cache

RUN apt-get update && \
    apt-get install -y unzip p7zip-full && \
    rm -rf /var/lib/apt/lists/*

ENTRYPOINT ["/entrypoint.sh"]
CMD ["php", "bin/console", "--env=dev", "server:run", "0.0.0.0:8000"]

FROM rootless AS default

ARG USER_UID=1000
ARG USER_GID=1000

RUN groupmod -g 1000 www-data ; \
    usermod -u ${USER_UID} -g www-data www-data ; \
    touch /usr/local/etc/php/conf.d/wallabag-php.ini \
        /var/www/.yarnrc ; \
    chown -R www-data: /var/www/html \
        /usr/local/etc/php/conf.d/wallabag-php.ini \
        /var/www/.cache \
        /var/www/.yarnrc

RUN mkdir -p /var/www/.config/composer/ \
    && composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/ \
    && chown -Rf 1000:1000 /var/www/.config

USER www-data
