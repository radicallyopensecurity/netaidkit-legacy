#ifndef MESSAGE_H
#define MESSAGE_H

#include <command.h>
#include <stdlib.h>

/* Max size of command sent over domain socket. */
#define MAX_MSG_LEN     262144

/* Max number of arguments passed along with a command. */
#define MAX_MSG_ARG     8

typedef enum {
    MSG_TYPE_UNKNOWN,
    MSG_TYPE_COMMAND,
    MSG_TYPE_REPLY
} msg_type;

typedef enum {
    MSG_STATUS_UNKNOWN,
    MSG_STATUS_SUCCESS,
    MSG_STATUS_ERROR
} msg_status;

typedef struct {
    char payload[MAX_MSG_LEN + 1];
    char **args; /* must end with NULL */
    msg_type type;
    msg_status status;
    command *cmd;
} message;

message *parse_message(msg_type type, char *data);
char **parse_args(char *data);

#endif
