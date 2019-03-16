#!/usr/bin/env bash
set -e

PHP=$(which php)
SUPERVISORD=$(which supervisord)
cd $WORKDIR

mkdir -p data/cache
chmod 777 data/cache

if [[ $XDEBUG_ENABLE == 1 && -f /usr/local/etc/php/conf.d/xdebug.ini.disable ]] ; then
    echo "Enabling Xdebug"
    mv /usr/local/etc/php/conf.d/xdebug.ini.disable /usr/local/etc/php/conf.d/xdebug.ini
fi

if [[ $XDEBUG_ENABLE == 0  && -f /usr/local/etc/php/conf.d/xdebug.ini ]] ; then
    echo "Disabling Xdebug"
    mv /usr/local/etc/php/conf.d/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini.disable
fi

$PHP bin/shelled migrations:migrate  --no-interaction

supervisord -c /etc/supervisor/supervisord.conf&

exec "$@"
