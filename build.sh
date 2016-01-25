#!/bin/bash

# Update release info
tag=$(./scripts/make_release.sh)

# Build the netaidkit package archive.
./nak-pkg/pkg.sh

# If this is the first build, pull the OpenWRT sources.
if [ ! -d "openwrt" ]; then
    git clone git://git.openwrt.org/15.05/openwrt.git && cd openwrt
else
    # On subsequent builds, update the OpenWRT sources.
    cd openwrt && git pull
fi

# Create a default configuration.
#rm -f .config; make defconfig
make defconfig

# Update the package feed and install packages.
./scripts/feeds update && ./scripts/feeds install -a

# Update timestamp of the build
if (git branch | grep "\*" | grep "dev")&>/dev/null; then
    buildtype="dev"
else
    buildtype="prod"
fi

rm -rf files/*

# Copy the netaidkit sources to the OpenWRT directory.
tar cf - --exclude=openwrt --exclude=.git ./../ | tar xvf -

if [ "$buildtype" == "dev" ]; then
    # Change make config and passwd file to enable root ssh login.
    ./scripts/enable_root_ssh.py
    # Prompt for root password and update shadow file.
    ./scripts/change_rootpwd.py "\`K@qt1)pLMto"
fi

# Update the package feed and install additional packages.
./scripts/feeds update && ./scripts/feeds install -a

# Copy configuration overwrites and start the build process.
cat netaidkit.config >> .config && make oldconfig && make V=s

# Delete cached nak-web package
rm -rf build_dir/target*/nak-web-*

# Copy images over to netaidkit bin folder.
cp bin/ar71xx/openwrt-ar71xx-generic-gl-inet-6416A-v1-squashfs-factory.bin ../bin/netaidkit_firmware_${tag}.bin
cp bin/ar71xx/openwrt-ar71xx-generic-gl-inet-6416A-v1-squashfs-sysupgrade.bin ../bin/netaidkit_firmware_${tag}_sysupgrade.bin
