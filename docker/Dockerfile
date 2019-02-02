FROM php:7.2

ARG userid

RUN [ -n "${userid}" ]] && usermod -u ${userid} www-data || true

RUN mkdir -p /srv/app/var /srv/app/vendor; \
    chown -R www-data. /srv/app/var /srv/app/vendor

RUN set -xeu \
    && apt-get update && apt-get install -y --no-install-recommends \
        git \
        zlib1g-dev \
        libssl-dev \
        librabbitmq-dev \
        netcat \
        rabbitmq-server \
    && docker-php-ext-install \
        zip \
        pcntl

RUN touch /usr/local/etc/php/php.ini

RUN set -xe \
 && pecl install mongodb \
 && pecl install xdebug \
 && pecl install amqp

RUN set -xe \
 && echo "extension=mongodb.so" >> /usr/local/etc/php/php.ini \
 && echo "extension=amqp.so" >> /usr/local/etc/php/conf.d/amqp.ini

RUN curl -L https://cs.sensiolabs.org/download/php-cs-fixer-v2.phar -o php-cs-fixer \
    && chmod a+x php-cs-fixer \
    && mv php-cs-fixer /usr/local/bin/php-cs-fixer

COPY .php_cs.dist /opt/php_cs/.php_cs.dist

COPY lint_code.sh /usr/local/bin

RUN curl -s -f -L -o /tmp/installer.php https://raw.githubusercontent.com/composer/getcomposer.org/b107d959a5924af895807021fcef4ffec5a76aa9/web/installer \
 && php -r " \
    \$signature = '544e09ee996cdf60ece3804abc52599c22b1f40f4323403c44d44fdfdd586475ca9813a858088ffbc1f233e9b180f061'; \
    \$hash = hash('SHA384', file_get_contents('/tmp/installer.php')); \
    if (!hash_equals(\$signature, \$hash)) { \
        unlink('/tmp/installer.php'); \
        echo 'Integrity check failed, installer is either corrupt or worse.' . PHP_EOL; \
        exit(1); \
    }" \
 && php /tmp/installer.php --no-ansi --install-dir=/usr/bin --filename=composer --version=1.7.3 \
 && composer --ansi --version --no-interaction \
 && rm -rf /tmp/* /tmp/.htaccess

ENV COMPOSER_HOME=/tmp

RUN set -xe \
 && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
 && echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/xdebug.ini \
 && echo "xdebug.remote_host=192.168.99.1" >> /usr/local/etc/php/conf.d/xdebug.ini \
 && echo "xdebug.remote_port=5902" >> /usr/local/etc/php/conf.d/xdebug.ini \
 && echo "xdebug.remote_handler=dbgp" >> /usr/local/etc/php/conf.d/xdebug.ini \
 && echo "xdebug.idekey=XDEBUG" >> /usr/local/etc/php/conf.d/xdebug.ini
