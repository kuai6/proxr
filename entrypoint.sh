#!/usr/bin/env bash
set -e

PHP=$(which php)
SUPERVISORD=$(which supervisord)
cd $WORKDIR

mkdir -p data/cache
chmod 777 data/cache

$PHP bin/shelled migrations:migrate  --no-interaction

supervisord -c /etc/supervisor/supervisord.conf&

exec "$@"
