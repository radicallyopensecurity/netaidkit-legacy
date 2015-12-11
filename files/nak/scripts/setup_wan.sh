#!/bin/sh

uci set wireless.@wifi-iface[0].ssid="$1";
uci set wireless.@wifi-iface[0].disabled=0

if [ ! -z "$2" ]
then

	# TEMP HACK - TODO: detect and pass enctype in the web interface
	iwinfo wlan0 scan | grep -m1 -B1 -A3 "$1" | grep "WPA2" >/dev/null
	if [ $? -eq 0 ]; then
		uci set wireless.@wifi-iface[0].encryption='psk2';
		uci set wireless.@wifi-iface[0].key="$2";
	else
		uci set wireless.@wifi-iface[0].encryption='';
		uci set wireless.@wifi-iface[0].key='';
	fi
else
	uci set wireless.@wifi-iface[0].encryption='';
	uci set wireless.@wifi-iface[0].key='';
fi

uci commit wireless;
/nak/scripts/restart_iface.sh wwan

/nak/scripts/go_online.sh;

(sleep 15; /nak/scripts/fix_wifi.sh) &
