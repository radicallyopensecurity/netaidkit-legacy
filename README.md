NetAidKit
====

Standalone VPN/Tor router for journalists and activists.

Note that this is version 0.1, and in order to meet certain time constraints, 
some shortcuts were made. Some code is temporary, and is annotated as such.


Building the image
====

Create a working directory somewhere and perform the following steps:

<ol>
    <li>git clone git://git.openwrt.org/openwrt.git && cd openwrt && make defconfig</li>
    <li>./scripts/feeds update && ./scripts/feeds install -a && cd ..</li>
    <li>git clone https://github.com/radicallyopensecurity/netaidkit && rm -rf netaidkit/.git</li>
    <li>cp -R netaidkit/. openwrt/ && cd openwrt && ./sbox-pkg/pkg.sh</li>
    <li>./scripts/feeds update && ./scripts/feeds install -a && make defconfig</li>
    <li>cat sbox.config >> .config && make oldconfig && make -j3 V=s # Use default answers</li>
</ol>
