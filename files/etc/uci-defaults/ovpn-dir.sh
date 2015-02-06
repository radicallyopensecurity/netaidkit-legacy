#!/bin/sh

# Create folder for VPN files
mkdir -p /nak/ovpn/upload
chgrp -R www-data /nak/ovpn
chmod -R g+w /nak/ovpn
