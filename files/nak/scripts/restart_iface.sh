#!/bin/sh

# Changes to any of wireless interfaces (wlan, wwan) cause hostapd to restart,
# because both interfaces use the same wireless phy.

# Apparently this can't be patched easily. The user will experience a brief
# loss of connectivity while changing AP or client configuration until a
# better support for that is developed in OpenWRT.

# nbd | wifi configuration is brought up per card, not per individual   
#     | virtual interface                                               
# nbd | so whenever you make the chanes to the sta, it has to bring down
#     | wifi and then bring it back up again                            

# /bin/ubus call uci reload_config
wifi
