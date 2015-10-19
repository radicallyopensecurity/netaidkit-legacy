#include <string.h>
#include <message.h>

message *parse_message(msg_type type, char *data) {
    message *msg = malloc(sizeof(message));
    char cmd_name[CMD_NAME_LEN + 1];
    msg->type = type;

    switch (msg->type) {
        case MSG_TYPE_UNKNOWN:
            break;
        case MSG_TYPE_COMMAND:
            strncpy(cmd_name, data, CMD_NAME_LEN);
            cmd_name[CMD_NAME_LEN] = 0x00;

            if ((msg->cmd = get_command(cmd_name)) == NULL) {
                free(msg);
                msg = NULL;
            }

            //set msg->args MUST END WITH NULL POINTER!!!!
            if ((msg->args = parse_args(data)) == NULL) {
                free(msg);
                msg = NULL;
            }

            break;
        case MSG_TYPE_REPLY:
            break;
    }

    return msg;
}

/* Will always at least return a pointer to {NULL} */
char **parse_args(char *data) {
    char **args = malloc(sizeof(char *));
    char *line;
    int n_args = 0;

    strsep(&data, "\r\n");
    while ((line = strsep(&data, "\r\n")) != NULL) {
        if (strlen(line) < 1)
            continue;
        args = realloc(args, sizeof(char *) * (++n_args + 1));
        args[n_args - 1] = strdup(line);
    }

    args[n_args] = NULL;
    return args;
}

void free_message(message *msg) {
    free(msg);
}
