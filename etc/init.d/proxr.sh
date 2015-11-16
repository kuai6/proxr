#!/bin/sh

uid="kuai6";
php=/usr/bin/php
installationName="proxr"

if [[ $USER != $uid ]]; then
    if [[ $EUID -ne 0 ]]; then
        echo "This script must be run as $uid"
    exit 1
    else
        sudo -u $uid -s "$0 $1 $2 $3";
        exit 0;
    fi;
fi;

. /etc/init.d/functions

prefix="/srv/www/$installationName"

case "$1" in
    start)
        $php $prefix/public/index.php system init
        echo -n "Starting Main Daemon"
        $php $prefix/public/index.php daemon main $1
        echo_success
        echo;
        echo -n "Starting Device Daemon"
        $php $prefix/public/index.php contactClosureDeviceDaemon
        echo_success
        echo;
    ;;
    stop)
        echo -n "Stopping Device Daemon"
        $php $prefix/public/index.php daemon contactClosureDevice $1
        echo_success
        echo;
        echo -n "Stopping Main Daemon"
        $php $prefix/public/index.php daemon main $1
        echo_success
        echo;
    ;;
    *)
        echo "Usage: /etc/init.d/proxr [start|stop|restart]"
        echo "Run or Stop all daemons"
    ;;
esac

exit 0;
