#!/bin/sh

uci set wireless.@wifi-iface[1].ssid=$1;
uci set wireless.@wifi-iface[1].key=$2;
uci commit wireless;

(env -i /bin/ubus call network reload) >/dev/null 2>/dev/null
