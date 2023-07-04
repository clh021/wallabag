#!/bin/sh

envsubst < /opt/wallabag/config/wallabag-php.ini > /usr/local/etc/php/conf.d/wallabag-php.ini
envsubst < /opt/wallabag/config/parameters.yml > /var/www/html/app/config/parameters.yml

yarn build:prod

# composer install
# php -S 0.0.0.0:8000
exec "$@"

# CMD bash -c "composer install && php artisan serve --host 0.0.0.0 --port 5001"
# whoami
# echo '-----------------------'
# mkdir -p /var/www/.config/composer/ 
# chown -Rf www-data:www-data /var/www/.config
# echo '-----------------------'
# composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/
# composer install
# php artisan serve --host 0.0.0.0 --port 8000
# php -S 0.0.0.0:8000