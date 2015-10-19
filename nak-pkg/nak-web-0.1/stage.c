#include <stdio.h>
#include "wrap.h"
#include "stage.h"

char *get_stage(char **args) {
    char *response = NULL;

    response = do_command("/nak/scripts/get_stage.sh", args);

    return response;
}

char *set_stage(char **args) {
    char *response = NULL;

    response = do_command("/nak/scripts/set_stage.sh", args);

    return response;
}

char *toggle_tor(char **args) {
    char *response = NULL;

    response = do_command("/nak/scripts/toggle_tor.sh", args);

    return response;
}

char *toggle_vpn(char **args) {
    char *response = NULL;

    response = do_command("/nak/scripts/toggle_vpn.sh", args);

    return response;
}
