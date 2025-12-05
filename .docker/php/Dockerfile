FROM phalconphp/cphalcon:v5.9.2-php8.4

USER root
RUN apt-get update && apt-get install -y \
libmemcached-dev zlib1g-dev libzip-dev libicu-dev libonig-dev zip

RUN set -eux \
  && install-php-extensions memcached mysqli pdo pdo_mysql zip bcmath intl opcache xdebug

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('sha384', 'composer-setup.php') === 'c8b085408188070d5f52bcfe4ecfbee5f727afa458b2573b8eaaf77b3419b0bf2768dc67c86944da1544f06fa544fd47') { echo 'Installer verified'.PHP_EOL; } else { echo 'Installer corrupt'.PHP_EOL; unlink('composer-setup.php'); exit(1); }" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');" \
    && mv composer.phar /usr/local/bin/composer


RUN usermod -s /bin/bash phalcon

ADD ./.docker/php/php.ini /usr/local/etc/php/conf.d/xxx-custom.ini

USER phalcon

RUN composer global require phalcon/devtools:"^5.0@dev" --dev  \
    && composer clear-cache \
    && echo 'export PATH="$HOME/.composer/vendor/bin:$PATH"' >> ~/.bashrc

EXPOSE 9000
CMD ["php-fpm"]