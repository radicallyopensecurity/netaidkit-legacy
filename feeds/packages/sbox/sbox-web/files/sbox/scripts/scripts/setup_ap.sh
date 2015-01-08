#!/bin/sh

uci set wireless.@wifi-iface[1].ssid=$1;
uci set wireless.@wifi-iface[1].key=$2;
uci commit wireless;
