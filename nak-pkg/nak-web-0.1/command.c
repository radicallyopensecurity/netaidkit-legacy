#include <stdio.h>
#include <stdlib.h>
#include <command.h>

#define N_COMMANDS  sizeof(commands) / sizeof(commands[0])

int scan_wifi(char *args[]);

static command commands[] = {
    { "wifiscan", scan_wifi, 0 },
    { "apconfig", NULL, 0 },
    { "wificonn", NULL, 0 },
    { "setstage", NULL, 0 },
    { "getstage", NULL, 0 },
    { "stagetor", NULL, 0 },
    { "stagevpn", NULL, 0 }
};



command *get_command(char *cmd_name)
{
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
