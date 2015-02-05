#!/bin/sh

# Create folder for VPN files
mkdir -p /netaidkit/ovpn/upload
chgrp -R www-data /netaidkit/ovpn
chmod -R g+w /netaidkit/ovpn
