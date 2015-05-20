#!/bin/sh
# TODO: check signature again to prevent local privilege escalation

killall sshd lighttpd; sleep 1;
/sbin/sysupgrade -n "$1"
