#!/bin/sh

# check outbound internet connectivity
# see: https://github.com/radicallyopensecurity/netaidkit/issues/9
# returns:
# 	0 if outbound works
# 	4 if network fails ( http://www.gnu.org/software/wget/manual/html_node/Exit-Status.html )
wget -q --timeout=120 -O- http://google.com/generate_204