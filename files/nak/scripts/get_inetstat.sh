#!/bin/sh

ping -c1 8.8.8.8 &> /dev/null

if [ $? -ne 0 ]; then
    echo "1"
else
    echo "0"
fi
