#!/bin/sh

CWD="`pwd`"
INITSCRIPT="$CWD/aragmq"
CONFIG="$CWD/aragmq.cfg"
SPOOLDIR="$CWD/../queue/"

if [ -e "$CONFIG" ]
then
  . "$CONFIG"

  case "$1" in
    remove)
        # remove dropr
        cd ../vendor/dropr/client/bin/
        ./install.sh remove

        if [ -f /etc/init.d/aragmq ]
        then
            /etc/init.d/aragmq stop
        fi

        rm -f /etc/cron.hourly/aragmq
        rm -f /etc/cron.daily/aragmq
        rm -f /etc/cron.weekly/aragmq
        rm -f /etc/cron.monthly/aragmq

        rm -f /etc/init.d/aragmq
        rm -f /etc/default/aragmq.cfg

        echo
        update-rc.d aragmq remove
        echo
    ;;
    *)

        if [ ! -d $SPOOLDIR ]
        then
            mkdir -p $SPOOLDIR
        fi
        chmod -R ug+rwx $SPOOLDIR
        chown -R www-data:www-data $SPOOLDIR

        ln -sf "$CWD/aragmq-hourly" /etc/cron.hourly/aragmq
        ln -sf "$CWD/aragmq-daily" /etc/cron.daily/aragmq
        ln -sf "$CWD/aragmq-weekly" /etc/cron.weekly/aragmq
        ln -sf "$CWD/aragmq-monthly" /etc/cron.monthly/aragmq

        ln -sf $INITSCRIPT /etc/init.d/aragmq
        cp $CONFIG /etc/default/aragmq.cfg

        REGEX="s|%scripts_dir%|$CWD|"
        sed -i -e $REGEX /etc/default/aragmq.cfg

        DISPATCHERDIR=$(readlink -f "$CWD/../../../../public_html")
        REGEX="s|%dispatcher_dir%|$DISPATCHERDIR|"
        sed -i -e $REGEX /etc/default/aragmq.cfg

        /etc/init.d/aragmq start

        echo
        update-rc.d aragmq defaults 99 01
        echo

        # install dropr
        cd ../vendor/dropr/client/bin/
        ./install.sh

        REGEX="s|%dropr_dir%|$CWD/../vendor/dropr/client/bin/|"
        sed -i -e $REGEX /etc/default/dropr

        /etc/init.d/dropr start
   ;;
   esac

else
  echo "$CONFIG missing."
  echo "Could not install/remove."
fi
