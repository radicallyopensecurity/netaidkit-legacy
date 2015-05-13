#!/bin/sh

# If this is the first build, pull the OpenWRT sources.
if [ ! -d "openwrt" ]; then
    git clone git://git.openwrt.org/openwrt.git && cd openwrt
else
    # On subsequent builds, update the OpenWRT sources.
    cd openwrt && git pull
fi

# Create a default configuration.
make defconfig

# Update the package feed and install packages.
./scripts/feeds update && ./scripts/feeds install -a

# Copy the netaidkit sources to the OpenWRT directory.
tar cf - --exclude=openwrt --exclude=.git ./../ | tar xvf -

# Prompt for root password and update shadow file.
./scripts/change_rootpwd.py

# Build the netaidkit package archive.
./nak-pkg/pkg.sh

# Update the package feed and install additional packages.
./scripts/feeds update && ./scripts/feeds install -a

# Copy configuration overwrites and start the build process.
cat netaidkit.config >> .config && make oldconfig && make V=s

# Copy images over to netaidkit bin folder.
cp bin/ar71xx/openwrt-ar71xx-generic-gl-inet-6416A-v1-squashfs-* ../bin/ar71xx
