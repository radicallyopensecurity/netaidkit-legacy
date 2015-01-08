#include <stdio.h>
#include <stdlib.h>
#include <command.h>

int main(int argc, char **argv)
{
    command *cmd;
    char **args;
    int cmd_status;
    
    if (argc < 2) {
        fprintf(stderr, "Usage: %s COMMAND [ARGS]...\n", argv[0]);
        exit(-1);
    }
    
    cmd = get_command(argv[1]);
    if (cmd == NULL) {
        fprintf(stderr, "Unrecognized command.\n");
        exit(-1);
    }
    
    if ((argc - 2) != cmd->arg_count) {
        fprintf(stderr, "Invalid number of argument(s).\n");
        exit(-1);
    }
    
    if (cmd->handler != NULL) {
        args = argv + 2;
        cmd_status = cmd->handler(args);
        if (cmd_status < 0) {
            fprintf(stderr, "An error has occurred.\n");
            exit(cmd_status);
        }
    }
}
