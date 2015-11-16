#!/bin/sh

uid="kuai6";
php=/usr/bin/php
installationName="proxr"
CONSOLETYPE="normal"

if [[ $USER != $uid ]]; then
    if [[ $EUID -ne 0 ]]; then
        echo "This script must be run as $uid"
    exit 1
    else
        sudo -u $uid -s "$0 $1 $2 $3";
        exit 0;
    fi;
fi;

# This all seem confusing? Look in /etc/sysconfig/init,
# or in /usr/share/doc/initscripts-*/sysconfig.txt
RES_COL=60
MOVE_TO_COL="echo -en \\033[${RES_COL}G"
SETCOLOR_SUCCESS="echo -en \\033[1;32m"
SETCOLOR_FAILURE="echo -en \\033[1;31m"
SETCOLOR_WARNING="echo -en \\033[1;33m"
SETCOLOR_NORMAL="echo -en \\033[0;39m"

if [ "$CONSOLETYPE" = "serial" ]; then
  MOVE_TO_COL=
  SETCOLOR_SUCCESS=
  SETCOLOR_FAILURE=
  SETCOLOR_WARNING=
  SETCOLOR_NORMAL=
fi


echo_success() {
  [ "$BOOTUP" = "color" ] && $MOVE_TO_COL
  echo -n "["
  [ "$BOOTUP" = "color" ] && $SETCOLOR_SUCCESS
  echo -n $"  OK  "
  [ "$BOOTUP" = "color" ] && $SETCOLOR_NORMAL
  echo -n "]"
  echo -ne "\r"
  return 0
}

echo_failure() {
  [ "$BOOTUP" = "color" ] && $MOVE_TO_COL
  echo -n "["
  [ "$BOOTUP" = "color" ] && $SETCOLOR_FAILURE
  echo -n $"FAILED"
  [ "$BOOTUP" = "color" ] && $SETCOLOR_NORMAL
  echo -n "]"
  echo -ne "\r"
  return 1
}

echo_passed() {
  [ "$BOOTUP" = "color" ] && $MOVE_TO_COL
  echo -n "["
  [ "$BOOTUP" = "color" ] && $SETCOLOR_WARNING
  echo -n $"PASSED"
  [ "$BOOTUP" = "color" ] && $SETCOLOR_NORMAL
  echo -n "]"
  echo -ne "\r"
  return 1
}

echo_warning() {
  [ "$BOOTUP" = "color" ] && $MOVE_TO_COL
  echo -n "["
  [ "$BOOTUP" = "color" ] && $SETCOLOR_WARNING
  echo -n $"WARNING"
  [ "$BOOTUP" = "color" ] && $SETCOLOR_NORMAL
  echo -n "]"
  echo -ne "\r"
  return 1
}

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
