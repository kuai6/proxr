#!/usr/bin/env bash
set -ex

# Process template file and replace default.conf of nginx
envsubst \
    '$NGINX_PORT $NGINX_HOST $NGINX_FPM_URL' \
    < /etc/nginx/conf.d/www.tpl.nginx \
    > /etc/nginx/conf.d/default.conf

exec "$@"