#!/bin/sh

nbytes=$(wget -qO- --no-check-certificate --no-dns-cache http://clients3.google.com/generate_204 | wc -c)

if [ $nbytes -ne 0 ]; then
    echo "yes"
else
    echo "no"
fi
