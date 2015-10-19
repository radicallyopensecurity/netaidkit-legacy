#!/bin/bash

# Build the netaidkit package archive.
./nak-pkg/pkg.sh

cd openwrt && rm -rf files/*

# Copy the netaidkit sources to the OpenWRT directory.
tar cf - --exclude=openwrt --exclude=.git ./../ | tar xvf -

# Delete cached nak-web package
rm -rf build_dir/target*/nak-web-*

make package/nak-web/compile -j1 V=s TARGET_OPTIMIZATION="-ggdb3 -O0" STRIP="/bin/true"

