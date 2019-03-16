#!/usr/bin/env bash
set -e

PHP=$(which php)
SUPERVISORD=$(which supervisord)
cd $WORKDIR

mkdir -p data/cache
chmod 777 data/cache

supervisord -c /etc/supervisor/supervisord.conf&

exec "$@"
