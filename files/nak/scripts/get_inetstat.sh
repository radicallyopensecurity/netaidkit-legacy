#!/bin/sh

# check outbound internet connectivity
# see: https://github.com/radicallyopensecurity/netaidkit/issues/9
# returns:
# 	0 if outbound works
#	1 if outbound fails

ping -c1 8.8.8.8
