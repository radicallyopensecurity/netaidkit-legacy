#!/bin/sh

if [ $1 = "on" ];then

    uci set firewall.@forwarding[0].enabled=0;
    uci set firewall.@forwarding[1].enabled=1;    
    uci commit firewall
    /etc/init.d/firewall restart;

    openvpn --daemon --config /netaidkit/ovpn/current.ovpn
    
    /netaidkit/scripts/set_stage.sh 4
elif [ $1 = "off" ]
then

    uci set firewall.@forwarding[0].enabled=1;
    uci set firewall.@forwarding[1].enabled=0;    
    uci commit firewall
    /etc/init.d/firewall restart;

    killall -9 openvpn
    
    /netaidkit/scripts/set_stage.sh 2
fi

/etc/init.d/firewall restart
