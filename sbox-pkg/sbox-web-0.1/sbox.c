#include <stdio.h>
#include <stdlib.h>
#include <sys/types.h>
#include <unistd.h>

/* This is the _very_ dirty temporary wrapper solution.
 * The other files in this package contain the future solution, 
 * which should use libiw for wireless functionality, 
 * and the OpenWRT UCI bindings for configuration functionality. */

void print_iwinfo()
{
    uid_t uid = geteuid();
    setreuid(uid, uid);
        
    system("/usr/bin/iwinfo wlan0 scan");
}

void connect_wifi(char *ssid, char *key)
{
    uid_t uid = geteuid();
    setreuid(uid, uid); 
    
    char *dirty[] = {"/bin/sh", "/sbox/scripts/setup_wan.sh", ssid, key, NULL};
    execve(dirty[0], dirty, NULL);
}

void ap_config(char *ssid, char *key)
{
    uid_t uid = geteuid();
    setreuid(uid, uid); 
    
    char *dirty[] = {"/bin/sh", "/sbox/scripts/setup_ap.sh", ssid, key, NULL};
    execve(dirty[0], dirty, NULL);
}

void set_stage(char *stage)
{
    uid_t uid = geteuid();
    setreuid(uid, uid); 
    
    char *dirty[] = {"/bin/sh", "/sbox/scripts/set_stage.sh", stage, NULL};
    execve(dirty[0], dirty, NULL);
}

void get_stage()
{
    uid_t uid = geteuid();
    setreuid(uid, uid); 

    char *dirty[] = {"/bin/sh", "/sbox/scripts/get_stage.sh", NULL};
    execve(dirty[0], dirty, NULL);
}

void toggle_tor(char *mode)
{
    uid_t uid = geteuid();
    setreuid(uid, uid); 

    char *dirty[] = {"/bin/sh", "/sbox/scripts/toggle_tor.sh", mode, NULL};
    execve(dirty[0], dirty, NULL);
}

void toggle_vpn(char *mode)
{
    uid_t uid = geteuid();
    setreuid(uid, uid); 

    char *dirty[] = {"/bin/sh", "/sbox/scripts/toggle_vpn.sh", mode, NULL};
    execve(dirty[0], dirty, NULL);
}

void wlan_info(char *iface)
{
    uid_t uid = geteuid();
    setreuid(uid, uid); 
    
    char *dirty[] = {"/usr/bin/iwinfo", iface, "info", NULL};
    execve(dirty[0], dirty, NULL);
}

int main(int argc, char *argv[])
{
    char *action;

    if (argc < 2) {
        fprintf(stderr, "No action specified.\n");
        exit(-1);
    }
    
    action = argv[1];
    if (!strncmp("wifiscan", action, 8)) {
        print_iwinfo();
    } else if (!strncmp("apconfig", action, 8)) {
        ap_config(argv[2], argv[3]);
    } else if (!strncmp("wificonn", action, 8)) {
        connect_wifi(argv[2], argv[3]);
    } else if (!strncmp("setstage", action, 8)) {
        set_stage(argv[2]);
    } else if (!strncmp("getstage", action, 8)) {
        get_stage();
    } else if (!strncmp("stagetor", action, 8)) {
        toggle_tor(argv[2]);
    } else if (!strncmp("wlaninfo", action, 8)) {
        wlan_info(argv[2]);
    } else if (!strncmp("stagevpn", action, 8)) {
        toggle_vpn(argv[2]);
    } else {
        fprintf(stderr, "Specified action does not exist.\n");
        exit(-1);
    }
}
