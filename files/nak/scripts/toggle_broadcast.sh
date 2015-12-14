#!/bin/sh

if [ $1 = "on" ];then
    uci set wireless.@wifi-iface[1].hidden=0;
elif [ $1 = "off" ]
then
    uci set wireless.@wifi-iface[1].hidden=1;
fi

uci commit wireless
/nak/scripts/restart_iface.sh wlan
