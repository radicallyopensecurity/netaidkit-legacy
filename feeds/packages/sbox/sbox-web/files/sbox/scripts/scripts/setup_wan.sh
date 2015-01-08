#!/bin/sh

uci set wireless.@wifi-iface[0].ssid=$1;
uci set wireless.@wifi-iface[0].encryption='psk2';
uci set wireless.@wifi-iface[0].key=$2;
uci commit wireless;

/sbox/scripts/go_online.sh;
