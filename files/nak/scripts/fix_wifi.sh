#!/bin/sh

uplink_conf=$(uci show wireless.@wifi-iface[0].disabled | grep -o "'.*'")
uplink=${uplink_conf:1:$((${#uplink_conf} - 2))}

if [ $uplink = "0" ]; then
    ssid_line=$(iwinfo wlan0 info | head -1)
    case "$ssid_line" in
        *unknown*)
        uci set wireless.@wifi-iface[0].disabled=1
        uci commit
        wifi
    esac

fi

