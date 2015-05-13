NetAidKit
====

Standalone VPN/Tor router for journalists and activists.

Note that this is version 0.1, and in order to meet certain time constraints, 
some shortcuts were made. Some code is temporary, and is annotated as such.

Building the image
----

Create a working directory somewhere and execute the following commands:

```bash
git clone https://github.com/radicallyopensecurity/netaidkit
cd netaidkit && ./build.sh
```

You will be prompted to create a root password.
The compiled images will be in the netaidkit/bin folder.

Flashing the device for the first time:
----

<ol>
    <li>Plug the GL-Inet into an USB port.</li>
    <li>Wait for the default wireless network (SSID and key can be found 
        on the side of the box) to come up.</li>
    <li>Connect with default credentials, and browse to 192.168.8.1.</li>
    <li>Choose your language.</li>
    <li>Choose your timezone.</li>
    <li>Choose a temporary password (this one will be reset).</li>
    <li>Reconnect to the wireless network with your new password.</li>
    <li>In the menu on the left, go to 'Firmware'
        and then 'Upload Firmware'.</li>
    <li>Click on the file box and select the 
        'openwrt-ar71xx-generic-gl-inet-6416A-v1-squashfs-factory.bin'
        file.</li>
    <li>Wait for it to process the file, <b>uncheck 'Keep settings'</b>
         and click 'Upgrade'.</li>
    <li>Wait for the flashing process to complete, do not turn off the 
        GL-Inet or close the window.</li>
    <li>When you get disconnected, reconnect to the network NETAIDKIT 
        (default pass s3cr3tp4ss).</li>
    <li>Visit any web page to be redirected to the set-up page of 
        your newly flashed NetAidKit.</li>
</ol>

Reflashing an already flashed device:
----

Execute the following commands while in your working build directory:

<ol>
    <li>scp
    bin/ar71xx/openwrt-ar71xx-generic-gl-inet-6416A-v1-squashfs-sysupgrade.bin
        root@192.168.101.1:/tmp</li>
    <li>ssh root@192.168.101.1 "sysupgrade -n 
    /tmp/openwrt-ar71xx-generic-gl-inet-6416A-v1-squashfs-sysupgrade.bin"</li>
</ol>
