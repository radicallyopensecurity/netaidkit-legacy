#include <wifi.h>
#include <stage.h>
#include <inet.h>
#include <update.h>
#include <stdio.h>
#include <stdlib.h>
#include <command.h>

#define N_COMMANDS  (sizeof(commands) / sizeof(commands[0]))

static command commands[] = {
    { "wifiscan", scan_wifi, 0 },
    { "apconfig", ap_config, 2 },
    { "getapnam", get_ap_name, 0 },
    { "wificonn", connect_wifi, 2 },
    { "goonline", go_online, 0 },
    { "inetstat", inet_stat, 0 },
    { "nrouting", toggle_routing, 1},
    { "wlaninfo", wlan_info, 1},
    { "setstage", set_stage, 1 },
    { "getstage", get_stage, 0 },
    { "stagetor", toggle_tor, 1 },
    { "stagevpn", toggle_vpn, 1 },
    { "doupdate", do_update, 1},
    { "broadcst", toggle_broadcast, 1},
    { "isportal", detect_portal, 0}
};

command *get_command(char *cmd_name) {
    command *cmd = NULL;

    int i;
    for (i = 0; i < N_COMMANDS; i++) {
        if ((strncmp(cmd_name, commands[i].name, 8)) == 0) {
            cmd = &commands[i];
            break;
        }
    }

    return cmd;
}
