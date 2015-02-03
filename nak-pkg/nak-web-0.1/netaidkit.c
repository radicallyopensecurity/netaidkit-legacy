#include <stdio.h>
#include <stdlib.h>
#include <command.h>

int main(int argc, char **argv)
{
    command *cmd;
    char **args;
    int cmd_status;
 
    /* We need a minimal an argument */   
    if (argc < 2) {
        fprintf(stderr, "Usage: %s COMMAND [ARGS]...\n", argv[0]);
        exit(-1);
    }
 
    /* Get the first command and check if this command exists */   
    cmd = get_command(argv[1]);
    if (cmd == NULL) {
        fprintf(stderr, "Unrecognized command.\n");
        exit(-1);
    }
    
    /* The get_command() returns an object where arg_count is set.
     * if we have not an exact match between expected arguments 
     * and actual aguments, we exit this application */
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
