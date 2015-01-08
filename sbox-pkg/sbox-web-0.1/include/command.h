typedef int (*cmd_handler)(char *args[]);

typedef struct {
    char name[8];
    cmd_handler handler;
    int arg_count;
} command;

command *get_command(char *cmd_name);
