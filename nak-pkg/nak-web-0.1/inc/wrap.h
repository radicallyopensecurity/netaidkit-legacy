#ifndef WRAP_H
#define WRAP_H

char *do_command(char *script, char *args[]);
char **build_argv(char *script, char *args[]);
void free_argv(char **argv);

#endif
