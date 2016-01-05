#!/bin/sh

/nak/scripts/reset_iptables.sh # iptables flushing

if [ $1 = "on" ];then
    uci set firewall.@forwarding[0].enabled=1;
elif [ $1 = "off" ]
then
    uci set firewall.@forwarding[0].enabled=0;
fi

uci commit firewall
/etc/init.d/firewall restart
