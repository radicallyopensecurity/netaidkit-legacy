#!/bin/sh

if [ $1 = "on" ];then
    openvpn --config /etc/openvpn/AirVPN_United-Kingdom_UDP-443.ovpn 
    /sbox/scripts/set_stage.sh 4
elif [ $1 = "off" ]
then
    killall -9 openvpn
    /sbox/scripts/set_stage.sh 2
fi

/etc/init.d/firewall restart
