#!/bin/sh

uci set firewall.@redirect[0].enabled=0;
uci set firewall.@forwarding[0].enabled=1;
uci set firewall.@forwarding[1].enabled=1;
uci commit firewall;
/etc/init.d/firewall restart;

echo > /etc/dnsmasq.conf
/etc/init.d/dnsmasq restart

wifi
