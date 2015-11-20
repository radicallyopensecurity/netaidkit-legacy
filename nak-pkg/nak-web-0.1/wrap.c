#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <string.h>
#include <sys/wait.h>
#include <json-c/json.h>
#include "wrap.h"
#include "command.h"
#include "message.h"

#define PIPE_READ       0
#define PIPE_WRITE      1

static char *json_wrap(const char *result) {
	char *json;
	json_object *jobj = json_object_new_object();
	json_object *jstring = json_object_new_string(result);

	json_object_object_add(jobj, "result", jstring);
	json = strdup(json_object_to_json_string(jobj));
	json_object_put(jobj);
	return json;
}

/* Returns NULL if the command failed.
 * args must end with a NULL pointer.
 */
char *do_command(char *script, char *args[]) {
    pid_t pid;
    int pipe_fd[2];
    char response[MAX_MSG_LEN + 1];

    memset(response, 0, MAX_MSG_LEN + 1);

    if (pipe(pipe_fd) == -1) {
        p_error("pipe()", "Could not create pipe.");
        return NULL;
    }

    pid = fork();
    if (pid < 0) {
        return NULL;
    } else if (pid == 0) { /* child */
        close(pipe_fd[PIPE_READ]);
        dup2(pipe_fd[PIPE_WRITE], 1);
        dup2(pipe_fd[PIPE_WRITE], 2);

        char **argv = build_argv(script, args);
        execve(argv[0], argv, NULL);

        free_argv(argv);
        p_error("execve()", "Could not execute command.");
        exit(-1);
    } else { /* parent */
        int n = 0;
        waitpid(pid, NULL, WUNTRACED);

        close(pipe_fd[PIPE_WRITE]);
        if ((n = read(pipe_fd[PIPE_READ], response, MAX_MSG_LEN) < 0)) {
            p_error("read()", "Could not read from pipe.");
            return NULL;
        }
        response[MAX_MSG_LEN] = 0;

        close(pipe_fd[PIPE_READ]);
    }

    return json_wrap(response);
}

/* create {"/bin/sh", "script", args[0], ..., args[n], NULL} on heap */
char **build_argv(char *script, char *args[]) {
    int i, n_args = 0;
    char **argv = NULL;

    for (i = 0; args[i] != NULL; i++)
        n_args++;

    argv = malloc((n_args + 3) * sizeof(char *));
    argv[0] = strdup("/bin/sh");
    argv[1] = strdup(script);

    for (i = 0; args[i] != NULL; i++)
        argv[2+i] = strdup(args[i]);

    argv[2+i] = NULL;
    return argv;
}

void free_argv(char **argv) {
    int i;

    for (i = 0; argv[i] != NULL; i++)
        free(argv[i]);

    free(argv);
}
