#!/bin/sh

# Create folder for VPN files
mkdir -p /nak/ovpn/upload
chgrp -R www-data /nak/ovpn
chmod -R g+w /nak/ovpn
touch /var/log/openvpn.log

chgrp -R www-data /nak/webapp/data
chmod -R g+w /nak/webapp/data
