#!/bin/sh

# TODO: check signature again to prevent local privilege escalation
sysupgrade -n $1
