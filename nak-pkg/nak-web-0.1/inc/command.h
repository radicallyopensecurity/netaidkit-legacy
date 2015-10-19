#ifndef COMMAND_H
#define COMMAND_H

/* Length of the name of a command is static. */
#define CMD_NAME_LEN     8

typedef char* (*cmd_handler)(char **args);

typedef struct {
    char name[CMD_NAME_LEN];
    cmd_handler handler;
    int arg_count;
} command;

command *get_command(char *cmd_name);

#endif
