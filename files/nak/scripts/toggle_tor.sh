#!/bin/sh

/nak/scripts/reset_iptables.sh # iptables flushing

if [ $1 = "on" ];then
    rm /var/log/tor/notices.log
    /etc/init.d/tor start
    uci set firewall.@redirect[1].enabled=1;
    uci set firewall.@redirect[2].enabled=1;
    uci set firewall.@forwarding[0].enabled=0;
    /nak/scripts/set_stage.sh 3
    echo "1" > /sys/class/leds/gl-connect\:green\:lan/brightness
    echo "0" > /sys/class/leds/gl-connect\:red\:wlan/brightness
elif [ $1 = "off" ]
then
    /etc/init.d/tor stop
    uci set firewall.@redirect[1].enabled=0;
    uci set firewall.@redirect[2].enabled=0;
    /nak/scripts/set_stage.sh 2
    echo "0" > /sys/class/leds/gl-connect\:green\:lan/brightness
    echo "1" > /sys/class/leds/gl-connect\:red\:wlan/brightness
fi

uci commit;
/etc/init.d/firewall restart
