#!/bin/sh

if [ $1 = "on" ];then
    rm /var/log/tor/notices.log
    /etc/init.d/tor start
    uci set firewall.@redirect[1].enabled=1;
    uci set firewall.@redirect[2].enabled=1;
    uci set firewall.@forwarding[0].enabled=0;
    uci set firewall.@forwarding[1].enabled=0;
    /netaidkit/scripts/set_stage.sh 3
elif [ $1 = "off" ]
then
    rm /var/log/tor/notices.log
    /etc/init.d/tor stop
    uci set firewall.@redirect[1].enabled=0;
    uci set firewall.@redirect[2].enabled=0;
    uci set firewall.@forwarding[0].enabled=1;
    uci set firewall.@forwarding[1].enabled=1;
    /netaidkit/scripts/set_stage.sh 2
fi

uci commit;
/etc/init.d/firewall restart
