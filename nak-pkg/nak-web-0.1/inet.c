#include <stdio.h>
#include "wrap.h"
#include "inet.h"

char *go_online(char **args) {
    char *response = NULL;

    response = do_command("/nak/scripts/go_online.sh", args);

    return response;
}

char *inet_stat(char **args) {
    char *response = NULL;

    response = do_command("/nak/scripts/get_inetstat.sh", args);

    return response;
}

char *toggle_routing(char **args) {
    char *response = NULL;

    response = do_command("/nak/scripts/toggle_routing.sh", args);

    return response;
}

char *detect_portal(char **args) {
    char *response = NULL;

    response = do_command("/nak/scripts/detect_portal.sh", args);

    return response;
}
